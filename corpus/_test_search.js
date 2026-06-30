var fs = require('fs');
var titles = JSON.parse(fs.readFileSync('_eom_allpages.json', 'utf-8'));

var titleToFile = {};
for (var i = 0; i < titles.length; i++) {
  var safe = titles[i].toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
  if (!titleToFile[safe]) titleToFile[safe] = titles[i];
}
console.log('Total title mappings:', Object.keys(titleToFile).length);

// Test with Richard L. Warner
var name = 'Richard L. Warner';
var parts = name.split(' ');
var first = parts[0].replace(/\./g,'').toLowerCase();
var last = parts[parts.length-1].replace(/\./g,'').toLowerCase();
console.log('Looking for:', first, last);

function escapeRegex(s) {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

for (var safe in titleToFile) {
  var filePath = 'eom/' + safe + '.html';
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
      console.log('FOUND in:', titleToFile[safe]);
      var idx = text.indexOf(first + ' ' + last);
      console.log('Context:', text.substring(Math.max(0, idx-50), idx+100));
    }
  } catch(e) {
    console.log('Error reading', filePath, e.message);
  }
}
console.log('DONE');
