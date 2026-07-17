import { defineConfig } from 'vitest/config';

export default defineConfig({
  resolve: {
    alias: {
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
