import { defineConfig } from 'vitest/config';
import { resolve } from 'path';

export default defineConfig({
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
  esbuild: {
    jsx: 'automatic',
    jsxImportSource: 'react',
  },
  define: {
    __ASSET_URL__: JSON.stringify(''),
  },
  test: {
    environment: 'happy-dom',
    globals: true,
  },
});
