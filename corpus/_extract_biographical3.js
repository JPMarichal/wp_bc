var fs = require('fs');
var dir = __dirname;

var authors = JSON.parse(fs.readFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', 'utf-8'));
var names = authors.filter(function(a) {
  return !a.birthYear && !a.deathYear && !a.image && !a.description_en;
});

var allText = '';
var volMap = {};
for (var v = 1; v <= 4; v++) {
  var t = fs.readFileSync(dir + '/biographical-encyclopedia/vol' + v + '.txt', 'utf-8');
  volMap[v] = t;
  allText += t + '\n';
}

function esc(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

function extractBirthYear(text) {
  var pats = [
    /(?:born|bo[iu]r?n|b\.)\D{0,60}(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i,
    /(?:born|bo[iu]r?n|b\.)\s+(\d{4})\b/i,
    /(\d{4})\D{0,20}(?:born|birth|b\.)/i
  ];
  for (var i = 0; i < pats.length; i++) {
    var m = text.match(pats[i]);
    if (m) {
      var y = parseInt(m[2] || m[1]);
      if (y >= 1700 && y <= 2010) return y;
    }
  }
  return null;
}

function extractDeathYear(text) {
  var pats = [
    /(?:died|death|passed\s+away)\D{0,80}(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i,
    /(?:died|death|passed\s+away)\D{0,30}(\d{4})/i,
    /(\d{4})\D{0,20}(?:died|death)/i
  ];
  for (var i = 0; i < pats.length; i++) {
    var m = text.match(pats[i]);
    if (m) {
      var y = parseInt(m[2] || m[1]);
      if (y >= 1700 && y <= 2030) return y;
    }
  }
  return null;
}

var found = [];

for (var n = 0; n < names.length; n++) {
  var name = names[n].name;
  var parts = name.split(' ');
  var last = parts[parts.length - 1].replace(/\./g, '').toUpperCase();
  var firstRaw = parts[0].replace(/\./g, '');
  var firstCap = firstRaw.charAt(0).toUpperCase() + firstRaw.slice(1).toLowerCase();

  for (var v = 1; v <= 4; v++) {
    if (found.some(function(f) { return f.name === name; })) break;
    var text = volMap[v];

    var regex = new RegExp('^\\s*' + esc(last) + ',\\s*' + esc(firstCap), 'm');
    var m = text.match(regex);

    if (m) {
      var idx = m.index;
      // Use a wide context window: 2000 chars from entry header
      var ctx = text.substring(idx, Math.min(text.length, idx + 2500)).replace(/\s+/g, ' ').trim();

      var birthYear = extractBirthYear(ctx);
      var deathYear = extractDeathYear(ctx);

      var desc = ctx.replace(/^[^,]*,\s*/, '').replace(/^[^,]*,\s*/, '').trim();
      var sentMatch = desc.match(/^(.{30,}?)(?:\.\s|$)/);
      if (sentMatch) desc = sentMatch[1];
      else desc = desc.substring(0, 200);
      desc = 'LDS Biographical Encyclopedia: ' + desc;

      found.push({
        name: name,
        vol: v,
        birthYear: birthYear,
        deathYear: deathYear,
        description: desc
      });

      console.log('=== ' + name + ' (Vol ' + v + ') ===');
      console.log('Birth: ' + (birthYear || '?') + ', Death: ' + (deathYear || '?'));
      if (desc) console.log('Desc: ' + desc.substring(0, 200));
      console.log();
    }
  }
}

console.log('\n=== FOUND: ' + found.length + ' / ' + names.length + ' ===');
for (var f = 0; f < found.length; f++) {
  var e = found[f];
  console.log(e.name + ' (Vol ' + e.vol + ') birth=' + (e.birthYear || '?') + ' death=' + (e.deathYear || '?'));
}

// MERGE
console.log('\n=== MERGING ===');
var merged = 0;
for (var f = 0; f < found.length; f++) {
  var entry = found[f];
  var author = authors.find(function(a) { return a.name === entry.name; });
  if (author) {
    var changed = false;
    if (!author.birthYear && entry.birthYear) { author.birthYear = entry.birthYear; changed = true; }
    if (!author.deathYear && entry.deathYear) { author.deathYear = entry.deathYear; changed = true; }
    if (!author.description_en && entry.description) { author.description_en = entry.description; changed = true; }
    if (changed) {
      console.log('MERGED: ' + entry.name + ' birth=' + entry.birthYear + ' death=' + entry.deathYear);
      merged++;
    }
  }
}
fs.writeFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', JSON.stringify(authors, null, 2));
console.log('\nWritten to authors.json. Total merged: ' + merged);
