const puppeteer = require('puppeteer-core');
(async () => {
  const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH, headless: 'new', ignoreHTTPSErrors: true, args: ['--ignore-certificate-errors', '--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1440, height: 900 });
  await page.goto('https://ese-latam-2026.local/catalogo-de-productos/', { waitUntil: 'load', timeout: 60000 });
  await new Promise(r => setTimeout(r, 1200));
  const DIR = 'C:/Users/Usuario/AppData/Local/Temp/claude/c--Users-Usuario-Local-Sites-ese-latam-2026-app-public-wp-content-themes-ese-latam/4058604f-9103-45b2-bb7e-7f49714ab214/scratchpad';
  await page.screenshot({ path: `${DIR}/current-check.png` });

  const rects = await page.evaluate(() => {
    const r = (el) => { if (!el) return null; const b = el.getBoundingClientRect(); return { top: Math.round(b.top), bottom: Math.round(b.bottom), left: Math.round(b.left), right: Math.round(b.right), width: Math.round(b.width), height: Math.round(b.height) }; };
    return {
      pedestal: r(document.querySelector('.catalogo-banner__pedestal')),
      pedestalImg: r(document.querySelector('.catalogo-banner__pedestal img')),
      main: r(document.querySelector('.catalogo-banner__product--main')),
      mainImg: r(document.querySelector('.catalogo-banner__product--main img')),
      scene: r(document.querySelector('.catalogo-banner__scene')),
    };
  });
  console.log(JSON.stringify(rects, null, 1));
  await browser.close();
})();
