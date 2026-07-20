import fs from 'fs'

const manifest = JSON.parse(
  fs.readFileSync('public/dist/.vite/manifest.json', 'utf-8')
)

const entry = manifest['resources/js/User/App.jsx']

if (!entry) {
  throw new Error('Entry not found in manifest. Check the key matches your Vite input path.')
}

const tags = [
  `<script type="module" src="/dist/${entry.file}"></script>`,
  ...(entry.css || []).map(css => `<link rel="stylesheet" href="/dist/${css}">`)
].join('\n  ')

const templatePath = 'resources/views/User/app-shell.template.html'
const outputPath = 'resources/views/User/app-shell.html'

const template = fs.readFileSync(templatePath, 'utf-8')
fs.writeFileSync(outputPath, template.replace('<!--VITE_ASSETS-->', tags))

console.log(`✔ app-shell.html generated → ${outputPath}`)