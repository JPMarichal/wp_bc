#!/usr/bin/env node
const { promises: fs } = require('fs');
const path = require('path');

const BASE_URL = process.env.BC_PERF_URL || 'http://localhost:8080';
const REPORTS_DIR = path.join(__dirname, '..', 'perf-reports');

const C = {
  good: '\x1b[32m', avg: '\x1b[33m', poor: '\x1b[31m',
  bold: '\x1b[1m', reset: '\x1b[0m', dim: '\x1b[2m',
};

const PAGES = [
  { name: 'Homepage',           url: '/' },
  { name: 'All Articles',       url: '/todos-los-articulos/' },
  { name: 'Single Post',        url: '/sara-hermana-o-sobrina-de-abraham/' },
  { name: 'Search Results',     url: '/?s=Abraham' },
  { name: 'Glosario',           url: '/glosario/' },
  { name: '404',                url: '/no-existe/' },
];

function color(score, label) {
  const c = score >= 90 ? C.good : score >= 50 ? C.avg : C.poor;
  return `${c}${label}${C.reset}`;
}

function bar(score, w) {
  const f = Math.round((score / 100) * w);
  const c = score >= 90 ? C.good : score >= 50 ? C.avg : C.poor;
  return `${c}${'█'.repeat(f)}${C.dim}${'░'.repeat(w - f)}${C.reset}`;
}

async function runLighthouse(url) {
  const chromeLauncher = (await import('chrome-launcher')).default;
  const { default: lighthouse } = await import('lighthouse');

  const chrome = await chromeLauncher.launch({
    chromeFlags: ['--headless', '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage'],
  });

  let result;
  try {
    result = await lighthouse(url, {
      port: chrome.port,
      output: 'json',
      onlyCategories: ['performance', 'accessibility', 'best-practices', 'seo'],
      logLevel: 'error',
    });
  } finally {
    await chrome.kill();
  }
  return result.lhr;
}

function extractOpportunities(lhr) {
  const audits = lhr.audits;
  return Object.values(audits)
    .filter(a => a.details && a.details.type === 'opportunity' && a.details.overallSavingsMs > 0)
    .sort((a, b) => b.details.overallSavingsMs - a.details.overallSavingsMs)
    .slice(0, 6)
    .map(a => ({
      title: a.title,
      savingsMs: a.details.overallSavingsMs,
      displayValue: a.displayValue || '',
    }));
}

function printResults(allResults) {
  const h = ['Page', 'Perf', 'A11y', 'BP', 'SEO'];
  const w = [24, 8, 8, 8, 8];
  const sep = h.map((_, i) => '─'.repeat(w[i] + 2)).join('┬');

  console.log(`\n  ${C.bold}Lighthouse Report — ${BASE_URL}${C.reset}`);
  console.log(`  ${new Date().toISOString().replace('T', ' ').slice(0, 19)}`);
  console.log(`  ┌${sep}┐`);
  console.log(`  │ ${h.map((x, i) => x.padEnd(w[i])).join(' │ ')} │`);
  console.log(`  ├${sep}┤`);

  for (const r of allResults) {
    if (r.scores.performance === 0 && r.scores.accessibility === 0) continue;
    const row = [
      r.name.padEnd(w[0]),
      color(r.scores.performance, `${r.scores.performance}`.padStart(3).padEnd(w[1] - 1)),
      color(r.scores.accessibility, `${r.scores.accessibility}`.padStart(3).padEnd(w[2] - 1)),
      color(r.scores['best-practices'], `${r.scores['best-practices']}`.padStart(3).padEnd(w[3] - 1)),
      color(r.scores.seo, `${r.scores.seo}`.padStart(3).padEnd(w[4] - 1)),
    ];
    console.log(`  │ ${row.join(' │ ')} │`);
    const bars = [bar(r.scores.performance, 8), bar(r.scores.accessibility, 8),
      bar(r.scores['best-practices'], 8), bar(r.scores.seo, 8)];
    console.log(`  │ ${''.padEnd(w[0])} │ ${bars.join(' │ ')} │`);
  }
  console.log(`  └${sep.replace(/./g, '─')}┘`);
}

function printOpportunities(allResults, rawReports) {
  const weakPages = allResults
    .filter(r => r.scores.performance > 0 && r.scores.performance < 90)
    .sort((a, b) => a.scores.performance - b.scores.performance);

  if (!weakPages.length) return;

  console.log(`\n  ${C.bold}Top Performance Opportunities${C.reset}`);
  console.log(`  ${'─'.repeat(60)}`);

  for (const page of weakPages.slice(0, 2)) {
    const lhr = rawReports[page.url];
    if (!lhr) continue;
    const opps = extractOpportunities(lhr);
    if (!opps.length) continue;

    console.log(`\n  ${C.bold}${page.name}${C.reset} (score: ${color(page.scores.performance, page.scores.performance)})`);
    for (const o of opps) {
      const savings = o.savingsMs >= 1000
        ? `${(o.savingsMs / 1000).toFixed(1)}s`
        : `${o.savingsMs}ms`;
      console.log(`    ${bar(Math.min(o.savingsMs / 50, 100), 10)}  ${C.bold}${savings}${C.reset}  ${o.title}`);
    }
  }
  console.log('');
}

function printSummary(allResults) {
  const cats = ['performance', 'accessibility', 'best-practices', 'seo'];
  const scores = allResults.filter(r => r.scores.performance > 0).map(r => r.scores);

  console.log(`  ${C.bold}Averages (real pages only)${C.reset}`);
  console.log(`  ${'─'.repeat(48)}`);
  for (const cat of cats) {
    const vals = scores.map(s => s[cat]);
    const avg = Math.round(vals.reduce((a, b) => a + b, 0) / vals.length);
    console.log(`  ${cat.padEnd(18)} ${color(avg, `${avg}`)}`);
  }
  console.log('');
}

async function saveHistory(allResults, rawReports) {
  await fs.mkdir(REPORTS_DIR, { recursive: true });
  const ts = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
  const file = path.join(REPORTS_DIR, `${ts}.json`);
  const data = {
    timestamp: new Date().toISOString(),
    baseUrl: BASE_URL,
    pages: allResults.map(r => ({
      name: r.name, url: r.url, scores: r.scores,
      opportunities: rawReports[r.url] ? extractOpportunities(rawReports[r.url]) : [],
    })),
  };
  await fs.writeFile(file, JSON.stringify(data, null, 2));
  return file;
}

async function getHistory() {
  try { await fs.access(REPORTS_DIR); } catch { return []; }
  const files = (await fs.readdir(REPORTS_DIR)).filter(f => f.endsWith('.json')).sort();
  const history = [];
  for (const file of files.slice(-10)) {
    const d = JSON.parse(await fs.readFile(path.join(REPORTS_DIR, file), 'utf8'));
    const vals = d.pages.filter(p => p.scores.performance > 0).flatMap(p => Object.values(p.scores));
    history.push({ file, timestamp: d.timestamp, avgScore: vals.length ? Math.round(vals.reduce((a, b) => a + b, 0) / vals.length) : null });
  }
  return history;
}

async function printHistory() {
  const h = await getHistory();
  if (h.length < 2) return;
  console.log(`  ${C.bold}Last ${h.length} runs${C.reset}`);
  console.log(`  ${'─'.repeat(48)}`);
  for (const x of h) {
    const t = x.timestamp.replace('T', ' ').slice(0, 19);
    console.log(`  ${t}  ${x.avgScore !== null ? color(x.avgScore, `${x.avgScore}`) : '  -'}`);
  }
  console.log('');
}

async function main() {
  const args = process.argv.slice(2);
  const singleUrl = args.find(a => a.startsWith('http'));
  const showHelp = args.includes('--help') || args.includes('-h');
  if (showHelp) {
    console.log(`\n  Usage: npm run perf [-- <url>]\n`);
    console.log(`  Examples:`);
    console.log(`    npm run perf           # test all default pages`);
    console.log(`    npm run perf -- /about  # test a specific path`);
    process.exit(0);
  }

  const pages = singleUrl
    ? [{ name: 'Custom', url: singleUrl }]
    : PAGES;

  pages.forEach(p => {
    if (!p.url.startsWith('http')) p.url = `${BASE_URL}${p.url}`;
  });

  console.log(`\n  Running Lighthouse on ${pages.length} page(s)...`);
  const allResults = [];
  const rawReports = {};

  for (const page of pages) {
    process.stdout.write(`  ${page.name}... `);
    try {
      const lhr = await runLighthouse(page.url);
      rawReports[page.url] = lhr;
      const scores = {
        performance: Math.round(lhr.categories.performance.score * 100),
        accessibility: Math.round(lhr.categories.accessibility.score * 100),
        'best-practices': Math.round(lhr.categories['best-practices'].score * 100),
        seo: Math.round(lhr.categories.seo.score * 100),
      };
      allResults.push({ ...page, scores });
      process.stdout.write('✓\n');
    } catch (err) {
      process.stdout.write(`✗ ${err.message}\n`);
    }
  }

  if (!allResults.length) {
    console.error('\n  No results. Is the site running?');
    process.exit(1);
  }

  printResults(allResults);
  printOpportunities(allResults, rawReports);
  printSummary(allResults);

  const f = await saveHistory(allResults, rawReports);
  console.log(`  Saved: perf-reports/${path.basename(f)}\n`);
  await printHistory();
}

main().catch(err => { console.error(err); process.exit(1); });
