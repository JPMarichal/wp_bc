const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const CORPUS_DIR = 'personajes';
const FAILED = [
  'albert-choules-jr.', 'brigham-young-jr.', 'carlos-g.-revillo-jr.',
  'christoffel-golden-jr.', 'clate-w.-mask-jr.', 'hartman-rector-jr.',
  'john-c.-pingree-jr.', 'joseph-smith-sr.', 'legrand-r.-curtis-jr.',
  'peter-whitmer-jr.', 'richard-e.-turley-sr.', 'william-j.-critchlow-jr.'
];

const index = JSON.parse(fs.readFileSync('index.json', 'utf-8'));

async function main() {
  for (const dir of FAILED) {
    const name = index[dir] || dir;
    const url = `https://en.wikipedia.org/wiki/${name.replace(/ /g, '_')}`;
    const tmpFile = '_wp_jr_fix.html';
    const outFile = path.join(CORPUS_DIR, dir, 'wikipedia.html');
    const metaFile = path.join(CORPUS_DIR, dir, 'wikipedia-meta.json');

    // Ensure dir exists
    const dp = path.join(CORPUS_DIR, dir);
    if (!fs.existsSync(dp)) fs.mkdirSync(dp, { recursive: true });

    const cmd = `curl --ssl-no-revoke -sL --connect-timeout 30 -m 45 ${JSON.stringify(url)} -o ${tmpFile}`;
    try {
      await new Promise((resolve, reject) => {
        exec(cmd, { timeout: 60000 }, err => {
          if (err || !fs.existsSync(tmpFile)) return reject(err || new Error('no file'));
          const stat = fs.statSync(tmpFile);
          if (stat.size < 100) return reject(new Error('too small: ' + stat.size));
          resolve();
        });
      });
      // Copy to final destination (use copy to avoid trailing period issue)
      fs.copyFileSync(tmpFile, outFile);
      const html = fs.readFileSync(outFile, 'utf-8');
      const qid = (html.match(/"wgWikibaseItemId":"(Q\d+)"/) || [])[1];
      console.log(`OK ${name} -> ${fs.statSync(outFile).size}b QID:${qid || 'none'}`);
    } catch (err) {
      console.log(`FAIL ${name}: ${err.message}`);
    } finally {
      if (fs.existsSync(tmpFile)) fs.unlinkSync(tmpFile);
    }
  }
}
main();
