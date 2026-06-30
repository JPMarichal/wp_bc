const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');
const https = require('https');

const execPromise = util.promisify(exec);

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'download-wikidata.log';
const BATCH_SIZE = 10;
const MAX_RETRIES = 2;
const CURL_TIMEOUT = 20;
const API_BATCH = 50;

let logStream;

function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  if (logStream) logStream.write(line + '\n');
}

// ---- Re-download Wikipedia with redirect following ----

async function download(url, outputPath, label) {
  for (let attempt = 1; attempt <= MAX_RETRIES + 1; attempt++) {
    try {
      const cmd = `curl --ssl-no-revoke -sL --connect-timeout ${CURL_TIMEOUT} ${JSON.stringify(url)} -o ${JSON.stringify(outputPath)}`;
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

function wikiName(name) {
  return name.replace(/ /g, '_');
}

function extractWikidataId(html) {
  const m = html.match(/"wgWikibaseItemId":"(Q\d+)"/);
  if (m) return m[1];
  const m2 = html.match(/wikidata\.org\/(?:entity|wiki)\/(Q\d+)/);
  if (m2) return m2[1];
  return null;
}

// ---- Wikidata API ----

function fetchJSON(url) {
  return new Promise((resolve, reject) => {
    const req = https.get(url, { rejectUnauthorized: false, timeout: 15000 }, res => {
      let data = '';
      res.on('data', c => data += c);
      res.on('end', () => {
        try { resolve(JSON.parse(data)); } catch (e) { reject(new Error('JSON parse error: ' + data.substring(0, 100))); }
      });
    });
    req.on('error', reject);
    req.on('timeout', () => { req.destroy(); reject(new Error('timeout')); });
  });
}

async function fetchWikidataEntity(qid) {
  const url = `https://www.wikidata.org/wiki/Special:EntityData/${qid}.json`;
  return fetchJSON(url);
}

async function fetchWikidataBatch(qids) {
  const chunk = qids.slice(0, API_BATCH);
  const url = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${chunk.join('|')}&props=labels|descriptions|claims&languages=en&format=json`;
  return fetchJSON(url);
}

function extractPersonSummary(qid, data) {
  const entity = data.entities?.[qid];
  if (!entity || entity.missing === '') return null;

  const claims = entity.claims || {};
  const labels = entity.labels || {};
  const descriptions = entity.descriptions || {};

  function getClaimValue(prop) {
    const claim = claims[prop]?.[0]?.mainsnak;
    if (!claim || claim.snaktype === 'novalue') return null;
    return claim.datavalue?.value;
  }

  function getDate(prop) {
    const val = getClaimValue(prop);
    if (!val || !val.time) return null;
    // Wikidata time format: +1813-01-01T00:00:00Z
    const m = val.time.match(/^[+-](\d{4})/);
    return m ? parseInt(m[1]) : null;
  }

  const birthDate = getDate('P569');
  const deathDate = getDate('P570');
  const image = getClaimValue('P18');
  const birthName = getClaimValue('P1477');

  // Occupation labels
  const occupations = (claims.P106 || []).map(c => {
    const id = c.mainsnak?.datavalue?.value?.id;
    const occLabels = data.entities?.[id]?.labels;
    return occLabels?.en?.value || id;
  });

  // Position held
  const positions = (claims.P39 || []).map(c => {
    const id = c.mainsnak?.datavalue?.value?.id;
    const posLabels = data.entities?.[id]?.labels;
    return posLabels?.en?.value || id;
  });

  // Religion
  let religion = null;
  const relVal = getClaimValue('P140');
  if (relVal) {
    const relId = typeof relVal === 'string' ? relVal : relVal.id;
    religion = data.entities?.[relId]?.labels?.en?.value || relId;
  }

  return {
    qid,
    label: labels.en?.value || null,
    description: descriptions.en?.value || null,
    birthDate,
    deathDate,
    birthName: birthName || null,
    image: image || null,
    occupations: occupations.length ? occupations : null,
    positions: positions.length ? positions : null,
    religion
  };
}

// ---- Main ----

async function run() {
  logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });

  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  log(`Found ${dirs.length} person directories`);

  // Phase 1: Extract Wikidata IDs (re-download redirects if needed)
  log('\n=== PHASE 1: Extract Wikidata IDs ===');

  const personQids = []; // { dir, qid }
  let redownloaded = 0;

  for (let i = 0; i < dirs.length; i += BATCH_SIZE) {
    const batch = dirs.slice(i, i + BATCH_SIZE);
    const batchPromises = batch.map(async (dir) => {
      const dirPath = path.join(CORPUS_DIR, dir);
      const wpFile = path.join(dirPath, 'wikipedia.html');
      const metaFile = path.join(dirPath, 'wikipedia-meta.json');

      if (fs.existsSync(wpFile)) {
        const html = fs.readFileSync(wpFile, 'utf-8');
        const qid = extractWikidataId(html);
        if (qid) {
          personQids.push({ dir, qid });
          return;
        }

        // Re-download with -L (follow redirects)
        const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));
        const originalName = index[dir] || dir;
        const url = `https://en.wikipedia.org/wiki/${wikiName(originalName)}`;
        try {
          await download(url, wpFile, originalName + ' (wp-redirect)');
          const newHtml = fs.readFileSync(wpFile, 'utf-8');
          const newQid = extractWikidataId(newHtml);
          if (newQid) {
            personQids.push({ dir, qid: newQid });
            redownloaded++;
            // Update meta
            const meta = JSON.parse(fs.readFileSync(metaFile, 'utf-8'));
            const titleMatch = newHtml.match(/<title>([^<]+?)\s*[-–—]\s*Wikipedia/i);
            if (titleMatch) meta.pageTitle = titleMatch[1].trim();
            meta.hasInfobox = newHtml.includes('class="infobox');
            const fnMatch = newHtml.match(/<span class="fn"[^>]*>([\s\S]*?)<\/span>/) || newHtml.match(/<div class="fn"[^>]*>([\s\S]*?)<\/div>/);
            if (fnMatch) {
              meta.fullName = fnMatch[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();
              if (meta.fullName.length > 100) meta.fullName = null;
            }
            const bdayMatch = newHtml.match(/<span[^>]*class="bday"[^>]*>(\d{4})-/);
            if (bdayMatch) meta.birthYear = parseInt(bdayMatch[1]);
            fs.writeFileSync(metaFile, JSON.stringify(meta, null, 2));
          } else {
            log(`  No QID after re-download: ${dir}`);
          }
        } catch (err) {
          log(`  WP re-download FAIL ${dir}: ${err.message}`);
        }
      }
    });
    await Promise.allSettled(batchPromises);
    process.stdout.write(`\r  Phase 1: ${Math.min(i + BATCH_SIZE, dirs.length)}/${dirs.length} (${personQids.length} QIDs found, ${redownloaded} re-downloaded)`);
  }

  log(`\nPhase 1 done: ${personQids.length}/${dirs.length} have Wikidata IDs, ${redownloaded} re-downloaded`);

  // Phase 2: Fetch Wikidata data in batches
  log('\n=== PHASE 2: Fetch Wikidata data ===');

  const qidList = [...new Set(personQids.map(p => p.qid))];
  log(`Unique QIDs to fetch: ${qidList.length}`);

  const qidData = {}; // qid -> summary

  for (let i = 0; i < qidList.length; i += API_BATCH) {
    const batch = qidList.slice(i, i + API_BATCH);
    try {
      const result = await fetchWikidataBatch(batch);
      if (!result.entities) {
        log(`  Batch ${i}-${i + batch.length}: no entities in response`);
        continue;
      }
      for (const qid of batch) {
        const summary = extractPersonSummary(qid, result);
        if (summary) qidData[qid] = summary;
      }
    } catch (err) {
      log(`  Batch ${i}-${i + batch.length} FAILED: ${err.message}`);
      // Fallback: fetch one by one
      for (const qid of batch) {
        try {
          const result = await fetchWikidataEntity(qid);
          const summary = extractPersonSummary(qid, result);
          if (summary) qidData[qid] = summary;
          await new Promise(r => setTimeout(r, 200));
        } catch (e2) {
          log(`  Single fetch FAIL ${qid}: ${e2.message}`);
        }
      }
    }
    process.stdout.write(`\r  Phase 2: ${Math.min(i + API_BATCH, qidList.length)}/${qidList.length} QIDs fetched`);
    // Rate limiting: be gentle with Wikidata API
    await new Promise(r => setTimeout(r, 500));
  }

  log(`\nPhase 2 done: ${Object.keys(qidData).length}/${qidList.length} entities fetched`);

  // Phase 3: Save wikidata.json per person
  log('\n=== PHASE 3: Save per-person wikidata.json ===');

  let saved = 0;
  for (const { dir, qid } of personQids) {
    const data = qidData[qid];
    if (data) {
      fs.writeFileSync(path.join(CORPUS_DIR, dir, 'wikidata.json'), JSON.stringify(data, null, 2));
      saved++;
    }
  }

  // Also save a global index
  const globalIndex = {};
  for (const { dir, qid } of personQids) {
    globalIndex[dir] = { qid, data: qidData[qid] || null };
  }
  fs.writeFileSync(path.join(CORPUS_DIR, '..', 'wikidata-index.json'), JSON.stringify(globalIndex, null, 2));

  log(`Saved ${saved} wikidata.json files`);

  // Summary
  const withBirth = Object.values(qidData).filter(d => d && d.birthDate).length;
  const withDeath = Object.values(qidData).filter(d => d && d.deathDate).length;
  const withDesc = Object.values(qidData).filter(d => d && d.description).length;
  const withImage = Object.values(qidData).filter(d => d && d.image).length;

  log(`\n=== DONE ===`);
  log(`Wikidata coverage: ${saved}/${dirs.length} persons`);
  log(`  Birth date: ${withBirth}`);
  log(`  Death date: ${withDeath}`);
  log(`  Description: ${withDesc}`);
  log(`  Image: ${withImage}`);

  if (logStream) logStream.end();
}

run().catch(err => {
  console.error('FATAL:', err);
  process.exit(1);
});
