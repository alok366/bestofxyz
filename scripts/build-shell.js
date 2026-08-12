import fs from 'fs';
import { STORAGE_KEY } from '../resources/js/shared/lib/theme/model/themeUtils.js';

const manifest = JSON.parse(
  fs.readFileSync('public/dist/.vite/manifest.json', 'utf-8')
);

const entry = manifest['resources/js/app/User/App.jsx'];

if (!entry) {
  throw new Error('Entry not found in manifest. Check the key matches your Vite input path.');
}

const tags = [
  `<script type="module" src="/dist/${entry.file}"></script>`,
  ...(entry.css || []).map((css) => `<link rel="stylesheet" href="/dist/${css}">`),
].join('\n  ');

const antiFlashScript = `<script>
    (function() {
      try {
        var key = '${STORAGE_KEY}';
        var pref = localStorage.getItem(key) || 'system';
        var isDark = pref === 'dark' || (pref === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        var theme = isDark ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.style.colorScheme = theme;
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', isDark ? '#0d1117' : '#ffffff');
      } catch (e) {}
    })();
  </script>`;

const templatePath = 'resources/views/User/app-shell.template.html';
const outputPath = 'resources/views/User/app-shell.html';

const template = fs.readFileSync(templatePath, 'utf-8');
const processed = template
  .replace('<!--THEME_ANTI_FLASH-->', antiFlashScript)
  .replace('<!--VITE_ASSETS-->', tags);

fs.writeFileSync(outputPath, processed);

console.log(`✔ app-shell.html generated → ${outputPath}`);