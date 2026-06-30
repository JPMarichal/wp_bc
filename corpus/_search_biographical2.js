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

var found = [];

for (var n = 0; n < names.length; n++) {
  var name = names[n].name;
  var parts = name.split(' ');
  var last = parts[parts.length - 1].replace(/\./g, '').toUpperCase();
  var firstRaw = parts[0].replace(/\./g, '');
  var firstCap = firstRaw.charAt(0).toUpperCase() + firstRaw.slice(1).toLowerCase();

  // Pattern: ALL CAPS entry header "LAST,  First" at start of line
  var regex = new RegExp('^\\s*' + esc(last) + ',\\s*' + esc(firstCap), 'm');
  var m = text.match(regex);
  if (m) {
    var idx = m.index;
    var ctx = text.substring(Math.max(0, idx - 80), idx + 500).replace(/\s+/g, ' ').trim();

    var yrs = ctx.match(/(\d{4})\s*[-–]\s*(\d{4})/);
    var born = ctx.match(/\b(born|b\.)\s+(\d{4})\b/i);
    var died = ctx.match(/\b(died|d\.)\s+(\d{4})\b/i);

    found.push({
      name: name,
      context: ctx.substring(0, 500),
      yearRange: yrs ? yrs[0] : null,
      birth: born ? parseInt(born[2]) : null,
      death: died ? parseInt(died[2]) : null
    });

    console.log('=== ' + name + ' ===');
    console.log(ctx.substring(0, 500));
    console.log();
  }

    // Also try "LAST NAME" all caps as section header
  var regex2 = new RegExp('^\\s*' + esc(last) + '\\s*$', 'm');
  var m2 = text.match(regex2);
  if (m2 && !m) {
    var idx2 = m2.index;
    var ctx2 = text.substring(Math.max(0, idx2 - 50), idx2 + 400).replace(/\s+/g, ' ').trim();
    var ctxLower = ctx2.toLowerCase();
    if (ctxLower.indexOf(firstRaw.toLowerCase()) >= 0) {
      var yrs2 = ctx2.match(/(\d{4})\s*[-–]\s*(\d{4})/);
      var born2 = ctx2.match(/\b(born|b\.)\s+(\d{4})\b/i);
      var died2 = ctx2.match(/\b(died|d\.)\s+(\d{4})\b/i);
      found.push({
        name: name,
        context: ctx2.substring(0, 500),
        yearRange: yrs2 ? yrs2[0] : null,
        birth: born2 ? parseInt(born2[2]) : null,
        death: died2 ? parseInt(died2[2]) : null
      });
      console.log('=== ' + name + ' (alt) ===');
      console.log(ctx2.substring(0, 500));
      console.log();
    }
  }
}

console.log('=== FOUND: ' + found.length + ' / ' + names.length + ' ===');
found.forEach(function(f) {
  var info = [];
  if (f.birth) info.push('born=' + f.birth);
  if (f.death) info.push('death=' + f.death);
  if (f.yearRange) info.push('range=' + f.yearRange);
  console.log('  ' + f.name + ' ' + info.join(' | '));
  console.log('  Context: ' + f.context.substring(0, 200));
  console.log();
});
