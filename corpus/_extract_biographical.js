var fs = require('fs');
var dir = __dirname;

var authors = JSON.parse(fs.readFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', 'utf-8'));
var names = authors.filter(function(a) {
  return !a.birthYear && !a.deathYear && !a.image && !a.description_en;
});

var text = '';
for (var v = 1; v <= 4; v++) {
  text += fs.readFileSync(dir + '/biographical-encyclopedia/vol' + v + '.txt', 'utf-8') + '\n';
}

function esc(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

// Extract year from various formats: "born Nov 29, 1897", "born 1897", "born in 1897", "b. 1897"
function extractYear(context, keyword) {
  var patterns = [
    keyword + '\\D{0,60}(\\d{4})',
    '\\b(\\d{4})\\D{0,30}' + keyword
  ];
  for (var i = 0; i < patterns.length; i++) {
    var re = new RegExp(patterns[i], 'i');
    var m = context.match(re);
    if (m) {
      var y = parseInt(m[1]);
      if (y >= 1700 && y <= 2030) return y;
    }
  }
  return null;
}

// Extract death from context: "died ... 1931", "death ... 1931"
function extractDeathYear(context) {
  var pats = [
    /(?:died|death|passed\s+away)\D{0,80}(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i,
    /(?:died|death)\D{0,30}(\d{4})/i,
    /(\d{4})\D{0,30}(?:died|death)/i
  ];
  for (var i = 0; i < pats.length; i++) {
    var m = context.match(pats[i]);
    if (m) {
      var y = parseInt(m[1]);
      if (y >= 1700 && y <= 2030) return y;
    }
  }
  return null;
}

function extractBirthYear(context) {
  var pats = [
    /(?:born|b\.)\D{0,60}(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[.\s]+\d{1,2},?\s*(\d{4})/i,
    /(?:born|b\.)\s+(\d{4})\b/i,
    /(\d{4})\D{0,30}(?:born|birth)/i
  ];
  for (var i = 0; i < pats.length; i++) {
    var m = context.match(pats[i]);
    if (m) {
      var y = parseInt(m[1]);
      if (y >= 1700 && y <= 2010) return y;
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

  var regex = new RegExp('^\\s*' + esc(last) + ',\\s*' + esc(firstCap), 'm');
  var m = text.match(regex);
  if (!m) {
    // Try with last name only all-caps header
    var regex2 = new RegExp('^\\s*' + esc(last) + '\\s*$', 'm');
    var m2 = text.match(regex2);
    if (m2) {
      var idx2 = m2.index;
      var ctx2 = text.substring(idx2, idx2 + 500).replace(/\s+/g, ' ').trim();
      if (ctx2.toLowerCase().indexOf(firstRaw.toLowerCase()) >= 0) {
        m = m2;
      }
    }
  }

  if (m) {
    var idx = m.index;
    // Get a larger context window (up to 2000 chars)
    var ctx = text.substring(Math.max(0, idx - 100), idx + 1500).replace(/\s+/g, ' ').trim();

    var birthYear = extractBirthYear(ctx);
    var deathYear = extractDeathYear(ctx);
    // If no death found, check age
    var ageMatch = ctx.match(/(?:aged?|died at)\D{0,30}(\d{2,3})\s*(?:years?)/i);
    if (!deathYear && birthYear && ageMatch) {
      var age = parseInt(ageMatch[1]);
      if (age >= 30 && age <= 115) deathYear = birthYear + age;
    }

    // Generate description (first 200 chars after the entry header)
    var descMatch = ctx.match(/,\s*(.+?)(?:\.|$)/);
    var description = descMatch ? descMatch[1].trim() : '';
    if (description.length > 200) description = description.substring(0, 200);

    found.push({
      name: name,
      birthYear: birthYear,
      deathYear: deathYear,
      description: 'LDS Biographical Encyclopedia: ' + description,
      context: ctx.substring(0, 400)
    });
    console.log('=== ' + name + ' ===');
    console.log('Birth: ' + (birthYear || '?') + ', Death: ' + (deathYear || '?'));
    console.log('Desc: ' + description.substring(0, 150));
    console.log();
  }
}

console.log('\n=== FOUND: ' + found.length + ' / ' + names.length + ' ===');
console.log('\n=== DATA TO MERGE ===');
for (var f = 0; f < found.length; f++) {
  var entry = found[f];
  var author = authors.find(function(a) { return a.name === entry.name; });
  if (author) {
    var changed = false;
    if (!author.birthYear && entry.birthYear) { author.birthYear = entry.birthYear; changed = true; }
    if (!author.deathYear && entry.deathYear) { author.deathYear = entry.deathYear; changed = true; }
    if (!author.description_en && entry.description) { author.description_en = entry.description; changed = true; }
    if (changed) console.log('WILL MERGE: ' + entry.name + ' birth=' + entry.birthYear + ' death=' + entry.deathYear);
  }
}

// Actually write the data
// fs.writeFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', JSON.stringify(authors, null, 2));
// console.log('\nData written to authors.json');
