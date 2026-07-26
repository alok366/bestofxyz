import { defineConfig, loadEnv } from 'vite';
import { resolve } from 'path';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), ['VITE_', 'MIX_']);

  return {
    root: resolve(__dirname),
    base: '/dist/',

    plugins: [
      react(),
    ],

    envPrefix: ['VITE_', 'MIX_'],

    define: {
      __ASSET_URL__: JSON.stringify(env.MIX_ASSET_URL || ''),
      global: 'window',
    },

    resolve: {
      alias: {
        '@app': resolve(__dirname, 'resources/js/app'),
        '@pages': resolve(__dirname, 'resources/js/pages'),
        '@widgets': resolve(__dirname, 'resources/js/widgets'),
        '@features': resolve(__dirname, 'resources/js/features'),
        '@entities': resolve(__dirname, 'resources/js/entities'),
        '@shared': resolve(__dirname, 'resources/js/shared'),
        '@Core': resolve(__dirname, 'resources/js/shared'),
        '@Components': resolve(__dirname, 'resources/js/shared/ui'),
      },
    },

    css: {
      preprocessorOptions: {
        less: {},
      },
    },

    publicDir: false,

    build: {
      outDir: 'public/dist',
      emptyOutDir: false,
      chunkSizeWarningLimit: 1100,
      manifest: true,
      target: 'es2022',
      sourcemap: mode === 'production' ? 'hidden' : true,

      rollupOptions: {
        onwarn(warning, defaultHandler) {
          if (warning.message?.includes("didn't resolve at build time")) return;
          if (warning.message?.includes('referenced in')) return;
          defaultHandler(warning);
        },
        input: {
          'fe-js/bundle': resolve(__dirname, 'resources/js/app/User/App.jsx'),
        },
        output: {
          entryFileNames: '[name].js',
          chunkFileNames: 'chunks/chunk-[hash].js',
          assetFileNames: (assetInfo) => {
            if (assetInfo.names?.[0]?.endsWith('.css')) {
              return '[name][extname]';
            }
            return 'assets/[name]-[hash][extname]';
          },
        },
      },
    },
  };
});