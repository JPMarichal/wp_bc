const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'fetch-wikidata-search.log';
const BATCH_SIZE = 50;

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function fetchJSON(url) {
  return new Promise((resolve, reject) => {
    const tmpFile = path.join(CORPUS_DIR, '..', '_wd_temp.json');
    const cmd = `curl --ssl-no-revoke -s --connect-timeout 15 -m 20 -H "User-Agent: Mozilla/5.0" ${JSON.stringify(url)} -o ${JSON.stringify(tmpFile)}`;
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

async function run() {
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));
  log(`Found ${dirs.length} directories`);

  // Find dirs without wikidata.json
  const needQid = [];
  for (const dir of dirs) {
    if (!fs.existsSync(path.join(CORPUS_DIR, dir, 'wikidata.json'))) {
      needQid.push(dir);
    }
  }
  log(`Need QID: ${needQid.length}/${dirs.length}`);

  // Search Wikidata for each
  const foundQids = [];
  for (let i = 0; i < needQid.length; i += 10) {
    const batch = needQid.slice(i, i + 10);
    const results = await Promise.allSettled(batch.map(async (dir) => {
      const name = index[dir] || dir;
      const url = `https://www.wikidata.org/w/api.php?action=wbsearchentities&search=${encodeURIComponent(name)}&language=en&limit=1&format=json`;
      const result = await fetchJSON(url);
      const qid = result.search?.[0]?.id;
      if (qid) foundQids.push({ dir, qid });
      await new Promise(r => setTimeout(r, 50));
    }));
    if ((i + 10) % 100 === 0 || i + 10 >= needQid.length) {
      log(`  Searched ${Math.min(i+10, needQid.length)}/${needQid.length}, found: ${foundQids.length}`);
    }
  }

  log(`Found via search: ${foundQids.length}`);

  if (foundQids.length === 0) {
    log('No new QIDs found. Done.');
    logStream.end();
    return;
  }

  // Fetch Wikidata data for new QIDs
  const unique = [...new Set(foundQids.map(p => p.qid))];
  log(`Unique new QIDs: ${unique.length}`);

  const qidData = {};
  for (let i = 0; i < unique.length; i += BATCH_SIZE) {
    const batch = unique.slice(i, i + BATCH_SIZE);
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${batch.join('|')}&props=labels|descriptions|claims&languages=en&format=json`;
    try {
      const result = await fetchJSON(url);
      for (const qid of batch) {
        const s = extractData(qid, result);
        if (s) qidData[qid] = s;
      }
    } catch (err) {
      log(`  Batch ${i}-${i+batch.length} FAIL: ${err.message}`);
    }
    log(`  Fetched ${Math.min(i+BATCH_SIZE, unique.length)}/${unique.length}`);
    await new Promise(r => setTimeout(r, 500));
  }

  // Save
  let saved = 0, withBirth = 0, withDeath = 0, withDesc = 0, withImage = 0;
  for (const { dir, qid } of foundQids) {
    const data = qidData[qid];
    if (data) {
      fs.writeFileSync(path.join(CORPUS_DIR, dir, 'wikidata.json'), JSON.stringify(data, null, 2));
      saved++;
      if (data.birthDate) withBirth++;
      if (data.deathDate) withDeath++;
      if (data.description) withDesc++;
      if (data.image) withImage++;
    }
  }

  // Overall count
  let totalWd = 0;
  for (const dir of dirs) {
    if (fs.existsSync(path.join(CORPUS_DIR, dir, 'wikidata.json'))) totalWd++;
  }

  log(`\n=== DONE ===`);
  log(`New saved: ${saved}`);
  log(`Total with Wikidata: ${totalWd}/${dirs.length}`);
  log(`  Birth: ${withBirth} | Death: ${withDeath} | Description: ${withDesc} | Image: ${withImage}`);
  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
