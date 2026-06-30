import fs from 'fs';
import path from 'path';
import https from 'https';

const PERSONAJES_DIR = 'C:/own/wp_bc/corpus/personajes';
const INDEX_PATH = 'C:/own/wp_bc/corpus/index.json';
const OUTPUT_PATH = 'C:/own/wp_bc/bin/wikidata-extra-places.json';

const index = JSON.parse(fs.readFileSync(INDEX_PATH, 'utf8'));
const existingWD = new Set();
for (const dir of fs.readdirSync(PERSONAJES_DIR)) {
  if (fs.existsSync(path.join(PERSONAJES_DIR, dir, 'wikidata.json'))) {
    existingWD.add(dir);
  }
}

const missing = Object.keys(index).filter(s => !existingWD.has(s));
console.log(`Querying SPARQL for ${missing.length} people...`);

function sparqlQuery(sparql) {
  return new Promise((resolve, reject) => {
    const url = 'https://query.wikidata.org/sparql?format=json&query=' + encodeURIComponent(sparql);
    https.get(url, { headers: { 'User-Agent': 'BC-WP-Plugin/1.0', 'Accept': 'application/json' } }, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        if (data.includes('<html') || res.statusCode !== 200) {
          reject(new Error(`HTTP ${res.statusCode}: ${data.substring(0,200)}`));
          return;
        }
        try { resolve(JSON.parse(data)); }
        catch (e) { reject(new Error(`Parse: ${data.substring(0,100)}`)); }
      });
    }).on('error', reject);
  });
}

async function main() {
  const names = missing.map(s => index[s]);
  const allResults = {};

  // Batch names to avoid query too large
  for (let start = 0; start < names.length; start += 100) {
    const batch = names.slice(start, start + 100);
    const values = batch.map(n => `"${n.replace(/"/g, '\\"')}"@en`).join(' ');

    const sparql = `
SELECT DISTINCT ?item ?itemLabel ?birthPlaceLabel ?deathPlaceLabel ?spouse ?spouseLabel WHERE {
  VALUES ?itemLabel { ${values} }
  ?item rdfs:label ?itemLabel.
  FILTER(LANG(?itemLabel) = "en")
  OPTIONAL { ?item wdt:P19 ?birthPlace. }
  OPTIONAL { ?item wdt:P20 ?deathPlace. }
  OPTIONAL { ?item wdt:P26 ?spouse. }
  SERVICE wikibase:label { bd:serviceParam wikibase:language "en". }
}
LIMIT 2000`.trim();

    console.log(`Querying batch ${start/100 + 1} (${batch.length} names)...`);
    try {
      const data = await sparqlQuery(sparql);
      const bindings = data.results?.bindings || [];
      console.log(`  Got ${bindings.length} result rows`);

      for (const row of bindings) {
        const label = row.itemLabel?.value;
        const item = row.item?.value?.replace('http://www.wikidata.org/entity/', '');
        if (!label || !item) continue;
        const slug = missing[start + batch.indexOf(label)];
        if (!slug) continue;

        if (!allResults[slug]) {
          allResults[slug] = { name: label, qid: item };
        }
        if (row.birthPlaceLabel?.value) allResults[slug].birthPlace = row.birthPlaceLabel.value;
        if (row.deathPlaceLabel?.value) allResults[slug].deathPlace = row.deathPlaceLabel.value;
        if (row.spouse?.value) {
          if (!allResults[slug].spouses) allResults[slug].spouses = [];
          const spouseQid = row.spouse.value.replace('http://www.wikidata.org/entity/', '');
          if (!allResults[slug].spouses.find(s => s.qid === spouseQid)) {
            allResults[slug].spouses.push({ qid: spouseQid, name: row.spouseLabel?.value || '' });
          }
        }
      }
    } catch (e) {
      console.log(`  Error: ${e.message}`);
    }

    // Small delay between batches
    await new Promise(r => setTimeout(r, 1000));
  }

  // Now fetch qualifiers (marriage dates) for spouses
  // Collect all spouse QIDs
  const spouseQids = new Set();
  for (const r of Object.values(allResults)) {
    if (r.spouses) for (const s of r.spouses) spouseQids.add(s.qid);
  }
  console.log(`\nFetching qualifiers for ${spouseQids.size} unique spouses...`);

  // For each person with spouses, fetch their P26 claims with qualifiers
  for (const [slug, r] of Object.entries(allResults)) {
    if (!r.spouses || r.spouses.length === 0) continue;
    // Use the entities API to get claims with qualifiers for this person
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${r.qid}&props=claims&format=json`;
    try {
      const data = await fetchJSON(url);
      const claims = data.entities?.[r.qid]?.claims?.P26;
      if (claims) {
        for (const c of claims) {
          const val = c.mainsnak?.datavalue?.value;
          if (!val) continue;
          const spouseEntry = r.spouses.find(s => s.qid === val.id);
          if (!spouseEntry) continue;
          const quals = c.qualifiers || {};
          if (quals.P580?.[0]?.datavalue?.value?.time) {
            const m = quals.P580[0].datavalue.value.time.match(/^\+?(\d{4})/);
            if (m) spouseEntry.marriage_year = parseInt(m[1]);
          }
          if (quals.P582?.[0]?.datavalue?.value?.time) {
            const m = quals.P582[0].datavalue.value.time.match(/^\+?(\d{4})/);
            if (m) spouseEntry.end_year = parseInt(m[1]);
          }
        }
      }
    } catch (e) {
      console.log(`  Error fetching qualifiers for ${r.name}: ${e.message}`);
    }
    await new Promise(r => setTimeout(r, 300));
  }

  fs.writeFileSync(OUTPUT_PATH, JSON.stringify(allResults, null, 2));
  const withBP = Object.values(allResults).filter(r => r.birthPlace).length;
  const withDP = Object.values(allResults).filter(r => r.deathPlace).length;
  const withSP = Object.values(allResults).filter(r => r.spouses?.length > 0).length;
  console.log(`\nDone. Found on Wikidata: ${Object.keys(allResults).length}/${missing.length}`);
  console.log(`Stats: birthPlace=${withBP}, deathPlace=${withDP}, spouses=${withSP}`);
}

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

main().catch(console.error);
