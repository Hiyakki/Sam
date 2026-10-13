import { execSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const baseUrl = process.argv[2] || 'http://127.0.0.1:8000';
const qaDir = path.resolve(__dirname, '../storage/app/qa/lighthouse');

if (!existsSync(qaDir)) {
  mkdirSync(qaDir, { recursive: true });
}

const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const chromePath = existsSync(edgePath) ? edgePath : undefined;

const pages = [
  { name: 'home', path: '/' },
  { name: 'about', path: '/about' },
  { name: 'katalog', path: '/katalog' },
  { name: 'kategori', path: '/katalog?kategori=orthosis' },
  { name: 'produk', path: '/katalog/wrist-thumb-support' },
  { name: 'faq', path: '/faq' },
  { name: 'testimoni', path: '/testimoni' },
  { name: 'appointment', path: '/appointment' },
  { name: 'privasi', path: '/kebijakan-privasi' },
];

console.log('===============================================================');
console.log('   SAM Orthotic Prosthetic - Lighthouse Audit Runner');
console.log(`   Base URL: ${baseUrl}`);
console.log(`   Output: ${qaDir}`);
console.log('===============================================================\n');

const results = [];

for (const page of pages) {
  const url = `${baseUrl}${page.path}`;
  const outJson = path.join(qaDir, `${page.name}-mobile.json`);
  const outHtml = path.join(qaDir, `${page.name}-mobile.html`);

  console.log(`Auditing ${page.name} (${url}) [Mobile] ...`);

  try {
    const env = { ...process.env };
    if (chromePath) {
      env.CHROME_PATH = chromePath;
    }

    const cmd = `npx -y lighthouse "${url}" --chrome-flags="--headless=new --no-sandbox" --output=json,html --output-path="${path.join(qaDir, `${page.name}-mobile`)}" --only-categories=seo,accessibility,best-practices,performance --quiet`;
    execSync(cmd, { env, stdio: 'pipe', timeout: 90000 });

    const raw = readFileSync(outJson, 'utf-8');
    const parsed = JSON.parse(raw);
    const cats = parsed.categories || {};

    const perf = Math.round((cats.performance?.score || 0) * 100);
    const a11y = Math.round((cats.accessibility?.score || 0) * 100);
    const bp = Math.round((cats['best-practices']?.score || 0) * 100);
    const seo = Math.round((cats.seo?.score || 0) * 100);

    results.push({
      page: page.name,
      url,
      performance: perf,
      accessibility: a11y,
      bestPractices: bp,
      seo,
    });

    console.log(`   -> Performance: ${perf} | A11y: ${a11y} | Best Practices: ${bp} | SEO: ${seo}\n`);
  } catch (err) {
    console.error(`   [ERROR] Failed auditing ${page.name}:`, err.message);
  }
}

console.log('\n===============================================================');
console.log('   LIGHTHOUSE AUDIT SUMMARY TABLE');
console.log('===============================================================');
console.table(results);

const summaryPath = path.join(qaDir, 'lighthouse-summary.json');
writeFileSync(summaryPath, JSON.stringify(results, null, 2));
console.log(`Summary saved to ${summaryPath}`);
