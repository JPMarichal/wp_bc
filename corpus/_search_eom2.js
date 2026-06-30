var fs = require('fs');
var dir = __dirname;

var titles = JSON.parse(fs.readFileSync(dir + '/_eom_allpages.json', 'utf-8'));

var titleToFile = {};
for (var i = 0; i < titles.length; i++) {
  var safe = titles[i].toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
  if (!titleToFile[safe]) titleToFile[safe] = titles[i];
}

var authors = JSON.parse(fs.readFileSync(dir + '/../wp-content/plugins/bc-quote-block/data/authors.json', 'utf-8'));
var names = authors.filter(function(a) {
  return !a.birthYear && !a.deathYear && !a.image && !a.description_en;
});

function escapeRegex(s) {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

var found = [];

for (var n = 0; n < names.length; n++) {
  var name = names[n].name;
  var parts = name.split(' ');
  var first = parts[0].replace(/\./g,'').toLowerCase();
  var last = parts[parts.length-1].replace(/\./g,'').toLowerCase();
  var middle = parts.slice(1, -1).map(function(p) { return p.replace(/\./g,'').toLowerCase(); });

  // Search patterns: "First Last", "First M. Last", "Last, First", "Last, First M."
  var patterns = [
    first + ' ' + last,
    first + ' ' + parts.slice(1).join(' ').toLowerCase(), // Full name as given
  ];
  if (middle.length > 0) {
    patterns.push(last + ', ' + first);
    patterns.push(last + ', ' + first + ' ' + middle.join(' '));
  }

  for (var pi = 0; pi < patterns.length; pi++) {
    var namePat = new RegExp(escapeRegex(patterns[pi]));
    if (found.some(function(f) { return f.name === name; })) break;

    for (var safe in titleToFile) {
      if (found.some(function(f) { return f.name === name; })) break;
      var filePath = dir + '/eom/' + safe + '.html';
      if (!fs.existsSync(filePath)) continue;

      try {
        var html = fs.readFileSync(filePath, 'utf-8');
        var text = html.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, ' ')
          .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ' ')
          .replace(/<[^>]+>/g, ' ')
          .replace(/&[^;]+;/g, ' ')
          .replace(/\s+/g, ' ').toLowerCase();

        if (namePat.test(text)) {
          var idx = text.indexOf(patterns[pi]);
          var ctxStart = Math.max(0, idx - 150);
          var ctxEnd = Math.min(text.length, idx + 250);
          var ctx = text.substring(ctxStart, ctxEnd).trim();

          // Extract birth/death years
          var yrPat = /(\d{4})\s*[-–]\s*(\d{4})/g;
          var years = [];
          var yrMatch;
          while ((yrMatch = yrPat.exec(ctx)) !== null) {
            var y1 = parseInt(yrMatch[1]);
            var y2 = parseInt(yrMatch[2]);
            if (y1 >= 1700 && y1 <= 2000 && y2 >= 1700 && y2 <= 2030 && y2 > y1) {
              years.push({birth: y1, death: y2});
            }
          }

          found.push({name: name, page: titleToFile[safe], context: ctx, years: years});
          console.log('---');
          console.log(name + ' FOUND in: ' + titleToFile[safe]);
          console.log('  Context: ...' + ctx.substring(0, 300) + '...');
          if (years.length) console.log('  Years: ' + years.map(function(y) { return y.birth + '-' + y.death; }).join(', '));
        }
      } catch(e) {}
    }
  }
}

console.log('\n=== TOTAL FOUND: ' + found.length + ' / ' + names.length + ' ===');
found.forEach(function(f) {
  console.log('  ' + f.name + ' -> ' + f.page + (f.years.length ? ' [' + f.years.map(function(y) { return y.birth + '-' + y.death; }).join(', ') + ']' : ''));
});
