const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const LOG_PATH = 'fix-wp-fails.log';

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

const FAILED_DIRS = [
  'albert-choules-jr.', 'brigham-young-jr.', 'carlos-g.-revillo-jr.',
  'christoffel-golden-jr.', 'clate-w.-mask-jr.', 'hartman-rector-jr.',
  'john-c.-pingree-jr.', 'joseph-smith-sr.', 'legrand-r.-curtis-jr.',
  'peter-whitmer-jr.', 'richard-e.-turley-sr.', 'william-j.-critchlow-jr.'
];

async function download(url, outputPath, label) {
  for (let attempt = 1; attempt <= 3; attempt++) {
    try {
      const cmd = `curl --ssl-no-revoke -sL --connect-timeout 30 -m 45 ${JSON.stringify(url)} -o ${JSON.stringify(outputPath)}`;
      await new Promise((resolve, reject) => {
        exec(cmd, { timeout: 60000 }, err => {
          if (err) return reject(err);
          if (!fs.existsSync(outputPath)) return reject(new Error('No file'));
          const stat = fs.statSync(outputPath);
          if (stat.size < 100) return reject(new Error('Too small: ' + stat.size));
          resolve(stat.size);
        });
      });
      return;
    } catch (err) {
      log(`  Attempt ${attempt}/3 for ${label}: ${err.message}`);
      if (attempt < 3) await new Promise(r => setTimeout(r, 3000 * attempt));
    }
  }
  throw new Error('Failed after 3 attempts');
}

async function run() {
  const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));
  log(`Fixing ${FAILED_DIRS.length} failed dirs`);

  for (const dir of FAILED_DIRS) {
    const name = index[dir] || dir;
    const wpFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
    const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');
    const url = `https://en.wikipedia.org/wiki/${name.replace(/ /g, '_')}`;

    log(`Trying ${name}...`);
    try {
      await download(url, wpFile, name);
      const html = fs.readFileSync(wpFile, 'utf-8');
      const qid = (html.match(/"wgWikibaseItemId":"(Q\d+)"/) || [])[1];
      log(`  OK ${name} (${fs.statSync(wpFile).size} bytes, QID: ${qid || 'none'})`);

      // Update meta
      const meta = { fullName: null, birthYear: null, pageTitle: null, hasInfobox: false };
      const titleMatch = html.match(/<title>([^<]+?)\s*[-–—]\s*Wikipedia/i);
      if (titleMatch) meta.pageTitle = titleMatch[1].trim();
      meta.hasInfobox = html.includes('class="infobox');
      fs.writeFileSync(metaFile, JSON.stringify(meta, null, 2));
    } catch (err) {
      log(`  FAIL ${name}: ${err.message}`);
    }
  }

  // Summary
  let totalWp = 0;
  const dirs = fs.readdirSync(CORPUS_DIR).filter(d => fs.statSync(path.join(CORPUS_DIR, d)).isDirectory());
  for (const dir of dirs) {
    if (fs.existsSync(path.join(CORPUS_DIR, dir, 'wikipedia.html'))) totalWp++;
  }
  log(`\nTotal with wikipedia.html: ${totalWp}/${dirs.length}`);
  logStream.end();
}

run().catch(err => { console.error(err); process.exit(1); });
