const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'fetch-wp-summary.log';
const BATCH_SIZE = 2;

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function fetchJSON(url, tmpFile) {
  return new Promise((resolve, reject) => {
    const cmd = `curl --ssl-no-revoke -sL --connect-timeout 15 -m 20 -H "User-Agent: Mozilla/5.0" ${JSON.stringify(url)} -o ${JSON.stringify(tmpFile)}`;
    exec(cmd, { timeout: 30000 }, err => {
      if (err) return reject(err);
      if (!fs.existsSync(tmpFile)) return reject(new Error('no file'));
      try {
        resolve(JSON.parse(fs.readFileSync(tmpFile, 'utf-8')));
      } catch (e) {
        const c = fs.readFileSync(tmpFile, 'utf-8').substring(0, 80);
        reject(new Error('JSON parse: ' + c));
      }
    });
  });
}

async function run() {
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  log(`Found ${dirs.length} directories`);

  // Identify which dirs need summary (don't have wp-summary.json yet and have wikipedia.html)
  const toFetch = [];
  for (const dir of dirs) {
    const summaryFile = path.join(CORPUS_DIR, dir, 'wp-summary.json');
    if (fs.existsSync(summaryFile)) continue;
    const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');
    if (!fs.existsSync(metaFile)) continue;
    let meta;
    try { meta = JSON.parse(fs.readFileSync(metaFile, 'utf-8')); } catch { continue; }
    if (meta.pageTitle) toFetch.push({ dir, title: meta.pageTitle });
  }

  log(`Need WP summary: ${toFetch.length}/${dirs.length}`);

  let ok = 0, fail = 0;

  for (let i = 0; i < toFetch.length; i += BATCH_SIZE) {
    const batch = toFetch.slice(i, i + BATCH_SIZE);
    await Promise.allSettled(batch.map(async ({ dir, title }) => {
      const url = `https://en.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(title.replace(/ /g, '_'))}`;
      const outFile = path.join(CORPUS_DIR, dir, 'wp-summary.json');
      const tmpFile = path.join(CORPUS_DIR, '..', `_wp_temp_${dir.replace(/[^a-z0-9]/g, '_')}.json`);
      try {
        await fetchJSON(url, tmpFile);
        const data = JSON.parse(fs.readFileSync(tmpFile, 'utf-8'));
        if (data.type === 'https://mediawiki.org/wiki/HyperSwitch/errors/not_found' || data.title?.includes('Not found')) {
          fail++;
        } else {
          fs.renameSync(tmpFile, outFile);
          ok++;
        }
      } catch (err) {
        fail++;
      } finally {
        if (fs.existsSync(tmpFile)) try { fs.unlinkSync(tmpFile); } catch {}
      }
    }));
    if ((i + BATCH_SIZE) % 100 === 0 || i + BATCH_SIZE >= toFetch.length) {
      log(`  ${Math.min(i+BATCH_SIZE, toFetch.length)}/${toFetch.length} (OK:${ok} FAIL:${fail})`);
    }
    await new Promise(r => setTimeout(r, 1500));
  }

  // Summary
  let total = 0, withExtract = 0, withDesc = 0, withThumb = 0;
  for (const dir of dirs) {
    const sf = path.join(CORPUS_DIR, dir, 'wp-summary.json');
    if (fs.existsSync(sf)) {
      total++;
      try {
        const d = JSON.parse(fs.readFileSync(sf, 'utf-8'));
        if (d.extract) withExtract++;
        if (d.description) withDesc++;
        if (d.thumbnail) withThumb++;
      } catch { fs.unlinkSync(sf); total--; }
    }
  }

  log(`\n=== DONE ===`);
  log(`WP Summary: ${total}/${dirs.length}`);
  log(`  Extract: ${withExtract} | Description: ${withDesc} | Thumbnail: ${withThumb}`);
  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
