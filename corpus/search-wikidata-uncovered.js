const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');
const execPromise = util.promisify(exec);

const CORPUS_DIR = 'C:/own/wp_bc/corpus/personajes';
const AUTHORS_PATH = 'C:/own/wp_bc/wp-content/plugins/bc-quote-block/data/authors.json';
const INDEX_PATH = 'C:/own/wp_bc/corpus/index.json';
const LOG_PATH = 'C:/own/wp_bc/corpus/search-wikidata-uncovered.log';
const BATCH_SIZE = 50;

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function fetchJSON(url) {
  return new Promise((resolve, reject) => {
    const tmpFile = 'C:/own/wp_bc/corpus/_wd_temp.json';
    const cmd = `curl --ssl-no-revoke -s --connect-timeout 15 -m 20 -H "User-Agent: Mozilla/5.0 (compatible; WPBot/1.0)" ${JSON.stringify(url)} -o ${JSON.stringify(tmpFile)}`;
    exec(cmd, { timeout: 30000 }, err => {
      if (err) return reject(err);
      if (!fs.existsSync(tmpFile)) return reject(new Error('no file'));
      try {
        resolve(JSON.parse(fs.readFileSync(tmpFile, 'utf-8')));
      } catch (e) {
        const c = fs.readFileSync(tmpFile, 'utf-8').substring(0, 100);
        reject(new Error('JSON parse: ' + c));
      }
    });
  });
}

function extractData(qid, data) {
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
  const occupations = (claims.P106 || []).map(c => {
    const id = c.mainsnak?.datavalue?.value?.id;
    return data.entities?.[id]?.labels?.en?.value || id;
  });
  const positions = (claims.P39 || []).map(c => {
    const id = c.mainsnak?.datavalue?.value?.id;
    return data.entities?.[id]?.labels?.en?.value || id;
  });
  let religion = null;
  const rv = getVal('P140');
  if (rv) {
    const rid = typeof rv === 'string' ? rv : rv.id;
    religion = data.entities?.[rid]?.labels?.en?.value || rid;
  }
  return {
    qid, label: entity.labels?.en?.value || null,
    description: entity.descriptions?.en?.value || null,
    birthDate: getYear('P569'), deathDate: getYear('P570'),
    birthName: getVal('P1477') || null, image: getVal('P18') || null,
    occupations: occupations.length ? occupations : null,
    positions: positions.length ? positions : null, religion
  };
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

async function run() {
  const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
  const uncovered = authors.filter(a => !a.birthYear && !a.deathYear && !a.image && !a.description_en);
  log(`Total uncovered: ${uncovered.length}`);

  // Search Wikidata for each name + variants
  const qidMap = {}; // normalized -> qid

  for (let i = 0; i < uncovered.length; i++) {
    const person = uncovered[i];
    const name = person.name;
    const variants = [name];

    // Variant: remove middle initial (e.g. "Charles A. Didier" -> "Charles Didier")
    const stripped = name.replace(/^([A-Z][a-z]+)\s+[A-Z]\.\s+/, '$1 ');
    if (stripped !== name) variants.push(stripped);

    // Variant: without Jr./Sr.
    const noSuffix = name.replace(/\s+(Jr|Sr)\.?$/i, '');
    if (noSuffix !== name) variants.push(noSuffix);

    // Variant: Full LastName, FirstName format
    const parts = name.split(' ');
    if (parts.length >= 2) {
      const last = parts[parts.length - 1].replace(/\.$/, '');
      const first = parts[0];
      variants.push(first + ' ' + last);
      variants.push(last + ', ' + first);
      if (parts.length >= 3) {
        const middle = parts.slice(1, -1).join(' ').replace(/\./g, '');
        variants.push(first + ' ' + middle + ' ' + last);
      }
    }

    let found = false;
    for (const v of variants) {
      if (found) break;
      try {
        const url = `https://www.wikidata.org/w/api.php?action=wbsearchentities&search=${encodeURIComponent(v)}&language=en&limit=3&format=json`;
        const result = await fetchJSON(url);
        const hits = result.search || [];
        for (const hit of hits) {
          // Accept exact name match or high similarity
          const hitLabel = (hit.label || '').toLowerCase();
          const searchName = name.toLowerCase();
          if (hitLabel === searchName || hitLabel.includes(searchName) || searchName.includes(hitLabel)) {
            qidMap[person.name] = { qid: hit.id, label: hit.label, description: hit.description };
            found = true;
            break;
          }
        }
      } catch (e) {
        // ignore
      }
      await new Promise(r => setTimeout(r, 100));
    }

    if ((i + 1) % 20 === 0) {
      log(`Searched ${i + 1}/${uncovered.length}, found: ${Object.keys(qidMap).length}`);
    }
  }

  log(`Found ${Object.keys(qidMap).length} QIDs via search`);
  if (Object.keys(qidMap).length === 0) {
    log('No new QIDs found. Done.');
    logStream.end();
    return;
  }

  // Log what we found
  for (const [name, info] of Object.entries(qidMap)) {
    log(`  ${name} -> ${info.qid} (${info.label})`);
  }

  // Fetch Wikidata data for new QIDs
  const uniqueQids = [...new Set(Object.values(qidMap).map(v => v.qid))];
  log(`Unique QIDs: ${uniqueQids.length}`);

  const qidData = {};
  for (let i = 0; i < uniqueQids.length; i += BATCH_SIZE) {
    const batch = uniqueQids.slice(i, i + BATCH_SIZE);
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${batch.join('|')}&props=labels|descriptions|claims&languages=en&format=json`;
    try {
      const result = await fetchJSON(url);
      for (const qid of batch) {
        const s = extractData(qid, result);
        if (s) qidData[qid] = s;
      }
    } catch (err) {
      log(`  Batch ${i}-${i + batch.length} FAIL: ${err.message}`);
    }
    log(`  Fetched ${Math.min(i + BATCH_SIZE, uniqueQids.length)}/${uniqueQids.length}`);
    await new Promise(r => setTimeout(r, 500));
  }

  // Save wikidata.json for each found person
  const index = JSON.parse(fs.readFileSync(INDEX_PATH, 'utf-8'));
  let saved = 0, withBirth = 0, withDeath = 0, withDesc = 0, withImage = 0;

  for (const [name, info] of Object.entries(qidMap)) {
    const data = qidData[info.qid];
    if (!data) continue;

    // Validate: the label must be a reasonable match (full name or last name with context)
    const label = (data.label || '').toLowerCase();
    const searchName = name.toLowerCase();
    // Skip if the label is just a last name (< 4 chars) or generic like "Howells", "Smith"
    // Only accept if label includes the full name OR if the full Wikidata label is 3+ words matching
    const labelWords = label.split(/\s+/);
    const nameWords = searchName.split(/\s+/);
    const lastName = nameWords[nameWords.length - 1].replace(/\.$/, '');
    // Accept if: label contains full name, OR label is same as last name + first initial
    const isValid = label.includes(searchName) 
      || searchName.includes(label)
      || (labelWords.length >= 2 && labelWords.some(w => w === lastName))
    if (!isValid) {
      log(`  SKIP ${name}: label "${data.label}" doesn't match well`);
      continue;
    }


    // Find the normalized directory name
    const normalized = normalizeName(name);
    const dir = path.join(CORPUS_DIR, normalized);
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
    }

    fs.writeFileSync(path.join(dir, 'wikidata.json'), JSON.stringify(data, null, 2));
    saved++;
    if (data.birthDate) withBirth++;
    if (data.deathDate) withDeath++;
    if (data.description) withDesc++;
    if (data.image) withImage++;
  }

  log(`\n=== DONE ===`);
  log(`Saved: ${saved}`);
  log(`  Birth: ${withBirth} | Death: ${withDeath} | Description: ${withDesc} | Image: ${withImage}`);
  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
