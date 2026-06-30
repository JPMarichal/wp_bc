const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'download-ldsorg.log';
const PARALLEL = 3;
const MAX_RETRIES = 2;
const CURL_TIMEOUT = 25;

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function makeSlug(name) {
  return name
    .toLowerCase()
    .replace(/\s+/g, '-')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

async function download(url, outputPath, label) {
  for (let attempt = 1; attempt <= MAX_RETRIES + 1; attempt++) {
    try {
      const cmd = `curl --ssl-no-revoke -sL --connect-timeout ${CURL_TIMEOUT} -m 30 -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)" ${JSON.stringify(url)} -o ${JSON.stringify(outputPath)}`;
      await new Promise((resolve, reject) => {
        exec(cmd, { timeout: 40000 }, err => {
          if (err) return reject(err);
          if (!fs.existsSync(outputPath)) return reject(new Error('No file'));
          const stat = fs.statSync(outputPath);
          if (stat.size < 200) return reject(new Error('Too small: ' + stat.size));
          resolve(stat.size);
        });
      });
      return 'ok';
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

async function run() {
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));
  log(`Found ${dirs.length} directories`);

  const todos = [];
  for (const dir of dirs) {
    const outFile = path.join(CORPUS_DIR, dir, 'ldsorg.html');
    if (fs.existsSync(outFile) && fs.statSync(outFile).size > 200) continue;
    const name = index[dir] || dir;
    const slug = makeSlug(name);
    const url = `https://www.churchofjesuschrist.org/learn/${slug}?lang=eng`;
    todos.push({ dir, name, slug, url, outFile });
  }

  log(`Need ldsorg: ${todos.length}/${dirs.length}`);

  let ok = 0, fail = 0, verifiedOk = 0;

  for (let i = 0; i < todos.length; i += PARALLEL) {
    const batch = todos.slice(i, i + PARALLEL);
    await Promise.allSettled(batch.map(async ({ dir, name, slug, url, outFile }) => {
      try {
        await download(url, outFile, name);
        ok++;
        // Verify it's a real bio page (not a 404)
        const html = fs.readFileSync(outFile, 'utf-8');
        const nextData = html.match(/__NEXT_DATA__[^>]*>\s*(\{.*?\})\s*<\/script>/);
        let is404 = false;
        if (nextData) {
          try {
            const d = JSON.parse(nextData[1]);
            const status = d?.props?.pageProps?.statusCode || d?.props?.pageProps?.page?.statusCode;
            if (status === 404) is404 = true;
          } catch {}
        }
        if (is404 || html.includes('<title>Page not found')) {
          fs.unlinkSync(outFile);
          throw new Error('404');
        }
        verifiedOk++;
      } catch (err) {
        fail++;
        if (fs.existsSync(outFile)) try { fs.unlinkSync(outFile); } catch {}
      }
    }));
    if ((i + PARALLEL) % 60 === 0 || i + PARALLEL >= todos.length) {
      log(`  ${Math.min(i+PARALLEL, todos.length)}/${todos.length} OK:${ok} FAIL:${fail} VERIFIED:${verifiedOk}`);
    }
    await new Promise(r => setTimeout(r, 1200));
  }

  // Final count
  let total = 0;
  for (const dir of dirs) {
    const f = path.join(CORPUS_DIR, dir, 'ldsorg.html');
    if (fs.existsSync(f) && fs.statSync(f).size > 200) total++;
  }

  log(`\n=== DONE ===`);
  log(`ldsorg.html: ${total}/${dirs.length}`);
  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); process.exit(1); });
