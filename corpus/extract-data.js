const fs = require('fs');
const path = require('path');

const WORKROOT = path.resolve(__dirname, '..');
const AUTHORS_FILE = path.join(WORKROOT, 'wp-content/plugins/bc-quote-block/data/authors.json');
const CORPUS_DIR = path.join(__dirname, 'personajes');
const INDEX_FILE = path.join(__dirname, 'index.json');
const OUTPUT_FILE = path.join(__dirname, 'authors-enriched.json');
const REPORT_FILE = path.join(__dirname, 'extraction-report.json');

const authors = JSON.parse(fs.readFileSync(AUTHORS_FILE, 'utf-8'));
const index = JSON.parse(fs.readFileSync(INDEX_FILE, 'utf-8'));

const nameToNorm = {};
for (const [norm, name] of Object.entries(index)) {
  nameToNorm[name] = norm;
}

function safeReadJSON(filePath) {
  try {
    if (!fs.existsSync(filePath)) return null;
    const raw = fs.readFileSync(filePath, 'utf-8').replace(/^\uFEFF/, '').trim();
    if (raw.length === 0) return null;
    const parsed = JSON.parse(raw);
    if (parsed && typeof parsed === 'object' && !Array.isArray(parsed) && Object.keys(parsed).length === 0) return null;
    return parsed;
  } catch { return null; }
}

function safeReadHTML(filePath) {
  try {
    if (!fs.existsSync(filePath)) return null;
    return fs.readFileSync(filePath, 'utf-8');
  } catch { return null; }
}

function wikidataImageUrl(filename) {
  if (!filename) return null;
  return `https://commons.wikimedia.org/wiki/Special:FilePath/${encodeURIComponent(filename.replace(/ /g, '_'))}`;
}

function extractWPDescription(wpSummary) {
  if (!wpSummary) return null;
  if (wpSummary.description) return wpSummary.description;
  if (wpSummary.extract) return wpSummary.extract.replace(/<[^>]+>/g, '').trim();
  return null;
}

function extractLDSMeta(html) {
  if (!html) return null;
  const m = html.match(/<meta[^>]*name="description"[^>]*content="([^"]+)"/);
  return m ? m[1].trim() : null;
}

function extractDeathYearFromWP(html) {
  if (!html) return null;
  // Try microformat first
  const dday = html.match(/<span[^>]*class="dday"[^>]*>(\d{4})-/);
  if (dday) return parseInt(dday[1]);
  // Try infobox "Died" / "Death" row for a 4-digit year
  const deathRow = html.match(/<th[^>]*class="infobox-label"[^>]*>(?:Died|Death|Died?)[^<]*<\/th>\s*<td[^>]*class="infobox-data"[^>]*>([\s\S]*?)<\/td>/i);
  if (deathRow) {
    const yearMatch = deathRow[1].match(/(\d{4})/);
    if (yearMatch) return parseInt(yearMatch[1]);
  }
  return null;
}

function extractImageFromWP(html) {
  if (!html) return null;
  // Look for infobox image
  const imgMatch = html.match(/<td[^>]*class="infobox-image"[^>]*>[\s\S]*?<img[^>]*src="([^"]+)"/);
  if (imgMatch) {
    let url = imgMatch[1];
    if (url.startsWith('//')) url = 'https:' + url;
    return url;
  }
  return null;
}

const stats = {
  total: authors.length,
  withBirthYear: 0,
  withDeathYear: 0,
  withBirthName: 0,
  withImage: 0,
  withEnDescription: 0,
  withOccupations: 0,
  withReligion: 0,
  missingDir: 0,
  errors: [],
};

const enriched = authors.map((author, i) => {
  const norm = nameToNorm[author.name];
  const dirPath = norm ? path.join(CORPUS_DIR, norm) : null;
  const result = { ...author };

  if (!dirPath || !fs.existsSync(dirPath)) {
    stats.missingDir++;
    return result;
  }

  const wpMeta = safeReadJSON(path.join(dirPath, 'wikipedia-meta.json'));
  const wpHTML = safeReadHTML(path.join(dirPath, 'wikipedia.html'));
  const wd = safeReadJSON(path.join(dirPath, 'wikidata.json'));
  const wpSum = safeReadJSON(path.join(dirPath, 'wp-summary.json'));
  const ldsHTML = safeReadHTML(path.join(dirPath, 'ldsorg.html'));
  const chdHTML = safeReadHTML(path.join(dirPath, 'chd.html'));

  // birthYear: wpMeta > wikidata > WP HTML
  let birthYear = null;
  if (wpMeta && wpMeta.birthYear) birthYear = wpMeta.birthYear;
  else if (wd && wd.birthDate) birthYear = wd.birthDate;
  if (birthYear) {
    result.birthYear = birthYear;
    stats.withBirthYear++;
  }

  // deathYear: wikidata > WP HTML infobox
  let deathYear = null;
  if (wd && wd.deathDate) deathYear = wd.deathDate;
  else if (wpHTML) deathYear = extractDeathYearFromWP(wpHTML);
  if (deathYear) {
    result.deathYear = deathYear;
    stats.withDeathYear++;
  }

  // birthName
  if (wd && wd.birthName) {
    result.birthName = wd.birthName;
    stats.withBirthName++;
  }

  // image: wp-summary thumbnail > wikidata > WP HTML infobox
  let image = null;
  if (wpSum && wpSum.thumbnail && wpSum.thumbnail.source) {
    image = wpSum.thumbnail.source;
    stats.withImage++;
  } else if (wd && wd.image) {
    image = wikidataImageUrl(wd.image);
    stats.withImage++;
  } else if (wpHTML) {
    image = extractImageFromWP(wpHTML);
    if (image) stats.withImage++;
  }
  if (image) result.image = image;

  // description_en
  let enDesc = null;
  enDesc = extractWPDescription(wpSum);
  if (!enDesc && wd && wd.description) enDesc = wd.description;
  if (!enDesc) enDesc = extractLDSMeta(ldsHTML);
  if (enDesc) {
    result.description_en = enDesc;
    stats.withEnDescription++;
  }

  // occupations
  if (wd && wd.occupations && wd.occupations.length > 0) {
    result.occupations = wd.occupations;
    stats.withOccupations++;
  }

  // religion
  if (wd && wd.religion) {
    result.religion = wd.religion;
    stats.withReligion++;
  }

  return result;
});

fs.writeFileSync(OUTPUT_FILE, JSON.stringify(enriched, null, 2));
fs.writeFileSync(REPORT_FILE, JSON.stringify(stats, null, 2));

console.log('=== Extraction Complete ===');
console.log('Total authors:', stats.total);
console.log('Missing dirs:', stats.missingDir);
console.log('With birthYear:', stats.withBirthYear);
console.log('With deathYear:', stats.withDeathYear);
console.log('With birthName:', stats.withBirthName);
console.log('With image:', stats.withImage);
console.log('With enDescription:', stats.withEnDescription);
console.log('With occupations:', stats.withOccupations);
console.log('With religion:', stats.withReligion);
console.log('Errors:', stats.errors.length);
console.log('\nOutput:', OUTPUT_FILE);
