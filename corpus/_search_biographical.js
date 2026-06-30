var fs = require('fs');
var dir = __dirname;

var authors = JSON.parse(fs.readFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', 'utf-8'));
var names = authors.filter(function(a) {
  return !a.birthYear && !a.deathYear && !a.image && !a.description_en;
});

// Load all 4 volumes
var texts = [];
for (var v = 1; v <= 4; v++) {
  var filePath = dir + '/biographical-encyclopedia/vol' + v + '.txt';
  if (fs.existsSync(filePath)) {
    texts.push({vol: v, text: fs.readFileSync(filePath, 'utf-8')});
  }
}

console.log('Loaded ' + texts.length + ' volumes');
console.log('Searching for ' + names.length + ' authors...\n');

var found = [];

for (var n = 0; n < names.length; n++) {
  var name = names[n].name;
  var parts = name.split(' ');
  var first = parts[0].replace(/\./g, '').toLowerCase();
  var last = parts[parts.length - 1].replace(/\./g, '').toLowerCase();

  // Generate search patterns
  var patterns = [];
  // "First Last"
  patterns.push(first + ' ' + last);
  // "First M. Last" (as given)
  patterns.push(name.toLowerCase());
  // "Last, First"
  patterns.push(last + ', ' + first);
  // "Last, First M."
  if (parts.length >= 3) {
    patterns.push(last + ', ' + first + ' ' + parts.slice(1,-1).map(function(p){return p.replace(/\./g,'');}).join(' '));
    // Try "Last, First M" (without the dot on initial)
    patterns.push(last + ', ' + first + ' ' + parts.slice(1,-1).map(function(p){return p.replace('.','');}).join(' '));
  }

  for (var ti = 0; ti < texts.length; ti++) {
    if (found.some(function(f) { return f.name === name; })) break;
    var text = texts[ti].text;
    var textLower = text.toLowerCase();

    for (var pi = 0; pi < patterns.length; pi++) {
      if (found.some(function(f) { return f.name === name; })) break;
      var pat = patterns[pi];
      var idx = textLower.indexOf(pat);
      if (idx >= 0) {
        // Found! Extract context
        var ctxStart = Math.max(0, idx - 100);
        var ctxEnd = Math.min(text.length, idx + 500);
        var ctx = text.substring(ctxStart, ctxEnd);

        // Extract years from context
        var yearRegex = /(\d{4})\s*[-–]\s*(\d{4})/g;
        var years = [];
        var m;
        while ((m = yearRegex.exec(ctx)) !== null) {
          var y1 = parseInt(m[1]);
          var y2 = parseInt(m[2]);
          if (y1 >= 1700 && y1 <= 2010 && y2 >= 1700 && y2 <= 2030 && y2 > y1) {
            years.push({birth: y1, death: y2});
          }
        }

        // Also look for standalone years close to the name
        var closeContext = text.substring(Math.max(0, idx - 200), Math.min(text.length, idx + 400));
        var birthMatch = closeContext.match(/\b(born|b\.)\s+(\d{4})\b/i);
        var deathMatch = closeContext.match(/\b(died|d\.)\s+(\d{4})\b/i);

        found.push({
          name: name,
          vol: texts[ti].vol,
          context: ctx.replace(/\s+/g, ' ').trim().substring(0, 400),
          years: years,
          birthFromText: birthMatch ? parseInt(birthMatch[2]) : null,
          deathFromText: deathMatch ? parseInt(deathMatch[2]) : null
        });

        console.log('--- ' + name + ' (Vol ' + texts[ti].vol + ') ---');
        console.log(ctx.replace(/\s+/g, ' ').trim().substring(0, 500));
        if (years.length) console.log('  Year ranges: ' + years.map(function(y) { return y.birth + '-' + y.death; }).join(', '));
        if (birthMatch) console.log('  Born: ' + birthMatch[2]);
        if (deathMatch) console.log('  Died: ' + deathMatch[2]);
        console.log();
      }
    }
  }
}

console.log('\n=== FOUND: ' + found.length + ' / ' + names.length + ' ===');
for (var f = 0; f < found.length; f++) {
  var entry = found[f];
  var info = [];
  if (entry.birthFromText) info.push('born ' + entry.birthFromText);
  if (entry.deathFromText) info.push('died ' + entry.deathFromText);
  if (entry.years.length) info.push('ranges: ' + entry.years.map(function(y){return y.birth+'-'+y.death;}).join(', '));
  console.log('  ' + entry.name + ' (Vol ' + entry.vol + ') ' + info.join(' | '));
}

// Also list NOT found
console.log('\n=== NOT FOUND: ' + (names.length - found.length) + ' ===');
for (var n = 0; n < names.length; n++) {
  if (!found.some(function(f) { return f.name === names[n].name; })) {
    console.log('  ' + names[n].name);
  }
}
