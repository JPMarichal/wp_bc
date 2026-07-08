const fs = require('fs');
let content = fs.readFileSync('current_body.txt', 'utf-8');

// 1. John Gee blockquote
content = content.replace(
  "<p>In the Egyptian of Abraham's day, there are two words for wife. One (<em>\u1e25mt</em>) means only 'wife'; the other (<em>snt</em>) means principally 'sister' but can also mean 'wife.' So by using an ambiguous term, Abraham was not saying something that was false.</p>",
  '<p>En el egipcio de la \u00e9poca de Abraham, existen dos palabras para esposa. Una (<em>\u1e25mt</em>) significa solo &#8220;esposa&#8221;; la otra (<em>snt</em>) significa principalmente &#8220;hermana&#8221; pero tambi\u00e9n puede significar &#8220;esposa&#8221;. Por lo tanto, al usar un t\u00e9rmino ambiguo, Abraham no estaba diciendo algo falso.</p>'
);

// 2. BYU Studies inline conclusion
content = content.replace(
  '"for an Egyptian audience, Abram\'s calling Sarai his sister would not have precluded her being his wife."',
  '&#8220;para una audiencia egipcia, que Abram llamara hermana a Sarai no imped\u00eda que ella fuera su esposa&#8221;'
);

// 3. Article titles in running text
content = content.replace(
  '"Did Abraham Lie about His Wife, Sarai?"',
  '&#8220;\u00bfMinti\u00f3 Abraham acerca de su esposa, Sarai?\u8221'
);

content = content.replace(
  '"The Wife/Sister Experience"',
  '&#8220;La experiencia de esposa/hermana&#8221;'
);

content = content.replace(
  '"Why Abraham Was Not Wrong to Lie"',
  '&#8220;Por qu\u00e9 Abraham no estaba equivocado al mentir&#8221;'
);

// 4. Fix source reference book name still wrong
content = content.replace(
  'en <em>The Temple in Time and Eternity</em>, RSC/BYU.',
  'en <em>An Introduction to the Book of Abraham</em>, RSC/BYU, 2017.'
);

fs.writeFileSync('current_body.txt', content);
console.log('Saved.');

// Verify
const v = fs.readFileSync('current_body.txt', 'utf-8');
console.log('Gee quote fixed:', v.includes('En el egipcio de la \u00e9poca') ? 'OK' : 'MISSING');
console.log('BYU inline fixed:', v.includes('para una audiencia egipcia') ? 'OK' : 'MISSING');
console.log('Smoot article fixed:', v.includes('\u00bfMinti\u00f3 Abraham') ? 'OK' : 'MISSING');
console.log('Strathearn article fixed:', v.includes('La experiencia de esposa/hermana') ? 'OK' : 'MISSING');
console.log('Boyce article fixed:', v.includes('Por qu\u00e9 Abraham no estaba equivocado') ? 'OK' : 'MISSING');
