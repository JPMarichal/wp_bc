import fs from 'fs';
import https from 'https';

const QID_LIST_PATH = 'C:/own/wp_bc/bin/qid-list.json';
const INTERMEDIATE_PATH = 'C:/own/wp_bc/bin/wikidata-claims.json';
const OUTPUT_PATH = 'C:/own/wp_bc/bin/wikidata-places.json';

const qidEntries = JSON.parse(fs.readFileSync(QID_LIST_PATH, 'utf8'));

function fetchJSON(url, retries = 3) {
  return new Promise((resolve, reject) => {
    const attempt = (n) => {
      https.get(url, { headers: { 'User-Agent': 'BC-WP-Plugin/1.0' } }, (res) => {
        let data = '';
        res.on('data', chunk => data += chunk);
        res.on('end', () => {
          if (data.includes('making too many requests')) {
            if (n > 0) {
              console.log('Rate limited, retrying in 10s...');
              setTimeout(() => attempt(n - 1), 10000);
            } else {
              reject(new Error('Rate limited after retries'));
            }
            return;
          }
          try { resolve(JSON.parse(data)); }
          catch (e) { reject(new Error(`Parse error: ${data.substring(0,200)}`)); }
        });
      }).on('error', reject);
    };
    attempt(retries);
  });
}

async function main() {
  const allQids = qidEntries.map(e => e.qid);
  const results = [];

  // Fetch entities with claims in batches
  for (let i = 0; i < allQids.length; i += 50) {
    const batch = allQids.slice(i, i + 50);
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${batch.join('|')}&props=claims&format=json`;
    const data = await fetchJSON(url);
    
    for (const [qid, entity] of Object.entries(data.entities || {})) {
      const claims = entity.claims || {};
      const entry = { qid, slug: qidEntries.find(e => e.qid === qid)?.slug || '' };
      
      if (claims.P19 && claims.P19[0]?.mainsnak?.datavalue?.value) {
        entry.birthPlaceQid = claims.P19[0].mainsnak.datavalue.value.id;
      }
      
      if (claims.P20 && claims.P20[0]?.mainsnak?.datavalue?.value) {
        entry.deathPlaceQid = claims.P20[0].mainsnak.datavalue.value.id;
      }
      
      if (claims.P26) {
        entry.spouses = [];
        for (const c of claims.P26) {
          if (!c.mainsnak?.datavalue?.value) continue;
          const val = c.mainsnak.datavalue.value;
          const spouse = { spouseQid: val.id };
          const quals = c.qualifiers || {};
          if (quals.P580 && quals.P580[0]?.datavalue?.value?.time) {
            spouse.marriageDate = quals.P580[0].datavalue.value.time;
          }
          if (quals.P582 && quals.P582[0]?.datavalue?.value?.time) {
            spouse.endDate = quals.P582[0].datavalue.value.time;
          }
          if (quals.P1971 && quals.P1971[0]?.datavalue?.value?.amount) {
            spouse.childrenCount = quals.P1971[0].datavalue.value.amount;
          }
          entry.spouses.push(spouse);
        }
      }
      
      results.push(entry);
    }
    
    console.log(`Fetched ${Math.min(i+50, allQids.length)}/${allQids.length}`);
    fs.writeFileSync(INTERMEDIATE_PATH, JSON.stringify(results, null, 2));
    await new Promise(r => setTimeout(r, 1500));
  }

  // Collect all referenced QIDs
  const refQids = new Set();
  for (const r of results) {
    if (r.birthPlaceQid) refQids.add(r.birthPlaceQid);
    if (r.deathPlaceQid) refQids.add(r.deathPlaceQid);
    if (r.spouses) {
      for (const s of r.spouses) refQids.add(s.spouseQid);
    }
  }

  console.log(`Resolving ${refQids.size} referenced QID labels...`);
  
  const labels = {};
  const unique = [...refQids];
  for (let i = 0; i < unique.length; i += 50) {
    const batch = unique.slice(i, i + 50);
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${batch.join('|')}&props=labels&languages=en&format=json`;
    const data = await fetchJSON(url);
    for (const [qid, entity] of Object.entries(data.entities || {})) {
      if (entity.labels && entity.labels.en) {
        labels[qid] = entity.labels.en.value;
      }
    }
    console.log(`Labels resolved ${Math.min(i+50, unique.length)}/${unique.length}`);
    await new Promise(r => setTimeout(r, 2000));
  }
  
  // Apply labels
  for (const r of results) {
    if (r.birthPlaceQid) r.birthPlace = labels[r.birthPlaceQid] || r.birthPlaceQid;
    if (r.deathPlaceQid) r.deathPlace = labels[r.deathPlaceQid] || r.deathPlaceQid;
    if (r.spouses) {
      for (const s of r.spouses) {
        s.spouseName = labels[s.spouseQid] || s.spouseQid;
      }
    }
  }

  fs.writeFileSync(OUTPUT_PATH, JSON.stringify(results, null, 2));
  console.log(`Done. Saved ${results.length} entries to ${OUTPUT_PATH}`);

  const withBP = results.filter(r => r.birthPlace).length;
  const withDP = results.filter(r => r.deathPlace).length;
  const withSP = results.filter(r => r.spouses && r.spouses.length > 0).length;
  console.log(`Stats: birthPlace=${withBP}, deathPlace=${withDP}, spouses=${withSP}`);
}

main().catch(console.error);
