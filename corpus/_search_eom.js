var fs = require('fs');
var titles = JSON.parse(require('fs').readFileSync(__dirname + '/_eom_allpages.json', 'utf-8'));
var authors = JSON.parse(require('fs').readFileSync(__dirname + '/../wp-content/plugins/bc-quote-block/data/authors.json', 'utf-8'));
var names = authors.filter(function(a) {
  return !a.birthYear && !a.deathYear && !a.image && !a.description_en;
});

var titleToFile = {};
for (var i = 0; i < titles.length; i++) {
  var safe = titles[i].toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
  if (!titleToFile[safe]) titleToFile[safe] = titles[i];
}

function escapeRegex(s) {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

var found = [];

for (var n = 0; n < names.length; n++) {
  var name = names[n].name;
  var parts = name.split(' ');
  var first = parts[0].replace(/\./g,'').toLowerCase();
  var last = parts[parts.length-1].replace(/\./g,'').toLowerCase();

  for (var safe in titleToFile) {
    var filePath = __dirname + '/eom/' + safe + '.html';
    if (!fs.existsSync(filePath)) continue;

    try {
      var html = fs.readFileSync(filePath, 'utf-8');
      var text = html.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, ' ')
        .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ' ')
        .replace(/<[^>]+>/g, ' ')
        .replace(/&[^;]+;/g, ' ')
        .replace(/\s+/g, ' ').toLowerCase();

      var firstPat = new RegExp('\\b' + escapeRegex(first) + '\\b');
      var lastPat = new RegExp('\\b' + escapeRegex(last) + '\\b');

      if (firstPat.test(text) && lastPat.test(text)) {
        found.push({name: name, page: titleToFile[safe]});
        var idx = text.indexOf(first + ' ' + last);
        if (idx === -1) idx = text.indexOf(last + ', ' + first);
        if (idx === -1) idx = text.indexOf(first);
        var ctxStart = Math.max(0, idx - 80);
        var ctxEnd = Math.min(text.length, idx + 200);
        var ctx = text.substring(ctxStart, ctxEnd).trim();
        console.log('---');
        console.log(name + ' FOUND in: ' + titleToFile[safe]);
        console.log('  Context: ...' + ctx + '...');
        var yrPat = /(\d{4})\s*[\-–]\s*(\d{4})/g;
        var yrMatch;
        while ((yrMatch = yrPat.exec(ctx)) !== null) {
          console.log('  Years: ' + yrMatch[1] + '-' + yrMatch[2]);
        }
        break;
      }
    } catch(e) {}
  }
}

console.log('\n=== TOTAL FOUND: ' + found.length + ' / ' + names.length + ' ===');
found.forEach(function(f) { console.log('  ' + f.name + ' -> ' + f.page); });
