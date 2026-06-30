const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'fetch-wikidata.log';
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
    qid,
    label: entity.labels?.en?.value || null,
    description: entity.descriptions?.en?.value || null,
    birthDate: getYear('P569'),
    deathDate: getYear('P570'),
    birthName: getVal('P1477') || null,
    image: getVal('P18') || null,
    occupations: occupations.length ? occupations : null,
    positions: positions.length ? positions : null,
    religion
  };
}

async function run() {
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  log(`Found ${dirs.length} directories`);

  // Collect QIDs from Wikipedia HTML
  const dirQids = [];
  for (const dir of dirs) {
    const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
    if (fs.existsSync(wpFile)) {
      const html = fs.readFileSync(wpFile, 'utf-8');
      const m = html.match(/"wgWikibaseItemId":"(Q\d+)"/);
      if (m) dirQids.push({ dir, qid: m[1] });
    }
  }

  const unique = [...new Set(dirQids.map(p => p.qid))];
  log(`QIDs found: ${dirQids.length} entries, ${unique.length} unique`);

  // Fetch Wikidata data in batches
  const qidData = {};

  for (let i = 0; i < unique.length; i += BATCH_SIZE) {
    const batch = unique.slice(i, i + BATCH_SIZE);
    const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${batch.join('|')}&props=labels|descriptions|claims&languages=en&format=json`;

    try {
      const result = await fetchJSON(url);
      for (const qid of batch) {
        const summary = extractData(qid, result);
        if (summary) qidData[qid] = summary;
      }
    } catch (err) {
      log(`  Batch ${i}-${i+batch.length} FAIL: ${err.message}`);
      // Fallback: one at a time
      for (const qid of batch) {
        try {
          const url2 = `https://www.wikidata.org/wiki/Special:EntityData/${qid}.json`;
          const result = await fetchJSON(url2);
          const summary = extractData(qid, result);
          if (summary) qidData[qid] = summary;
        } catch (e2) {
          log(`  ${qid}: ${e2.message}`);
        }
      }
    }

    log(`  Batch ${Math.floor(i/BATCH_SIZE)+1}/${Math.ceil(unique.length/BATCH_SIZE)}: ${Math.min(i+BATCH_SIZE, unique.length)}/${unique.length}`);
    await new Promise(r => setTimeout(r, 500));
  }

  log(`Fetched: ${Object.keys(qidData).length}/${unique.length}`);

  // Save per-person
  let saved = 0, withBirth = 0, withDeath = 0, withDesc = 0, withImage = 0;
  for (const { dir, qid } of dirQids) {
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

  log(`\n=== DONE ===`);
  log(`Wikidata: ${saved}/${dirs.length} persons`);
  log(`  Birth: ${withBirth} | Death: ${withDeath} | Description: ${withDesc} | Image: ${withImage}`);

  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
