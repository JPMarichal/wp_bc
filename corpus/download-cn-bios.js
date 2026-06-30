const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const util = require('util');
const execPromise = util.promisify(exec);

const CN_DIR = 'C:/own/wp_bc/corpus/church-news';
const URLS_FILE = 'C:/own/wp_bc/corpus/temp/cn-all-urls.txt';
const AUTHORS_PATH = 'C:/own/wp_bc/wp-content/plugins/bc-quote-block/data/authors.json';
const LOG_PATH = 'C:/own/wp_bc/corpus/download-cn-bios.log';
const OUTPUT_FILE = 'C:/own/wp_bc/corpus/cn-bio-data.json';

const logStream = fs.createWriteStream(LOG_PATH, { flags: 'a' });
function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}`;
  console.log(line);
  logStream.write(line + '\n');
}

function normalizeName(name) {
  return name
    .toLowerCase()
    .replace(/ /g, '-')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9.-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

// Extract birth year from HTML
function extractBirthYear(html) {
  // Pattern 1: "Born Dec. 19, 1948" or "Born December 19, 1948"
  const patterns = [
    /born\s+(?:on\s+)?(?:Jan(?:\.|uary)?|Feb(?:\.|ruary)?|Mar(?:\.|ch)?|Apr(?:\.|il)?|May|Jun(?:\.|e)?|Jul(?:\.|y)?|Aug(?:\.|ust)?|Sep(?:\.|tember)?|Oct(?:\.|ober)?|Nov(?:\.|ember)?|Dec(?:\.|ember)?)[.\s]+\d{1,2},?\s+(\d{4})/i,
    /(?:was\s+)?born\s+(?:in\s+)?(\d{4})/i,
    /birth\D{0,30}(?:year\D{0,10})?(\d{4})/i,
  ];
  for (const pat of patterns) {
    const m = html.match(pat);
    if (m) {
      const year = parseInt(m[1]);
      if (year >= 1800 && year <= 2010) return year;
    }
  }
  return null;
}

// Extract death year from HTML
function extractDeathYear(html) {
  const patterns = [
    /(?:died|death|passed away|passes away)\D{0,50}?(?:on\s+)?(?:Jan(?:\.|uary)?|Feb(?:\.|ruary)?|Mar(?:\.|ch)?|Apr(?:\.|il)?|May|Jun(?:\.|e)?|Jul(?:\.|y)?|Aug(?:\.|ust)?|Sep(?:\.|tember)?|Oct(?:\.|ober)?|Nov(?:\.|ember)?|Dec(?:\.|ember)?)[.\s]+\d{1,2},?\s+(\d{4})/i,
    /(?:died|death)\D{0,50}?(\d{4})/i,
  ];
  for (const pat of patterns) {
    const m = html.match(pat);
    if (m) {
      const year = parseInt(m[1]);
      if (year >= 1900 && year <= 2030) return year;
    }
  }
  return null;
}

// Extract age from obituary
function extractAge(html) {
  const patterns = [
    /(?:dies|died)\D{0,20}at\s+age\s+(\d+)/i,
    /at\s+(?:the\s+)?age\s+of\s+(\d+)/i,
    /(?:aged?|was)\s+(\d+)\s*(?:years?\s+old|years?\s+of\s+age)/i,
  ];
  for (const pat of patterns) {
    const m = html.match(pat);
    if (m) {
      const age = parseInt(m[1]);
      if (age >= 30 && age <= 115) return age;
    }
  }
  return null;
}

// Extract image URL
function extractImage(html) {
  // Look for og:image or article featured image
  const ogMatch = html.match(/<meta\s+property="og:image"\s+content="([^"]+)"/i);
  if (ogMatch) return ogMatch[1];
  const imgMatch = html.match(/<figure[^>]*class="[^"]*lead-art[^"]*"[^>]*>[\s\S]*?<img[^>]*src="([^"]+)"/i);
  if (imgMatch) return imgMatch[1];
  return null;
}

// Extract description
function extractDescription(html) {
  const ogMatch = html.match(/<meta\s+(?:name|property)="description"\s+content="([^"]+)"/i);
  if (ogMatch) return ogMatch[1];
  const ogMatch2 = html.match(/<meta\s+property="og:description"\s+content="([^"]+)"/i);
  if (ogMatch2) return ogMatch2[1];
  return null;
}

async function downloadArticle(url, outputPath) {
  const cmd = `curl --ssl-no-revoke -s --connect-timeout 15 -m 30 -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36" ${JSON.stringify(url)} -o ${JSON.stringify(outputPath)}`;
  try {
    await execPromise(cmd, { timeout: 35000 });
    if (!fs.existsSync(outputPath)) return false;
    const stat = fs.statSync(outputPath);
    if (stat.size < 1000) return false;
    return true;
  } catch (e) {
    return false;
  }
}

async function run() {
  if (!fs.existsSync(CN_DIR)) fs.mkdirSync(CN_DIR, { recursive: true });

  const authors = JSON.parse(fs.readFileSync(AUTHORS_PATH, 'utf-8'));
  const urls = fs.readFileSync(URLS_FILE, 'utf8').split('\n').filter(Boolean);
  const uncovered = authors.filter(a => !a.birthYear && !a.deathYear && !a.image && !a.description_en);

  log(`Total uncovered: ${uncovered.length}`);

  const results = [];

  for (let i = 0; i < uncovered.length; i++) {
    const person = uncovered[i];
    const name = person.name;
    const parts = name.split(' ');
    const first = parts[0].replace('.', '').toLowerCase();
    const last = parts[parts.length - 1].replace('.', '').toLowerCase();
    const firstLast = first + '-' + last;
    const fullSlug = name.toLowerCase().replace(/\./g, '').replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');

    // Find all matching URLs
    const matches = urls.filter(u => {
      const p = u.toLowerCase();
      return p.includes(firstLast) || p.includes(fullSlug);
    });

    // Score and pick best
    const scored = matches.map(u => {
      let score = 0;
      const p = u.toLowerCase();
      if (p.includes('biography')) score += 20;
      if (p.includes('dies') || p.includes('obituary') || p.includes('passes')) score += 15;
      if (p.includes('/elder-') || p.includes('/sister-') || p.includes('/brother-')) score += 5;
      if (p.includes(first + '-' + last)) score += 3;
      return { url: u, score, path: u.replace('https://www.thechurchnews.com', '') };
    });

    scored.sort((a, b) => b.score - a.score);

    const bestCandidate = scored[0];
    if (!bestCandidate) {
      log(`[${i + 1}/${uncovered.length}] ${name}: NO MATCHES`);
      continue;
    }

    const safeName = normalizeName(name);
    const filePath = path.join(CN_DIR, safeName + '.html');

    let html = null;
    if (fs.existsSync(filePath) && fs.statSync(filePath).size > 1000) {
      html = fs.readFileSync(filePath, 'utf8');
      log(`[${i + 1}/${uncovered.length}] ${name} -> CACHED ${bestCandidate.path}`);
    } else {
      log(`[${i + 1}/${uncovered.length}] ${name} -> ${bestCandidate.path}`);
      const ok = await downloadArticle(bestCandidate.url, filePath);
      if (ok) {
        html = fs.readFileSync(filePath, 'utf8');
      }
    }

    if (!html) {
      log(`  FAILED`);
      results.push({ name, url: bestCandidate.url, birthYear: null, deathYear: null, age: null, image: null, description: null });
      await new Promise(r => setTimeout(r, 3000));
      continue;
    }
    const birthYear = extractBirthYear(html);
    const deathYear = extractDeathYear(html);
    const age = extractAge(html);
    const image = extractImage(html);
    const description = extractDescription(html);
    log(`  birth=${birthYear} death=${deathYear} age=${age} img=${!!image} desc=${!!description}`);
    results.push({ name, url: bestCandidate.url, birthYear, deathYear, age, image, description });

    // Rate limiting
    await new Promise(r => setTimeout(r, 3000));
  }

  // Save all results
  fs.writeFileSync(OUTPUT_FILE, JSON.stringify(results, null, 2));
  log(`\n=== DONE ===`);
  log(`Total with data: ${results.length}`);
  log(`Output: ${OUTPUT_FILE}`);

  // Stats
  const withBirth = results.filter(r => r.birthYear).length;
  const withDeath = results.filter(r => r.deathYear).length;
  const withImage = results.filter(r => r.image).length;
  const withDesc = results.filter(r => r.description).length;
  log(`  Birth: ${withBirth} | Death: ${withDeath} | Image: ${withImage} | Desc: ${withDesc}`);

  logStream.end();
}

run().catch(err => { console.error('FATAL:', err); logStream.end(); process.exit(1); });
