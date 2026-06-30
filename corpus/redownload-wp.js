const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');

const execPromise = util.promisify(exec);
const CORPUS_DIR = 'personajes';
const LOG_PATH = 'redownload-wp.log';
const BATCH_SIZE = 10;
const MAX_RETRIES = 2;
const CURL_TIMEOUT = 20;

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function wikiName(name) {
  return name.replace(/ /g, '_');
}

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

function extractWikidataId(html) {
  const m = html.match(/"wgWikibaseItemId":"(Q\d+)"/);
  return m ? m[1] : null;
}

async function run() {
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));
  log(`Found ${dirs.length} directories`);

  const toRedownload = [];

  for (const dir of dirs) {
    const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
    const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');
    if (!fs.existsSync(wpFile)) {
      toRedownload.push(dir);
      continue;
    }
    const html = fs.readFileSync(wpFile, 'utf-8');
    if (!extractWikidataId(html)) {
      toRedownload.push(dir);
    }
  }

  log(`Need re-download: ${toRedownload.length}/${dirs.length}`);

  let ok = 0, fail = 0, withQid = 0;

  for (let i = 0; i < toRedownload.length; i += BATCH_SIZE) {
    const batch = toRedownload.slice(i, i + BATCH_SIZE);
    const promises = batch.map(async (dir) => {
      const originalName = index[dir] || dir;
      const url = `https://en.wikipedia.org/wiki/${wikiName(originalName)}`;
      const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
      const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');

      try {
        const size = await download(url, wpFile, originalName);
        const newHtml = fs.readFileSync(wpFile, 'utf-8');
        const qid = extractWikidataId(newHtml);

        ok++;
        if (qid) withQid++;

        // Update meta
        const meta = { fullName: null, birthYear: null, pageTitle: null, hasInfobox: false };
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
      } catch (err) {
        fail++;
        log(`  FAIL ${dir}: ${err.message}`);
      }
    });
    await Promise.allSettled(promises);
    const done = Math.min(i + BATCH_SIZE, toRedownload.length);
    log(`Batch ${Math.ceil(done/BATCH_SIZE)}/${Math.ceil(toRedownload.length/BATCH_SIZE)}: ${done}/${toRedownload.length} (OK:${ok} FAIL:${fail} QID:${withQid})`);
  }

  // Final count
  let totalQid = 0;
  for (const dir of dirs) {
    const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
    if (fs.existsSync(wpFile)) {
      const html = fs.readFileSync(wpFile, 'utf-8');
      if (extractWikidataId(html)) totalQid++;
    }
  }
  log(`\n=== DONE ===`);
  log(`Re-downloaded: ${ok+fail} (OK:${ok} FAIL:${fail})`);
  log(`Total with Wikidata ID: ${totalQid}/${dirs.length}`);
  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
