const fs = require('fs');
const AUTHORS_PATH = 'C:/own/wp_bc/wp-content/plugins/bc-quote-block/data/authors.json';
const CN_DATA_PATH = 'C:/own/wp_bc/corpus/cn-bio-data.json';

const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
const cnData = JSON.parse(fs.readFileSync(CN_DATA_PATH, 'utf-8'));

let merged = 0;
let skipped = 0;

cnData.forEach(entry => {
  const author = authors.find(a => a.name === entry.name);
  if (!author) {
    console.log(`WARN: ${entry.name} not found in authors.json`);
    skipped++;
    return;
  }

  let changed = false;

  // birthYear: only set if author doesn't have it
  if (!author.birthYear && entry.birthYear) {
    author.birthYear = entry.birthYear;
    changed = true;
  }

  // deathYear: only set if author doesn't have it
  if (!author.deathYear && entry.deathYear) {
    author.deathYear = entry.deathYear;
    changed = true;
  }

  // description_en: only set if author doesn't have it
  if (!author.description_en && entry.description) {
    // Clean HTML entities and truncate
    let desc = entry.description
      .replace(/&#\d+;/g, '')
      .replace(/<[^>]+>/g, '')
      .replace(/\s+/g, ' ')
      .trim();
    if (desc.length > 5) {
      author.description_en = desc;
      changed = true;
    }
  }

  // image: only set if author doesn't have it
  if (!author.image && entry.image) {
    author.image = entry.image;
    changed = true;
  }

  if (changed) {
    merged++;
    console.log(`MERGED: ${entry.name} birth=${entry.birthYear} death=${entry.deathYear}`);
  }
});

fs.writeFileSync(AUTHORS_PATH, JSON.stringify(authors, null, 2));
console.log(`\nDone. Merged: ${merged}, Skipped: ${skipped}`);
console.log('Total authors:', authors.length);

// Coverage stats
const withBirth = authors.filter(a => a.birthYear).length;
const withDeath = authors.filter(a => a.deathYear).length;
const withImage = authors.filter(a => a.image).length;
const withDesc = authors.filter(a => a.description_en).length;
console.log(`birthYear: ${withBirth}/${authors.length} (${(withBirth/authors.length*100).toFixed(1)}%)`);
console.log(`deathYear: ${withDeath}/${authors.length} (${(withDeath/authors.length*100).toFixed(1)}%)`);
console.log(`image: ${withImage}/${authors.length} (${(withImage/authors.length*100).toFixed(1)}%)`);
console.log(`description_en: ${withDesc}/${authors.length} (${(withDesc/authors.length*100).toFixed(1)}%)`);
