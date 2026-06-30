const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');

const execPromise = util.promisify(exec);

const AUTHORS_PATH = '../wp-content/plugins/bc-quote-block/data/authors.json';
const CORPUS_DIR = 'personajes';
const INDEX_PATH = 'index.json';
const LOG_PATH = 'download.log';
const BATCH_SIZE = 10;
const MAX_RETRIES = 2;
const CURL_TIMEOUT = 20;
const PILOT_LIMIT = null; // null = all 762, number = limit for pilot

let logStream;

function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  if (logStream) logStream.write(line + '\n');
}

function normalizeName(name) {
  let s = name
    .toLowerCase()
    .replace(/ /g, '-')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9.-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
  return s.length > 100 ? s.substring(0, 100).replace(/-+$/, '') : s;
}

function wikiName(name) {
  return name.replace(/ /g, '_');
}

async function download(url, outputPath, label) {
  for (let attempt = 1; attempt <= MAX_RETRIES + 1; attempt++) {
    try {
      const cmd = `curl --ssl-no-revoke -s --connect-timeout ${CURL_TIMEOUT} ${JSON.stringify(url)} -o ${JSON.stringify(outputPath)}`;
      await execPromise(cmd, { timeout: (CURL_TIMEOUT + 5) * 1000 });
      if (!fs.existsSync(outputPath)) throw new Error('File not created');
      const stat = fs.statSync(outputPath);
      if (stat.size < 50) throw new Error('File too small: ' + stat.size + ' bytes');
      return stat.size;
    } catch (err) {
      if (attempt <= MAX_RETRIES) {
        log(`  Retry ${attempt}/${MAX_RETRIES} for ${label}: ${err.message}`);
        await new Promise(r => setTimeout(r, 2000 * attempt));
      } else {
        throw err;
      }
    }
  }
}

async function ensurePersonDir(person) {
  const dir = path.join(CORPUS_DIR, person.normalized);
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
  return dir;
}

// ---- Wikipedia ----

async function downloadWikipedia(person, dir) {
  const output = path.join(dir, 'wikipedia.html');
  if (fs.existsSync(output) && fs.statSync(output).size > 100) return 'cached';
  const url = `https://en.wikipedia.org/wiki/${wikiName(person.name)}`;
  try {
    const size = await download(url, output, person.name + ' (wp)');
    log(`  WP OK ${person.name} (${size} bytes)`);
    return 'ok';
  } catch (err) {
    log(`  WP FAIL ${person.name}: ${err.message}`);
    return 'fail';
  }
}

function parseWikipediaMeta(html) {
  const result = { fullName: null, birthYear: null, pageTitle: null, hasInfobox: false };

  // extract page title
  const titleMatch = html.match(/<title>([^<]+?)\s*[-–—]\s*Wikipedia/i);
  if (titleMatch) result.pageTitle = titleMatch[1].trim();

  // check for infobox
  if (!html.includes('class="infobox')) return result;
  result.hasInfobox = true;

  // Full name from <span class="fn"> or <div class="fn"> (supports multiline)
  let fnMatch = html.match(/<span\s+class="fn"[^>]*>([\s\S]*?)<\/span>/)
    || html.match(/<div\s+class="fn"[^>]*>([\s\S]*?)<\/div>/)
    || html.match(/<th[^>]*>Full name\s*<\/th>\s*<td[^>]*>\s*([^<]+?)\s*<\/td>/i);
  if (fnMatch) {
    result.fullName = fnMatch[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();
    if (result.fullName.length > 100 || result.fullName === result.pageTitle) result.fullName = null;
  }

  // Fallback: Wikipedia convention — first <b> in the article body is the full name
  if (!result.fullName) {
    const pMatch = html.match(/<p>.*?<b>([^<]+)<\/b>/);
    if (pMatch) {
      const fn = pMatch[1].replace(/\s+/g, ' ').trim();
      if (fn.length > 2 && fn.length < 100) result.fullName = fn;
    }
  }

  // Birth year from <span class="bday">
  const bdayMatch = html.match(/<span[^>]*class="bday"[^>]*>(\d{4})-/);
  if (bdayMatch) {
    result.birthYear = parseInt(bdayMatch[1]);
  } else {
    // Fallback: Born row
    const bornMatch = html.match(/<th[^>]*>\s*Born\s*<\/th>\s*<td[^>]*>(?:[^<]*<[^>]+>)*?(\d{4})/);
    if (bornMatch) result.birthYear = parseInt(bornMatch[1]);
  }

  return result;
}

// ---- CHD ----

function makeChdSlug(text) {
  return text
    .toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/['']/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

function generateChdCandidates(person, meta) {
  const candidates = [];

  // CHD slugs vary: sometimes abbreviated (page title), sometimes full legal name.
  // Generate many variants and accept the first valid one.

  function addSlug(name, year) {
    if (!name) return;
    const base = makeChdSlug(name);
    if (base.length < 2) return;
    if (year) {
      candidates.push(base + '-' + year);
      candidates.push(base + '-' + (year - 1));
      candidates.push(base + '-' + (year + 1));
    }
    candidates.push(base);
  }

  // 1. Page title (most common pattern)
  addSlug(meta.pageTitle, meta.birthYear);

  // 2. Full legal name (when different from page title)
  if (meta.fullName && meta.fullName !== meta.pageTitle) {
    addSlug(meta.fullName, meta.birthYear);
  }

  // 3. Original author name as given in authors.json
  addSlug(person.name, meta.birthYear);

  // 4. Normalized name
  addSlug(person.normalized, meta.birthYear);

  // 5. Try stripping first initial + period (e.g. "A. Theodore Tuttle" → "Theodore Tuttle")
  if (meta.pageTitle) {
    const stripped = meta.pageTitle.replace(/^[A-Z]\.\s+/, '');
    if (stripped !== meta.pageTitle) addSlug(stripped, meta.birthYear);
  }
  if (person.name) {
    const stripped = person.name.replace(/^[A-Z]\.\s+/, '');
    if (stripped !== person.name) addSlug(stripped, meta.birthYear);
  }

  // 6. Try just the last name + first initial (for people without infobox)
  if (meta.pageTitle) {
    const parts = meta.pageTitle.split(' ');
    if (parts.length >= 2) {
      const last = parts[parts.length - 1];
      const first = parts[0].replace(/\.$/, '');
      addSlug(first + '-' + last, meta.birthYear);
      if (parts.length >= 3) {
        const middle = parts.slice(1, -1).join('-').replace(/\./g, '');
        addSlug(first + '-' + middle + '-' + last, meta.birthYear);
      }
    }
  }

  return [...new Set(candidates)];
}

async function downloadCHD(person, dir, meta) {
  const output = path.join(dir, 'chd.html');
  if (fs.existsSync(output) && fs.statSync(output).size > 100) return 'cached';

  const candidates = generateChdCandidates(person, meta);

  for (const slug of candidates) {
    const url = `https://history.churchofjesuschrist.org/chd/individual/${slug}`;
    try {
      const size = await download(url, output, person.name + ' (chd:' + slug + ')');
      // Validate: CHD is a Next.js SPA. Check __NEXT_DATA__ for personSummary
      const html = fs.readFileSync(output, 'utf-8');
      const nextDataMatch = html.match(/__NEXT_DATA__[^>]*>\s*(\{.*?\})\s*<\/script>/);
      if (nextDataMatch) {
        try {
          const data = JSON.parse(nextDataMatch[1]);
          const summary = data?.props?.pageProps?.personSummary;
          if (summary && summary.statusCode !== 404 && summary.name) {
            log(`  CHD OK ${person.name} (${slug}, ${size} bytes)`);
            return 'ok';
          }
        } catch (e) {
          // JSON parse failed, fall through to unlink
        }
      }
      // False positive, remove and try next
      if (fs.existsSync(output)) fs.unlinkSync(output);
    } catch (err) {
      if (fs.existsSync(output)) fs.unlinkSync(output);
    }
  }

  log(`  CHD FAIL ${person.name}: no URL resolved`);
  return 'fail';
}

// ---- Person pipeline ----

async function processPerson(person) {
  const dir = await ensurePersonDir(person);

  // Phase 1: Download Wikipedia
  const wpResult = await downloadWikipedia(person, dir);

  // Phase 2: Parse Wikipedia metadata
  let meta = { fullName: null, birthYear: null, pageTitle: null, hasInfobox: false };
  const wpFile = path.join(dir, 'wikipedia.html');
  if (fs.existsSync(wpFile) && fs.statSync(wpFile).size > 100) {
    const html = fs.readFileSync(wpFile, 'utf-8');
    meta = parseWikipediaMeta(html);
    fs.writeFileSync(path.join(dir, 'wikipedia-meta.json'), JSON.stringify(meta, null, 2));
  }

  // Phase 3: Download CHD
  const chdResult = await downloadCHD(person, dir, meta);

  return { name: person.name, normalized: person.normalized, wpResult, chdResult, meta };
}

// ---- Main ----

async function run() {
  if (!fs.existsSync(CORPUS_DIR)) fs.mkdirSync(CORPUS_DIR, { recursive: true });
  logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });

  const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
  const limit = PILOT_LIMIT || authors.length;
  log(`Loaded ${authors.length} authors. Processing: ${limit}`);

  const people = authors.slice(0, limit).map(a => ({
    name: a.name,
    normalized: normalizeName(a.name)
  }));

  log(`Processing ${people.length} people...\n`);

  // Build/update index from existing files (resume support)
  const index = {};
  for (const p of people) index[p.normalized] = p.name;
  fs.writeFileSync(INDEX_PATH, JSON.stringify(index, null, 2));

  let completed = 0, wpOk = 0, chdOk = 0;
  const statsPath = 'stats.json';

  // Load previous stats if resuming
  let startIndex = 0;
  if (fs.existsSync(statsPath)) {
    try {
      const prev = JSON.parse(fs.readFileSync(statsPath, 'utf-8'));
      startIndex = prev.lastCompleted || 0;
      completed = prev.completed || 0;
      wpOk = prev.wpOk || 0;
      chdOk = prev.chdOk || 0;
      log(`Resuming from index ${startIndex} (${completed} already done)`);
    } catch (e) { /* ignore corrupt stats */ }
  }

  const totalBatches = Math.ceil(people.length / BATCH_SIZE);

  for (let i = startIndex; i < people.length; i += BATCH_SIZE) {
    const batchNum = Math.floor(i / BATCH_SIZE) + 1;
    const batch = people.slice(i, i + BATCH_SIZE);
    log(`\n--- Batch ${batchNum}/${totalBatches} (${batch.length} people, ${i}/${people.length}) ---`);

    const startTime = Date.now();
    const results = await Promise.allSettled(batch.map(p => processPerson(p)));
    const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);

    let batchWp = 0, batchChd = 0;
    for (const r of results) {
      completed++;
      if (r.status === 'fulfilled') {
        const v = r.value;
        if (v.wpResult !== 'fail') { wpOk++; batchWp++; }
        if (v.chdResult !== 'fail') { chdOk++; batchChd++; }
      } else {
        log(`  ERROR: ${r.reason?.message || 'unknown'}`);
      }
    }

    const remaining = people.length - i - batch.length;
    const eta = remaining > 0 ? ' ~' + ((elapsed / batch.length) * remaining / 60).toFixed(1) + 'min' : '';
    log(`Batch done in ${elapsed}s | WP: ${batchWp}/${batch.length} | CHD: ${batchChd}/${batch.length}${eta}`);
    log(`Total: ${completed}/${people.length} | WP: ${wpOk} | CHD: ${chdOk}`);

    // Save stats for resume
    fs.writeFileSync(statsPath, JSON.stringify({ lastCompleted: i + batch.length, completed, wpOk, chdOk, updated: new Date().toISOString() }));
  }

  log(`\n=== DONE ===`);
  log(`Total: ${completed} | WP: ${wpOk}/${people.length} | CHD: ${chdOk}/${people.length}`);

  if (logStream) logStream.end();
}

run().catch(err => {
  console.error('FATAL:', err);
  process.exit(1);
});
