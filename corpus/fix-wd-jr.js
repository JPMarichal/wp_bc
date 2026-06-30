const { exec } = require('child_process');
const fs = require('fs');
const p = require('path');

const items = [
  ['brigham-young-jr.', 'Q4967308'],
  ['hartman-rector-jr.', 'Q5675007'],
  ['joseph-smith-sr.', 'Q6181938'],
  ['legrand-r.-curtis-jr.', 'Q3848705'],
  ['peter-whitmer-jr.', 'Q7177695']
];

function fetchJSON(url, tmp) {
  return new Promise((resolve, reject) => {
    const cmd = 'curl --ssl-no-revoke -s -H "User-Agent: Mozilla/5.0" ' + JSON.stringify(url) + ' -o ' + tmp;
    exec(cmd, { timeout: 15000 }, err => {
      if (err) return reject(err);
      if (!fs.existsSync(tmp)) return reject(new Error('no file'));
      try { resolve(JSON.parse(fs.readFileSync(tmp, 'utf-8'))); }
      catch(e) { reject(e); }
    });
  });
}

async function main() {
  for (const [dir, qid] of items) {
    try {
      const data = await fetchJSON('https://www.wikidata.org/wiki/Special:EntityData/' + qid + '.json', '_wd_new2.json');
      const e = data.entities[qid];
      const claims = e.claims || {};
      const getYear = (prop) => { const v = claims[prop]?.[0]?.mainsnak?.datavalue?.value; return v?.time ? parseInt(v.time.match(/^[+-]?(\d{4})/)[1]) : null; };
      const summary = {
        qid,
        label: e.labels?.en?.value || null,
        description: e.descriptions?.en?.value || null,
        birthDate: getYear('P569'),
        deathDate: getYear('P570'),
        image: claims.P18?.[0]?.mainsnak?.datavalue?.value || null
      };
      fs.writeFileSync(p.join('personajes', dir, 'wikidata.json'), JSON.stringify(summary, null, 2));
      console.log('OK', dir, 'label:', summary.label, 'birth:', summary.birthDate);
    } catch(err) {
      console.log('FAIL', dir, err.message);
    }
  }
}
main();
