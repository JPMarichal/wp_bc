const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');

const execPromise = util.promisify(exec);
const CORPUS_DIR = path.join(__dirname, 'personajes');
const INDEX_FILE = path.join(__dirname, 'index.json');
const LOG_PATH = path.join(__dirname, 'redownload-wp-fix.log');
const TEMP_FILE = '_wp_redl_temp.html';

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

const index = JSON.parse(fs.readFileSync(INDEX_FILE, 'utf-8'));
const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
log(`Found ${dirs.length} directories`);

// Classify current state
const errors = [], redirects = [], goods = [], others = [];
for (const d of dirs) {
  const wpFile = path.join(CORPUS_DIR, d, 'wikipedia.html');
  if (!fs.existsSync(wpFile)) { errors.push(d); continue; }
  const html = fs.readFileSync(wpFile, 'utf-8');
  if (html.includes('Wikimedia Error') || html.includes('Please set a proper')) {
    errors.push(d);
  } else if (html.includes('class="infobox"') || html.includes("class='infobox'")) {
    goods.push(d);
  } else if (html.includes('class="mw-redirect"') || html.includes('redirectMsg') || html.includes('redirectText')) {
    redirects.push(d);
  } else {
    others.push(d);
  }
}

log(`Good: ${goods.length}, Redirects: ${redirects.length}, Errors: ${errors.length}, Other: ${others.length}`);

const NEED_DOWNLOAD = [...errors, ...redirects];
log(`Need download/fix: ${NEED_DOWNLOAD.length}`);

const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
const DELAY_MS = 3500; // 3.5s between requests — be nice to Wikimedia

let ok = 0, fail = 0;

async function downloadOne(dir) {
  const originalName = index[dir] || dir;
  const url = 'https://en.wikipedia.org/wiki/' + encodeURIComponent(originalName).replace(/%20/g, '_');
  const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
  const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');

  // Ensure dir exists
  if (!fs.existsSync(path.join(CORPUS_DIR, dir))) {
    fs.mkdirSync(path.join(CORPUS_DIR, dir), { recursive: true });
  }

  for (let attempt = 1; attempt <= 3; attempt++) {
    const tmpFile = path.join(__dirname, TEMP_FILE);
    const cmd = `curl --ssl-no-revoke -sL --connect-timeout 30 -m 60 -H "User-Agent: ${UA}" -H "Accept: text/html" ${JSON.stringify(url)} -o ${JSON.stringify(tmpFile)}`;

    try {
      await execPromise(cmd, { timeout: 90000 });
      if (!fs.existsSync(tmpFile)) throw new Error('File not created');
      const stat = fs.statSync(tmpFile);
      if (stat.size < 100) throw new Error('Too small: ' + stat.size);

      const html = fs.readFileSync(tmpFile, 'utf-8');
      if (html.includes('Wikimedia Error') || html.includes('Please set a proper')) {
        throw new Error('Wikimedia error page (' + stat.size + ' bytes)');
      }

      // Copy to final destination (bypass trailing period issue)
      fs.copyFileSync(tmpFile, wpFile);
      if (fs.existsSync(tmpFile)) fs.unlinkSync(tmpFile);

      // Update meta
      const meta = { fullName: null, birthYear: null, pageTitle: null, hasInfobox: false };
      const titleMatch = html.match(/<title>([^<]+?)\s*[-–—]\s*Wikipedia/i);
      if (titleMatch) meta.pageTitle = titleMatch[1].trim();
      meta.hasInfobox = html.includes('class="infobox"') || html.includes("class='infobox'");
      const fnMatch = html.match(/<span class="fn"[^>]*>([\s\S]*?)<\/span>/) || html.match(/<div class="fn"[^>]*>([\s\S]*?)<\/div>/);
      if (fnMatch) {
        meta.fullName = fnMatch[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();
        if (meta.fullName.length > 100) meta.fullName = null;
      }
      const bdayMatch = html.match(/<span[^>]*class="bday"[^>]*>(\d{4})-/);
      if (bdayMatch) meta.birthYear = parseInt(bdayMatch[1]);
      fs.writeFileSync(metaFile, JSON.stringify(meta, null, 2));

      ok++;
      const qid = (html.match(/"wgWikibaseItemId":"(Q\d+)"/) || [])[1];
      log(`OK ${originalName} -> ${stat.size}b QID:${qid || 'none'} infobox:${meta.hasInfobox}`);
      return;
    } catch (err) {
      if (fs.existsSync(tmpFile)) fs.unlinkSync(tmpFile);
      if (attempt < 3) {
        log(`  Retry ${attempt}/3 for ${originalName}: ${err.message}`);
        await new Promise(r => setTimeout(r, 5000 * attempt));
      } else {
        fail++;
        log(`FAIL ${originalName} after 3 attempts: ${err.message}`);
      }
    }
  }
}

async function main() {
  log(`\n=== Starting re-download of ${NEED_DOWNLOAD.length} entries (${DELAY_MS}ms delay) ===`);

  for (let i = 0; i < NEED_DOWNLOAD.length; i++) {
    await downloadOne(NEED_DOWNLOAD[i]);
    // Delay between requests
    if (i < NEED_DOWNLOAD.length - 1) {
      await new Promise(r => setTimeout(r, DELAY_MS));
    }
    if ((i + 1) % 25 === 0) {
      log(`Progress: ${i+1}/${NEED_DOWNLOAD.length} (OK:${ok} FAIL:${fail})`);
    }
  }

  // Final stats
  let finalGood = 0, finalRedirect = 0, finalError = 0;
  for (const d of dirs) {
    const wpFile = path.join(CORPUS_DIR, d, 'wikipedia.html');
    if (!fs.existsSync(wpFile)) { finalError++; continue; }
    const html = fs.readFileSync(wpFile, 'utf-8');
    if (html.includes('Wikimedia Error') || html.includes('Please set a proper')) finalError++;
    else if (html.includes('class="infobox"') || html.includes("class='infobox'")) finalGood++;
    else if (html.includes('class="mw-redirect"') || html.includes('redirectMsg')) finalRedirect++;
    else finalGood++; // count as good if it has content
  }

  log(`\n=== FINAL ===`);
  log(`Total: ${dirs.length} | Good: ${finalGood} | Redirect: ${finalRedirect} | Error: ${finalError}`);
  log(`Downloaded: ${ok} OK, ${fail} FAIL`);
  logStream.end();
}

main().catch(err => { console.error('FATAL:', err); logStream.end(); process.exit(1); });
