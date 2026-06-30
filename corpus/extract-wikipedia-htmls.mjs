import fs from 'fs';
import path from 'path';

const BASE = 'C:/own/wp_bc/corpus/personajes';
const INDEX = JSON.parse(fs.readFileSync('C:/own/wp_bc/corpus/index.json', 'utf8'));
const OUTPUT = 'C:/own/wp_bc/bin/wikipedia-birthplaces.json';

const dirs = fs.readdirSync(BASE);
const results = {};
let total = 0, foundBP = 0, foundDP = 0;

function extractPlace(html, label, className) {
  // Find the label th, then find the span with className after it
  const labelIdx = html.indexOf(`<th` + `>${label}</th>`);
  const labelIdx2 = html.indexOf(`<th scope="row" class="infobox-label">${label}</th>`);
  const idx = Math.max(labelIdx, labelIdx2);
  if (idx === -1) return null;
  const after = html.substring(idx, idx + 2000);
  const spanRe = new RegExp(`<span\\s+class="${className}">([^<]+)<\\/span>`);
  const m = after.match(spanRe);
  return m ? m[1].trim() : null;
}

function extractTextBorn(html, type) {
  const text = html
    .replace(/<script[^>]*>[\s\S]*?<\/script>/g, '')
    .replace(/<style[^>]*>[\s\S]*?<\/style>/g, '')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&[^;]+;/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();

  const pattern = type === 'birth'
    ? /[Bb]orn\s+in\s+((?:[A-Z][\w.]+(?:\s[A-Z][\w.]*)*)(?:,\s*[A-Z][\w.]+(?:\s[A-Z][\w.]*)*)?)/
    : /[Dd]ied\s+in\s+((?:[A-Z][\w.]+(?:\s[A-Z][\w.]*)*)(?:,\s*[A-Z][\w.]+(?:\s[A-Z][\w.]*)*)?)/;

  const m = text.match(pattern);
  if (m) {
    let place = m[1].trim().replace(/,$/, '').trim();
    if (place.length > 2 && !/^(the|his|her|their|our|an?)/i.test(place)) return place;
  }
  return null;
}

for (const dir of dirs) {
  const entry = { name: INDEX[dir] || dir };

  // Try wikipedia.html
  const wpPath = path.join(BASE, dir, 'wikipedia.html');
  if (fs.existsSync(wpPath)) {
    total++;
    const html = fs.readFileSync(wpPath, 'utf8');

    // 1. Infobox birthplace (most reliable)
    let bp = extractPlace(html, 'Born', 'birthplace');
    if (!bp) bp = extractTextBorn(html, 'birth');
    if (bp) { entry.birthPlace = bp; foundBP++; }

    // 2. Infobox deathplace
    let dp = extractPlace(html, 'Died', 'deathplace');
    if (!dp) dp = extractTextBorn(html, 'death');
    if (dp) { entry.deathPlace = dp; foundDP++; }
  }

  // Try chd.html (Church History Department)
  const chdPath = path.join(BASE, dir, 'chd.html');
  if (fs.existsSync(chdPath)) {
    const html = fs.readFileSync(chdPath, 'utf8');
    if (!entry.birthPlace) {
      // CHD format: often has structured data
      const chdBp = extractTextBorn(html, 'birth');
      if (chdBp) { entry.birthPlace = chdBp; foundBP++; }
    }
    if (!entry.deathPlace) {
      const chdDp = extractTextBorn(html, 'death');
      if (chdDp) { entry.deathPlace = chdDp; foundDP++; }
    }
  }

  // Try ldsorg.html
  const ldsPath = path.join(BASE, dir, 'ldsorg.html');
  if (fs.existsSync(ldsPath)) {
    const html = fs.readFileSync(ldsPath, 'utf8');
    if (!entry.birthPlace) {
      const ldsBp = extractTextBorn(html, 'birth');
      if (ldsBp) { entry.birthPlace = ldsBp; foundBP++; }
    }
  }

  if (entry.birthPlace || entry.deathPlace) {
    results[dir] = entry;
  }
}

fs.writeFileSync(OUTPUT, JSON.stringify(results, null, 2));
console.log(`Wikipedia HTMLs scanned: ${total}`);
console.log(`Birth places found: ${foundBP}`);
console.log(`Death places found: ${foundDP}`);

// Compare with existing
const existing = JSON.parse(fs.readFileSync('C:/own/wp_bc/bin/wikidata-places.json', 'utf8'));
const existingSlugs = new Set(existing.map(e => e.slug));
const newBP = Object.entries(results).filter(([slug, d]) => {
  const e = existing.find(x => x.slug === slug);
  return d.birthPlace && (!e || !e.birthPlace);
});
const newDP = Object.entries(results).filter(([slug, d]) => {
  const e = existing.find(x => x.slug === slug);
  return d.deathPlace && (!e || !e.deathPlace);
});
console.log(`New birth places: ${newBP.length}`);
console.log(`New death places: ${newDP.length}`);
