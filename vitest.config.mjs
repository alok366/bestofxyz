import { defineConfig } from 'vitest/config';
import { resolve } from 'path';
import { transformWithEsbuild } from 'vite';

/**
 * Plugin: treat .js files as JSX so esbuild handles the transform.
 * Without this, Vite 6's native rollup parser rejects JSX in .js files.
 */
function jsxInJs() {
  return {
    name: 'jsx-in-js',
    enforce: 'pre',
    async transform(code, id) {
      if (!id.match(/resources\/js\/.*\.jsx?$/) || id.includes('node_modules')) return;
      if (!code.includes('<') && !code.includes('/>')) return;

      return transformWithEsbuild(code, id + '?jsx', {
        jsx: 'automatic',
        jsxImportSource: 'react',
        loader: 'jsx',
      });
    },
  };
}

export default defineConfig({
  plugins: [jsxInJs()],
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
    include: ['resources/js/tests/**/*.test.{js,jsx}'],
    setupFiles: ['resources/js/tests/setup.js'],
  },
});
