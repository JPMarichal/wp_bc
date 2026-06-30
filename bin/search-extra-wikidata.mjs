import fs from 'fs';
import path from 'path';
import https from 'https';

const INDEX_PATH = 'C:/own/wp_bc/corpus/index.json';
const PERSONAJES_DIR = 'C:/own/wp_bc/corpus/personajes';
const OUTPUT_PATH = 'C:/own/wp_bc/bin/wikidata-extra-search.json';

const index = JSON.parse(fs.readFileSync(INDEX_PATH, 'utf8'));
const existingWD = new Set();
for (const dir of fs.readdirSync(PERSONAJES_DIR)) {
  if (fs.existsSync(path.join(PERSONAJES_DIR, dir, 'wikidata.json'))) {
    existingWD.add(dir);
  }
}

const slugs = Object.keys(index);
const missing = slugs.filter(s => !existingWD.has(s));
console.log(`Searching Wikidata for ${missing.length} people without wikidata.json...`);

function fetchJSON(url) {
  return new Promise((resolve, reject) => {
    https.get(url, { headers: { 'User-Agent': 'BC-WP-Plugin/1.0' } }, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try { resolve(JSON.parse(data)); }
        catch (e) { reject(new Error(`Parse: ${data.substring(0,100)}`)); }
      });
    }).on('error', reject);
  });
}

async function sleep(ms) {
  return new Promise(r => setTimeout(r, ms));
}

async function main() {
  const found = {};

  // Load existing results to resume
  if (fs.existsSync(OUTPUT_PATH)) {
    Object.assign(found, JSON.parse(fs.readFileSync(OUTPUT_PATH, 'utf8')));
    console.log(`Resumed with ${Object.keys(found).length} already found.`);
  }

  for (let i = 0; i < missing.length; i++) {
    const slug = missing[i];
    if (found[slug]) continue; // skip already done

    const name = index[slug];
    const url = `https://www.wikidata.org/w/api.php?action=wbsearchentities&search=${encodeURIComponent(name)}&language=en&limit=3&format=json`;

    try {
      const data = await fetchJSON(url);
      if (data.search && data.search.length > 0) {
        const first = data.search[0];
        // Match by description to filter out non-person results
        found[slug] = {
          name,
          qid: first.id,
          label: first.label,
          description: first.description || ''
        };
        console.log(`[${i+1}/${missing.length}] ${name} → ${first.id} (${first.label})`);
      }
    } catch (e) {
      if (e.message.includes('Rate limited') || e.message.includes('too many')) {
        console.log(`[${i+1}/${missing.length}] Rate limited on "${name}", waiting 30s...`);
        await sleep(30000);
        i--; // retry same person
        continue;
      }
      console.log(`[${i+1}/${missing.length}] Error: ${name}: ${e.message}`);
    }

    // Save every 20
    if ((i + 1) % 20 === 0) {
      fs.writeFileSync(OUTPUT_PATH, JSON.stringify(found, null, 2));
      console.log(`  Saved ${Object.keys(found).length} found so far`);
    }

    await sleep(2500); // 2.5 seconds between requests
  }

  fs.writeFileSync(OUTPUT_PATH, JSON.stringify(found, null, 2));
  console.log(`Done. Found ${Object.keys(found).length} of ${missing.length} missing people on Wikidata.`);
}

main().catch(console.error);
