import { defineConfig, loadEnv } from 'vite';
import { resolve } from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';

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
    resolve: {
      alias: {
        '@Core': resolve(__dirname, 'resources/js/Core'),
        '@User': resolve(__dirname, 'resources/js/User'),
        '@Components': resolve(__dirname, 'resources/js/Components'),
      },
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
              'fe-js/bundle': resolve(__dirname, 'resources/js/User/App.jsx')
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
  };
});
