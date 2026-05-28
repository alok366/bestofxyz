import js from "@eslint/js";
import globals from "globals";

// ---------------------------------------------------------------------------
// Custom architectural rules (inline plugin)
// ---------------------------------------------------------------------------

/**
 * App-level modules that cannot import from each other.
 * Components/ and Core/ are shared — any module can import from them.
 */
const ISOLATED_MODULES = ['Admin', 'User'];

/**
 * Detect which isolated module a file belongs to based on its path.
 * Returns the module name or null if not in an isolated module.
 */
function getModule(filename) {
  const match = filename.match(/resources\/js\/(Admin|User)\//);
  return match ? match[1] : null;
}

const pkzPlugin = {
  meta: { name: 'eslint-plugin-xyz' },
  rules: {
    /**
     * No cross-module imports.
     * Admin/, User/, PublicWidget/, Core/ cannot import from each other.
     * Components/ (shared) can be imported by any module.
     */
    'no-cross-module-import': {
      meta: {
        type: 'problem',
        docs: { description: 'Disallow imports between isolated modules (Admin, User, PublicWidget, Core)' },
        schema: [],
      },
      create(context) {
        const filename = context.filename || context.getFilename();
        const sourceModule = getModule(filename);
        if (!sourceModule) return {};

        // Map aliases to app-level modules. Shared aliases (@Core, @Components) are omitted
        // — importing from shared modules is always allowed.
        const aliasModuleMap = {
          '@Tools': 'User', '@Utils': 'User',
        };

        return {
          ImportDeclaration(node) {
            const source = node.source.value;

            // Check alias-based imports first
            for (const [alias, mod] of Object.entries(aliasModuleMap)) {
              if (source === alias || source.startsWith(alias + '/')) {
                if (mod !== sourceModule) {
                  context.report({
                    node,
                    message: `Cross-module import: ${sourceModule}/ cannot import from ${mod}/ (via ${alias}). Use Components/ for shared code.`,
                  });
                }
                return; // Alias matched — skip path-based checks
              }
            }

            // Check for path-based cross-module imports
            for (const target of ISOLATED_MODULES) {
              if (target === sourceModule) continue;

              if (source.includes(`/${target}/`) || source.match(new RegExp(`^\\.\\.?/.*${target}/`))) {
                context.report({
                  node,
                  message: `Cross-module import: ${sourceModule}/ cannot import from ${target}/. Use Components/ for shared code.`,
                });
                return; // Report once per import
              }
            }
          },
        };
      },
    },

    /**
     * No jQuery in Admin/ files.
     */
    'no-jquery-in-admin': {
      meta: {
        type: 'problem',
        docs: { description: 'Disallow jQuery usage ($, jQuery) in Admin/ files' },
        schema: [],
      },
      create(context) {
        const filename = context.filename || context.getFilename();
        if (!filename.includes('/Admin/')) return {};

        return {
          // Catch: import $ from 'jquery', import jQuery from 'jquery'
          ImportDeclaration(node) {
            if (node.source.value === 'jquery') {
              context.report({ node, message: 'jQuery is not allowed in Admin/ files. Use vanilla JS or Preact.' });
            }
          },
          // Catch: $(...) or jQuery(...) as global usage
          MemberExpression(node) {
            if (node.object.type === 'Identifier' && (node.object.name === '$' || node.object.name === 'jQuery')) {
              context.report({ node, message: 'jQuery is not allowed in Admin/ files. Use vanilla JS or Preact.' });
            }
          },
          CallExpression(node) {
            if (node.callee.type === 'Identifier' && (node.callee.name === '$' || node.callee.name === 'jQuery')) {
              context.report({ node, message: 'jQuery is not allowed in Admin/ files. Use vanilla JS or Preact.' });
            }
          },
        };
      },
    },

    /**
     * No @jsx pragma in files covered by .babelrc overrides.
     */
    'no-pragma-in-override-dirs': {
      meta: {
        type: 'problem',
        docs: { description: 'Disallow /** @jsx h */ pragma in directories handled by .babelrc overrides' },
        schema: [],
      },
      create(context) {
        const filename = context.filename || context.getFilename();

        return {
          Program() {
            const sourceCode = context.sourceCode || context.getSourceCode();
            const comments = sourceCode.getAllComments();
            for (const comment of comments) {
              if (comment.type === 'Block' && /@jsx\s/.test(comment.value)) {
                context.report({
                  node: comment,
                  message: 'Remove @jsx pragma — .babelrc overrides handle JSX transform in this directory.',
                });
              }
            }
          },
        };
      },
    },

    /**
     * No cross-page component imports.
     * Files inside Pages/X/Components/ are scoped to that page.
     * Other pages (Pages/Y/) cannot import from Pages/X/Components/.
     * This also applies to Admin/Pages/.
     */
    'no-cross-page-import': {
      meta: {
        type: 'problem',
        docs: { description: 'Disallow importing page-scoped components from another page' },
        schema: [],
      },
      create(context) {
        const filename = context.filename || context.getFilename();

        // Only applies to files inside Pages/
        const pageMatch = filename.match(/Pages\/([^/]+)\//);
        if (!pageMatch) return {};

        const currentPage = pageMatch[1];

        return {
          ImportDeclaration(node) {
            const source = node.source.value;
            if (!source.startsWith('.')) return; // only relative imports

            // Resolve to detect cross-page imports like ../OtherPage/Components/Foo
            const otherPageMatch = source.match(/Pages\/([^/]+)\//);
            if (!otherPageMatch) return;

            const targetPage = otherPageMatch[1];
            if (targetPage !== currentPage) {
              context.report({
                node,
                message: `Cross-page import: Pages/${currentPage}/ cannot import from Pages/${targetPage}/. Page components are scoped. Use User/Components/ or Admin/Components/ for shared UI.`,
              });
            }
          },
        };
      },
    },

    /**
     * Logger.error required in catch blocks (warning).
     */
    'logger-error-in-catch': {
      meta: {
        type: 'suggestion',
        docs: { description: 'Require Logger.error() call in catch blocks' },
        schema: [],
      },
      create(context) {
        return {
          CatchClause(node) {
            const sourceCode = context.sourceCode || context.getSourceCode();
            const catchBody = sourceCode.getText(node.body);

            // Check if catch body contains Logger.error or Logger.fatal
            if (!catchBody.includes('Logger.error') && !catchBody.includes('Logger.fatal')) {
              context.report({
                node,
                message: 'catch blocks should include Logger.error() for observability. Import Logger from @Core/Logger.',
              });
            }
          },
        };
      },
    },
  },
};

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------

export default [
  js.configs.recommended,

  // Base config for all JS/JSX in resources/
  {
    files: ["resources/**/*.{js,jsx}"],
    plugins: {
      xyz: pkzPlugin,
    },
    linterOptions: {
      noInlineConfig: false,
    },
    settings: {
      'import/resolver': {
        alias: {
          map: [
            ['@Utils', './resources/js/User/Utils'],
          ],
          extensions: ['.js', '.jsx'],
        },
      },
    },
    languageOptions: {
      ecmaVersion: 2024,
      sourceType: "module",
      globals: {
        ...globals.browser,
        $: true,
        appStore: true,
        document: true,
        htmlDecode: true,
        process: true,
        window: true,
        Logger: true,
        __ASSET_URL__: true,
      },
      parserOptions: {
        ecmaFeatures: {
          jsx: true,
        },
      },
    },
    rules: {
      "arrow-body-style": ["error", "as-needed"],
      "import/prefer-default-export": "off",
      "no-console": "warn",
      "no-undef": "error",
      "no-unexpected-multiline": "error",
      "no-unused-vars": ["warn", { "varsIgnorePattern": "^jsxElem$|^[A-Z]|^h$|^Fragment$" }],
      "semi": ["error", "always"],

      // Architectural rules
      "xyz/no-cross-module-import": "error",
      "xyz/no-cross-page-import": "error",
      "xyz/no-jquery-in-admin": "error",
      "xyz/no-pragma-in-override-dirs": "error",
      "xyz/logger-error-in-catch": "warn",
    },
  },

  // Test files — relax rules
  {
    files: ["resources/js/**/*.test.{js,jsx}", "resources/js/**/__tests__/**/*.{js,jsx}"],
    languageOptions: {
      globals: {
        ...globals.node,
        describe: true,
        it: true,
        expect: true,
        beforeEach: true,
        afterEach: true,
        beforeAll: true,
        afterAll: true,
        vi: true,
      },
    },
    rules: {
      "xyz/logger-error-in-catch": "off",
      "no-console": "off",
    },
  },

  // Ignore build output and vendor
  {
    ignores: [
      "public/dist/**",
      "node_modules/**",
      "vendor/**",
      "storage/**",
      "docs/**",
    ],
  },
];
