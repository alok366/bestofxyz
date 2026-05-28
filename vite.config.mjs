import { defineConfig, loadEnv } from 'vite';
import { resolve } from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import { readFileSync } from 'fs';
import babel from '@rollup/plugin-babel';

/**
 * Custom plugin: imports .html files as raw strings.
 * Replaces Webpack's html-loader behavior for builder templates.
 */
function htmlRawPlugin() {
  return {
    name: 'html-raw-import',
    enforce: 'pre',
    resolveId(source, importer) {
      if (source.endsWith('.html') && importer && !source.startsWith('\0')) {
        const resolved = resolve(importer, '..', source);
        // Use .js extension in virtual ID to prevent Vite's HTML plugin from triggering
        return '\0html-raw:' + resolved.replace(/\.html$/, '.rawhtml.js');
      }
    },
    load(id) {
      if (id.startsWith('\0html-raw:')) {
        const filePath = id.slice('\0html-raw:'.length).replace(/\.rawhtml\.js$/, '.html');
        const content = readFileSync(filePath, 'utf-8');
        return `export default ${JSON.stringify(content)};`;
      }
    },
  };
}


export default defineConfig(({ mode }) => {
  // Load env vars so __ASSET_URL__ define works
  const env = loadEnv(mode, process.cwd(), ['VITE_', 'MIX_']);

  return {
    root: process.cwd(),
    base: '/dist/',


    // Expose MIX_* vars to import.meta.env without renaming .env files
    envPrefix: ['VITE_', 'MIX_'],

    // Replaces webpack DefinePlugin — 21 files use __ASSET_URL__ as a global
    define: {
      __ASSET_URL__: JSON.stringify(env.MIX_ASSET_URL || ''),
      // Webpack polyfilled Node's `global` as `window` — Vite doesn't
      global: 'window',
    },

    css: {
      preprocessorOptions: {
        less: {
          // Vite doesn't rewrite CSS url() by default — matches processCssUrls: false
        },
      },
    },

    // Disable public dir copying — our output IS inside public/
    publicDir: false,

    build: {
      outDir: 'public/dist',
      emptyOutDir: false,
      // Suppress large chunk warnings — known large vendor chunks (CKEditor, etc.)
      chunkSizeWarningLimit: 1100,
      manifest: true,
      target: 'es2022',
      sourcemap: mode === 'production' ? 'hidden' : true,

      rollupOptions: {
        onwarn(warning, defaultHandler) {
          // Suppress "didn't resolve at build time" for CSS url() references
          // These are runtime-resolved paths to images/fonts served by PHP
          if (warning.message && warning.message.includes('didn\'t resolve at build time')) return;
          if (warning.message && warning.message.includes('referenced in')) return;
          defaultHandler(warning);
        },
        input: {
              'login-bundle': resolve(__dirname, 'resources/js/User/Login.js'),
              'fe-js/bundle': resolve(__dirname, 'resources/js/User/App.js'),
              'be-js/bundle': resolve(__dirname, 'resources/js/Admin/App.js'),
            },

        output: {
          entryFileNames: '[name].js',
          chunkFileNames: 'chunks/chunk-[hash].js',
          assetFileNames: (assetInfo) => {
            if (assetInfo.names && assetInfo.names[0] && assetInfo.names[0].endsWith('.css')) {
              return '[name][extname]';
            }
            return 'assets/[name]-[hash][extname]';
          },
        },
      },
    },

    // Let Babel handle JSX — preserves .babelrc dual pragma
    // (jsx-no-react default + Preact h overrides for 14 directories)
    esbuild: {
      jsx: 'preserve',
    },

    plugins: [
      // Import .html files as raw strings (replaces Webpack html-loader)
      htmlRawPlugin(),

      // Babel for JSX transformation — respects .babelrc overrides
      babel({
        babelHelpers: 'bundled',
        extensions: ['.js', '.jsx'],
        exclude: 'node_modules/**',
      }),

      // Font copying — replaces mix.copy() calls
      viteStaticCopy({
        targets: [
          { src: 'resources/less/fonts/*', dest: 'fonts' },
        ],
      }),

    ],
  };
});
