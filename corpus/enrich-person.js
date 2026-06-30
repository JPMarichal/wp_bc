const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const ROOT = path.resolve(__dirname, '..');
const AUTHORS_PATH = path.join(ROOT, 'wp-content/plugins/bc-quote-block/data/authors.json');
const CORPUS_DIR = path.join(__dirname, 'personajes');
const CN_DIR = path.join(__dirname, 'church-news');
const URLS_FILE = path.join(__dirname, 'temp/cn-all-urls.txt');
const INDEX_PATH = path.join(__dirname, 'index.json');
const TMP_FILE = path.join(__dirname, '_enrich_temp.json');

const LOG_PATH = path.join(__dirname, 'enrich-person.log');
const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

function fetchJSON(url) {
  return new Promise((resolve, reject) => {
    const cmd = `curl --ssl-no-revoke -sL --connect-timeout 10 -m 15 -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" ${JSON.stringify(url)} -o ${JSON.stringify(TMP_FILE)}`;
    exec(cmd, { timeout: 20000 }, err => {
      if (err) return reject(err);
      if (!fs.existsSync(TMP_FILE)) return reject(new Error('no file'));
      try {
        resolve(JSON.parse(fs.readFileSync(TMP_FILE, 'utf-8')));
      } catch (e) {
        const c = fs.readFileSync(TMP_FILE, 'utf-8').substring(0, 100);
        reject(new Error('JSON parse: ' + c));
      }
    });
  });
}

function fetchHTML(url) {
  return new Promise((resolve, reject) => {
    const cmd = `curl --ssl-no-revoke -sL --connect-timeout 10 -m 15 -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" ${JSON.stringify(url)} -o ${JSON.stringify(TMP_FILE)}`;
    exec(cmd, { timeout: 20000 }, err => {
      if (err) return reject(err);
      if (!fs.existsSync(TMP_FILE)) return reject(new Error('no file'));
      const html = fs.readFileSync(TMP_FILE, 'utf-8');
      if (html.length < 200) return reject(new Error('too small: ' + html.length));
      resolve(html);
    });
  });
}

function normalizeName(name) {
  return name
    .toLowerCase()
    .replace(/ /g, '-')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9.-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

// Name -> exact Wikipedia page title mapping for known cases
const KNOWN_WP_TITLES = {
  'John Smith (hijo de Hyrum)': 'John_Smith_(nephew_of_Joseph_Smith)',
  'Joseph Fielding Smith (Patriarca)': 'Joseph_Fielding_Smith_(patriarch)',
};

// ---- Source: Wikipedia REST API + Wikidata fallback ----
async function sourceWikipedia(name) {
  // Check known title mapping first
  if (KNOWN_WP_TITLES[name]) {
    try {
      const url = `https://en.wikipedia.org/api/rest_v1/page/summary/${KNOWN_WP_TITLES[name]}`;
      const data = await fetchJSON(url);
      if (!data.type?.includes('not_found') && data.status !== 404 && data.extract) {
        return await processWPSummary(data, data.wikibase_item);
      }
    } catch {}
  }

  const parts = name.split(/\s+/);
  const first = parts[0].replace(/\./g, '');
  const last = parts[parts.length - 1].replace(/\./g, '');
  const midInitial = parts.length >= 3 && parts[1].length <= 3 ? parts[1].replace(/\./g, '') : '';

  const variants = [
    name.replace(/\(.*?\)/g, '').trim(),
    name,
  ];
  if (midInitial && parts.length >= 3) {
    const mid = parts.slice(1, -1).filter(p => p.length > 3 || !p.includes('.')).join(' ');
    variants.push(first + ' ' + midInitial + '. ' + last);
    if (mid) variants.push(first + ' ' + mid + ' ' + last);
  }
  variants.push(first + ' ' + last);
  const unique = [...new Set(variants)];

  let bestData = null;
  let bestQid = null;

  // Verify a Wikipedia page is about an LDS figure
  function isLDSContent(data) {
    const text = ((data.description || '') + ' ' + (data.extract || '')).toLowerCase();
    const ldsKeywords = /latter[-\s]day\s+saint|mormon|\blds\b|church\s+of\s+jesus\s+christ|of\s+the\s+seventy|general\s+authorit|president\s+of\s+the\s+(church|quorum|young|relief|primary|sunday)|patriarch|mission\s+president/i;
    return ldsKeywords.test(text);
  }

  for (const variant of unique) {
    try {
      const apiTitle = variant.replace(/ /g, '_');
      const url = `https://en.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(apiTitle)}`;
      const data = await fetchJSON(url);
      if (data.type && (data.type.includes('not_found') || data.type === 'disambiguation')) continue;
      if (data.status === 404) continue;
      if (!data.extract && !data.description) continue;
      if (!isLDSContent(data)) continue;
      bestData = data;
      bestQid = data.wikibase_item || null;
      break;
    } catch { continue; }
  }

  // Fallback: Wikipedia search (try multiple query suffixes)
  if (!bestData) {
    const queries = [
      name + ' Latter Day Saints',
      name.replace(/\(.*?\)/g, '').trim() + ' LDS',
      name.replace(/\(.*?\)/g, '').trim() + ' Mormon',
      name.replace(/\(.*?\)/g, '').trim() + ' LDS Church',
      parts.length >= 3 ? first + ' ' + last : name,
    ];
    for (const q of [...new Set(queries)]) {
      if (bestData) break;
      try {
        const searchUrl = `https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=${encodeURIComponent(q)}&srlimit=10&format=json`;
        const searchData = await fetchJSON(searchUrl);
        const pages = searchData?.query?.search || [];
        for (const page of pages) {
          const title = page.title;
          if (title.toLowerCase().includes(first.toLowerCase()) && title.toLowerCase().includes(last.toLowerCase())) {
            const url = `https://en.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(title.replace(/ /g, '_'))}`;
            const data = await fetchJSON(url);
            if (data.type && (data.type.includes('not_found') || data.type === 'disambiguation')) continue;
            if (data.status === 404) continue;
            if (!data.extract && !data.description) continue;
            if (!isLDSContent(data)) continue;
            bestData = data;
            bestQid = data.wikibase_item || null;
            break;
          }
        }
      } catch {}
    }
  }

  if (!bestData) return null;

  return await processWPSummary(bestData, bestQid);
}

async function processWPSummary(data, qid) {
  const result = {};
  const yearFromStr = s => { const m = s.match(/(\d{4})\s*[-–]\s*(\d{4})/); if (m) { result.birthYear = parseInt(m[1]); result.deathYear = parseInt(m[2]); } };
  if (data.description) yearFromStr(data.description);
  if (!result.birthYear && data.extract) yearFromStr(data.extract);
  if (data.description) result.description_en = data.description;
  else if (data.extract) result.description_en = data.extract.replace(/<[^>]+>/g, '').substring(0, 300).trim();
  if (data.thumbnail?.source) result.image = data.thumbnail.source;

  if (!result.birthYear && qid) {
    const tid = '_wd_' + qid + '.json';
    try {
      const wdUrl = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${qid}&props=claims&languages=en&format=json`;
      const cmd = `curl --ssl-no-revoke -sL --connect-timeout 10 -m 15 -H "User-Agent: Mozilla/5.0" ${JSON.stringify(wdUrl)} -o ${JSON.stringify(tid)}`;
      await new Promise((resolve, reject) => {
        require('child_process').exec(cmd, { timeout: 20000 }, err => { if (err) reject(err); else resolve(); });
      });
      const wd = JSON.parse(fs.readFileSync(tid, 'utf-8'));
      const entity = wd.entities?.[qid];
      const claims = entity?.claims || {};
      const getVal = prop => { const c = claims[prop]?.[0]?.mainsnak; if (!c || c.snaktype === 'novalue') return null; return c.datavalue?.value; };
      const getYear = prop => { const v = getVal(prop); if (!v?.time) return null; const m = v.time.match(/^[+-]?(\d{4})/); return m ? parseInt(m[1]) : null; };
      const by = getYear('P569'); if (by) result.birthYear = by;
      const dy = getYear('P570'); if (dy) result.deathYear = dy;
      if (!result.image) { const img = getVal('P18'); if (img) result.image = `https://commons.wikimedia.org/wiki/Special:FilePath/${encodeURIComponent(img.replace(/ /g, '_'))}`; }
      try { fs.unlinkSync(tid); } catch {}
    } catch {}
  }

  return Object.keys(result).length ? result : null;
}

// ---- Source: Wikidata Search + Fetch ----
async function sourceWikidata(name) {
  const variants = [name];
  // Without parenthetical disambiguation
  const cleanName = name.replace(/\s*\(.*?\)\s*/g, '').trim();
  if (cleanName !== name) variants.unshift(cleanName);
  const stripped = name.replace(/^([A-Z][a-z]+)\s+[A-Z]\.\s+/, '$1 ');
  if (stripped !== name) variants.push(stripped);
  const noSuffix = name.replace(/\s+(Jr|Sr)\.?$/i, '');
  if (noSuffix !== name) variants.push(noSuffix);

  let qid = null;
  for (const v of variants) {
    if (qid) break;
    try {
      const url = `https://www.wikidata.org/w/api.php?action=wbsearchentities&search=${encodeURIComponent(v)}&language=en&limit=5&format=json`;
      const result = await fetchJSON(url);
      const hits = result.search || [];
      for (const hit of hits) {
        const hitLabel = (hit.label || '').toLowerCase();
        const searchName = name.toLowerCase();
        if (hitLabel === searchName || hitLabel.includes(searchName) || searchName.includes(hitLabel)) {
          qid = hit.id;
          break;
        }
      }
    } catch {}
    await sleep(150);
  }

  if (!qid) return null;

  try {
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${qid}&props=labels|descriptions|claims&languages=en&format=json`;
    const data = await fetchJSON(url);
    const entity = data.entities?.[qid];
    if (!entity || entity.missing === '') return null;
    const claims = entity.claims || {};

    function getVal(prop) {
      const c = claims[prop]?.[0]?.mainsnak;
      if (!c || c.snaktype === 'novalue') return null;
      return c.datavalue?.value;
    }
    function getYear(prop) {
      const v = getVal(prop);
      if (!v?.time) return null;
      const m = v.time.match(/^[+-]?(\d{4})/);
      return m ? parseInt(m[1]) : null;
    }

    const result = {};
    const by = getYear('P569');
    if (by) result.birthYear = by;
    const dy = getYear('P570');
    if (dy) result.deathYear = dy;
    const desc = entity.descriptions?.en?.value;
    if (desc) result.description_en = desc;
    const img = getVal('P18');
    if (img) result.image = `https://commons.wikimedia.org/wiki/Special:FilePath/${encodeURIComponent(img.replace(/ /g, '_'))}`;
    return Object.keys(result).length ? result : null;
  } catch { return null; }
}

// ---- Source: Church News sitemap matching + cached HTML ----
async function sourceChurchNews(name) {
  if (!fs.existsSync(URLS_FILE)) return null;
  const urls = fs.readFileSync(URLS_FILE, 'utf8').split('\n').filter(Boolean);
  const parts = name.split(' ');
  const first = parts[0].replace('.', '').toLowerCase();
  const last = parts[parts.length - 1].replace('.', '').toLowerCase();

  // Generate name variants: try without middle initial too
  const nameVariants = [name];
  if (parts.length >= 3) {
    const mid = parts.slice(1, -1).filter(p => p.length > 3 || !p.includes('.'));
    if (mid.length > 0) nameVariants.push([first, ...mid, parts[parts.length-1]].join(' '));
    // Without any middle names/initials
    nameVariants.push(first + ' ' + parts[parts.length-1]);
  }

  const norm = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  const isInitial = w => w.length === 1 || (w.length === 2 && w.endsWith('.'));
  const keywordPat = /biography|obituary|dies|passes|death|elder|sister|funeral|burial/i;

  const scored = [];
  for (const variant of nameVariants) {
    const vParts = variant.split(' ');
    const vFirst = vParts[0].replace('.', '').toLowerCase();
    const vLast = vParts[vParts.length - 1].replace('.', '').toLowerCase();
    for (const u of urls) {
      let score = 0;
      const p = u.toLowerCase();
      const pNorm = norm(p);
      const firstNorm = norm(vFirst);
      const lastNorm = norm(vLast);
      const firstMatch = isInitial(vFirst) ? false : (p.includes(vFirst) || pNorm.includes(firstNorm));
      const lastMatch = p.includes(vLast) || pNorm.includes(lastNorm);
      if (firstMatch && lastMatch) score += 10;
      else if (lastMatch) score += 3;
      if (p.includes(vFirst + '-' + vLast) || pNorm.includes(firstNorm + '-' + lastNorm)) score += 5;
      if (keywordPat.test(p)) score += 20;
      if (p.includes('/elder-' + vFirst) || p.includes('/elder-' + firstNorm) || p.includes('/sister-' + vFirst) || p.includes('/sister-' + firstNorm)) score += 5;
      if (score > 0) scored.push({ url: u, score, variant });
    }
  }

  // Deduplicate: keep highest score per URL
  const bestPerUrl = {};
  for (const s of scored) {
    if (!bestPerUrl[s.url] || s.score > bestPerUrl[s.url].score) bestPerUrl[s.url] = s;
  }
  const unique = Object.values(bestPerUrl);
  unique.sort((a, b) => b.score - a.score);

  if (unique.length === 0 || unique[0].score < 20) return null;

  const targetUrl = unique[0].url;
  const safeName = normalizeName(name);
  const filePath = path.join(CN_DIR, safeName + '.html');

  let html;
  if (fs.existsSync(filePath) && fs.statSync(filePath).size > 1000) {
    html = fs.readFileSync(filePath, 'utf8');
  } else {
    try {
      html = await fetchHTML(targetUrl);
      fs.writeFileSync(filePath, html);
    } catch { return null; }
    await sleep(2000);
  }

  // Clean text for regex matching
  const text = html.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&[^;]+;/g, ' ')
    .replace(/\s+/g, ' ');

  const result = {};

  // Birth year: look for "born ... <Month> <Day>, <YEAR>" anywhere in text
  const birthMonthPat = /born\D{0,60}(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i;
  const birthM = text.match(birthMonthPat);
  if (birthM) { const y = parseInt(birthM[2]); if (y >= 1800 && y <= 2010) result.birthYear = y; }
  if (!result.birthYear) {
    const birthYearPat = /\b(\d{4})\b.{0,40}born\b|\bborn\b.{0,40}\b(\d{4})\b/i;
    const m = text.match(birthYearPat);
    if (m) { const y = parseInt(m[1] || m[2]); if (y >= 1800 && y <= 2010) result.birthYear = y; }
  }

  // Death year
  const deathMonthPat = /(?:died|death|passed\s+away)\D{0,80}(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i;
  const deathM = text.match(deathMonthPat);
  if (deathM) { const y = parseInt(deathM[2]); if (y >= 1800 && y <= 2030) result.deathYear = y; }
  if (!result.deathYear) {
    const deathYearPat = /(?:died|death)\D{0,30}(\d{4})/i;
    const m = text.match(deathYearPat);
    if (m) { const y = parseInt(m[1]); if (y >= 1800 && y <= 2030) result.deathYear = y; }
  }

  // Age (for birthYear calculation fallback)
  const agePat = /\b(\d{2,3})\b\s*(?:years?\s+old|years?\s+of\s+age)/i;
  const ageM = text.match(agePat);
  let age = ageM ? parseInt(ageM[1]) : null;
  if (!age || age < 30 || age > 115) {
    const agePat2 = /(?:died|dies|passed\s+away)\D{0,30}at\s+age\s+(\d+)/i;
    const m2 = text.match(agePat2);
    if (m2) { const a = parseInt(m2[1]); if (a >= 30 && a <= 115) age = a; }
  }
  if (!result.birthYear && result.deathYear && age) {
    result.birthYear = result.deathYear - age;
  }

  // Description
  const ogDesc = html.match(/<meta\s+(?:name|property)="description"\s+content="([^"]+)"/i);
  if (ogDesc) result.description_en = ogDesc[1].replace(/&[^;]+;/g, '').replace(/\s+/g, ' ').trim();

  // Image
  const ogImg = html.match(/<meta\s+property="og:image"\s+content="([^"]+)"/i);
  if (ogImg) result.image = ogImg[1];

  // Verify the page actually mentions this person (prevents false positives)
  const textLower = text.toLowerCase();
  const normText = norm(textLower);
  const partsVerif = name.split(' ');
  const firstVerif = partsVerif[0].replace(/\./g, '').toLowerCase();
  const lastVerif = partsVerif[partsVerif.length - 1].replace(/\./g, '').toLowerCase();
  const firstNorm = norm(firstVerif);
  const lastNorm = norm(lastVerif);
  const isInitialVerif = firstVerif.length <= 2;

  // Check for name as word boundaries (prevents "ruth" matching "truth")
  const wordCheck = (txt, word) => { try { return new RegExp('\\b' + word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'i').test(txt); } catch { return txt.includes(word); } };
  const firstNameMatch = wordCheck(textLower, firstVerif) || wordCheck(normText, firstNorm);
  const lastNameMatch = wordCheck(textLower, lastVerif) || wordCheck(normText, lastNorm);

  // Also check for full phrase "First Last" together
  const fullNamePhrase = partsVerif.filter(p => p.length > 2).map(p => p.replace(/\./g, '')).join(' ').toLowerCase();
  const fullNameMatch = fullNamePhrase.length > 3 && (textLower.includes(fullNamePhrase) || normText.includes(norm(fullNamePhrase)));

  const nameInText = fullNameMatch || (firstNameMatch && lastNameMatch);
  // For initial-only first names, use the unique middle name part
  const altCheck = isInitialVerif && partsVerif.length >= 3 && !firstNameMatch
    ? wordCheck(textLower, partsVerif[1].replace(/\./g, '').toLowerCase())
    : true;
  if (!nameInText || !altCheck) return null;

  return Object.keys(result).length ? result : null;
}

// ---- Source: LDS Church website (learn/chd) ----
async function sourceCHD(name) {
  const slug = normalizeName(name).replace(/\./g, '');
  // Check local cache first
  const cachePath = path.join(CORPUS_DIR, slug, 'chd.html');
  if (fs.existsSync(cachePath)) {
    const html = fs.readFileSync(cachePath, 'utf-8');
    const result = {};
    const text = html.replace(/<[^>]+>/g, ' ').replace(/&[^;]+;/g, ' ').replace(/\s+/g, ' ').trim();
    const yearM = text.match(/(\d{4})\s*[-–]\s*(\d{4})/);
    if (yearM) { result.birthYear = parseInt(yearM[1]); result.deathYear = parseInt(yearM[2]); }
    const desc = html.match(/<meta\s+name="description"\s+content="([^"]+)"/i);
    if (desc) result.description_en = desc[1];
    return Object.keys(result).length ? result : null;
  }
  const url = `https://www.churchofjesuschrist.org/learn/${slug}?lang=eng`;
  try {
    const html = await fetchHTML(url);
    if (html.includes('<title>Page not found') || html.includes('statusCode\":404')) return null;
    // Cache for future use
    const cacheDir = path.join(CORPUS_DIR, slug);
    if (!fs.existsSync(cacheDir)) fs.mkdirSync(cacheDir, { recursive: true });
    fs.writeFileSync(path.join(cacheDir, 'chd.html'), html);
    const result = {};
    const nextData = html.match(/__NEXT_DATA__[^>]*>\s*(\{.*?\})\s*<\/script>/);
    if (nextData) {
      try {
        const d = JSON.parse(nextData[1]);
        const bio = d?.props?.pageProps?.page?.content?.biography;
        if (bio) {
          const yearM = bio.match(/(\d{4})\s*[-–]\s*(\d{4})/);
          if (yearM) { result.birthYear = parseInt(yearM[1]); result.deathYear = parseInt(yearM[2]); }
          result.description_en = bio.replace(/<[^>]+>/g, '').substring(0, 300).trim();
        }
      } catch {}
    }
    const desc = html.match(/<meta\s+name="description"\s+content="([^"]+)"/i);
    if (desc && !result.description_en) result.description_en = desc[1];
    return Object.keys(result).length ? result : null;
  } catch { return null; }
}

// ---- Source: Encyclopedia of Mormonism ----
async function sourceEOM(name) {
  const searchName = name.replace(/\(.*?\)/g, '').trim();
  const urlName = searchName.replace(/ /g, '_').replace(/\./g, '');
  const url = `https://eom.byu.edu/index.php?title=${encodeURIComponent(urlName)}`;
  try {
    const html = await fetchHTML(url);
    if (html.includes('There is currently no text in this page') || html.length < 1000) return null;
    const result = {};
    const text = html.replace(/<[^>]+>/g, ' ').replace(/&[^;]+;/g, ' ').replace(/\s+/g, ' ').trim();
    const yearM = text.match(/(\d{4})\s*[-–]\s*(\d{4})/);
    if (yearM) { result.birthYear = parseInt(yearM[1]); result.deathYear = parseInt(yearM[2]); }
    const firstP = html.match(/<p>([\s\S]*?)<\/p>/);
    if (firstP) result.description_en = firstP[1].replace(/<[^>]+>/g, '').substring(0, 300).trim();
    return Object.keys(result).length ? result : null;
  } catch { return null; }
}

// ---- Main enrichment pipeline ----
async function enrichPerson(name) {
  log(`Enriching: "${name}"`);

  const sources = [
    { name: 'Wikipedia', fn: sourceWikipedia },
    { name: 'Wikidata', fn: sourceWikidata },
    { name: 'ChurchNews', fn: sourceChurchNews },
    { name: 'CHD', fn: sourceCHD },
    { name: 'EOM', fn: sourceEOM },
  ];

  for (const source of sources) {
    try {
      const result = await source.fn(name);
      if (result && (result.birthYear || result.deathYear || result.image || result.description_en)) {
        log(`  ${source.name}: birth=${result.birthYear} death=${result.deathYear} img=${!!result.image} desc=${!!result.description_en}`);
        return { source: source.name, data: result };
      }
    } catch (err) {
      log(`  ${source.name}: error ${err.message}`);
    }
  }

  log(`  No data found from any source`);
  return null;
}

// ---- Merge into authors.json ----
function mergeIntoAuthors(name, enriched) {
  const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
  const author = authors.find(a => a.name === name);
  if (!author) {
    log(`  WARN: "${name}" not found in authors.json`);
    return false;
  }

  let changed = false;
  const data = enriched.data;

  if (!author.birthYear && data.birthYear) { author.birthYear = data.birthYear; changed = true; }
  if (!author.deathYear && data.deathYear) { author.deathYear = data.deathYear; changed = true; }
  if (!author.image && data.image) { author.image = data.image; changed = true; }
  if (!author.description_en && data.description_en) {
    author.description_en = data.description_en.replace(/&[^;]+;/g, '').replace(/\s+/g, ' ').trim();
    changed = true;
  }

  if (changed) {
    fs.writeFileSync(AUTHORS_PATH, JSON.stringify(authors, null, 2));
    log(`  MERGED into authors.json`);
  }

  return changed;
}

// ---- CLI entry ----
async function main() {
  const args = process.argv.slice(2);
  const batchMode = args.includes('--batch');
  const singleName = args.filter(a => !a.startsWith('--')).join(' ').trim();

  if (singleName) {
    const enriched = await enrichPerson(singleName);
    if (enriched) {
      console.log(JSON.stringify(enriched, null, 2));
      mergeIntoAuthors(singleName, enriched);
    }
  } else if (batchMode) {
    const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
    const targets = authors.filter(a => !a.birthYear && !a.deathYear && !a.image && !a.description_en);
    log(`Batch mode: ${targets.length} authors to enrich`);

    let enriched = 0;
    for (let i = 0; i < targets.length; i++) {
      const person = targets[i];
      log(`[${i + 1}/${targets.length}] ${person.name}`);
      const result = await enrichPerson(person.name);
      if (result) {
        const merged = mergeIntoAuthors(person.name, result);
        if (merged) enriched++;
      }
      // Rate limiting between persons
    await sleep(500);
  }

    // Final stats
    const final = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
    const withBirth = final.filter(a => a.birthYear).length;
    const withDeath = final.filter(a => a.deathYear).length;
    const withImage = final.filter(a => a.image).length;
    const withDesc = final.filter(a => a.description_en).length;
    const remain = final.filter(a => !a.birthYear && !a.deathYear && !a.image && !a.description_en).length;

    log(`\n=== BATCH COMPLETE ===`);
    log(`Enriched: ${enriched}/${targets.length}`);
    log(`birthYear: ${withBirth}/762`);
    log(`deathYear: ${withDeath}/762`);
    log(`image: ${withImage}/762`);
    log(`description_en: ${withDesc}/762`);
    log(`remaining empty: ${remain}`);
  } else {
    console.log('Usage:');
    console.log('  node enrich-person.js "John Smith"    # Enrich a single person');
    console.log('  node enrich-person.js --batch           # Enrich all remaining empty authors');
    console.log('');
    console.log('Sources: Wikipedia → Wikidata → ChurchNews → CHD → EOM');
    if (fs.existsSync(AUTHORS_PATH)) {
      const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
      const empty = authors.filter(a => !a.birthYear && !a.deathYear && !a.image && !a.description_en);
      console.log(`Currently ${empty.length}/${authors.length} authors need enrichment`);
    }
  }

  logStream.end();
}

main().catch(err => { console.error('FATAL:', err); logStream.end(); process.exit(1); });
