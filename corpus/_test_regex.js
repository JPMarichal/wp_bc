var text = 'in may 1849, richard ballantyne began plans. joseph b. wirthlin and richard l. warner as assistants.';
var first = 'richard';
var last = 'warner';
var firstPat = new RegExp('\\b' + first.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b');
var lastPat = new RegExp('\\b' + last.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b');
console.log('firstPat:', firstPat);
console.log('lastPat:', lastPat);
console.log('first test:', firstPat.test(text));
console.log('last test:', lastPat.test(text));
