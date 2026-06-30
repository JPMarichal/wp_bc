import fs from 'fs';
import path from 'path';

const CN_DIR = 'C:/own/wp_bc/corpus/church-news';
const INDEX_PATH = 'C:/own/wp_bc/corpus/index.json';
const OUTPUT_PATH = 'C:/own/wp_bc/bin/church-news-data.json';

const index = JSON.parse(fs.readFileSync(INDEX_PATH, 'utf8'));
const files = fs.readdirSync(CN_DIR).filter(f => f.endsWith('.html'));

function cleanText(html) {
  return html
    .replace(/<script[^>]*>[\s\S]*?<\/script>/g, '')
    .replace(/<style[^>]*>[\s\S]*?<\/style>/g, '')
    .replace(/<a[^>]*>/g, ' ')
    .replace(/<\/a>/g, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&[^;]+;/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

const results = {};

for (const file of files) {
  const slug = file.replace('.html', '');
  const name = index[slug] || slug;
  const html = fs.readFileSync(path.join(CN_DIR, file), 'utf8');
  const text = cleanText(html);
  const entry = { slug, name };

  const first = name.split(' ')[0]; // first name for matching

  // Strategy: find "born" and extract location after it
  const bornIdx = text.toLowerCase().indexOf('born');
  if (bornIdx === -1) { results[slug] = entry; continue; }

  const afterBorn = text.substring(bornIdx, bornIdx + 600);

  // Extract: born [optional date stuff], in [Place] or just born in [Place]
  let m;
  
  // Pattern A: "born in [Place]" (with or without date before)
  m = afterBorn.match(/born\s+(?:on\s+)?(?:[A-Z][a-z]+\.?\s+\d+[,\s]+\d{4}\s*)?,?\s*in\s+([A-Z][a-zA-Zéàèùêîôûçäëïöüñ\s,.'-]+?)(?:\s*[.,;]|\s+to\s|$)/);
  if (!m) {
    // Pattern B: "born [Date] in [Place]" — no comma before "in"
    m = afterBorn.match(/born\s+(?:on\s+)?[A-Z][a-z]+\.?\s+\d+[,\s]+\d{4}\s+in\s+([A-Z][a-zA-Zéàèùêîôûçäëïöüñ\s,.'-]+?)(?:\s*[.,;]|\s+to\s|$)/);
  }

  if (m) {
    const place = m[1].trim().replace(/,$/, '').trim();
    if (place.length > 2 && !/^(the|his|her|their|our|this)/i.test(place) && place !== 'Jesus Christ') {
      // Verify it's about this person (first name within 300 chars before "born")
      const contextStart = Math.max(0, bornIdx - 300);
      const context = text.substring(contextStart, bornIdx);
      if (context.toLowerCase().includes(first.toLowerCase())) {
        entry.birthPlace = place;
      }
    }
  }

  // If still not found, try the "Family:" prefix pattern
  if (!entry.birthPlace) {
    const familyMatch = text.match(/Family:[\s\S]{0,300}/i);
    if (familyMatch) {
      const fm = familyMatch[0];
      const m2 = fm.match(/born\s+(?:on\s+)?(?:[A-Z][a-z]+\.?\s+\d+[,\s]+\d{4}\s*)?,?\s*in\s+([A-Z][a-zA-Zéàèùêîôûçäëïöüñ\s,.'-]+?)(?:\s*[.,;]|\s+to\s|$)/i);
      if (m2) {
        const place = m2[1].trim().replace(/,$/, '').trim();
        if (place.length > 2 && !/^(the|his|her|their)/i.test(place)) {
          entry.birthPlace = place;
        }
      }
    }
  }

  results[slug] = entry;
}

fs.writeFileSync(OUTPUT_PATH, JSON.stringify(results, null, 2));
const withBP = Object.values(results).filter(r => r.birthPlace).length;

// Merge with existing coverage
const existing = JSON.parse(fs.readFileSync('C:/own/wp_bc/bin/wikidata-places.json', 'utf8'));
const existingSlugs = new Set(existing.map(e => e.slug));
const newOnes = Object.values(results).filter(r => r.birthPlace && !existingSlugs.has(r.slug));

console.log(`Church News birth places found: ${withBP}/${files.length}`);
console.log(`New (not in wikidata-places): ${newOnes.length}`);
for (const r of newOnes.slice(0, 15)) {
  console.log(`  ${r.slug}: ${r.birthPlace}`);
}
