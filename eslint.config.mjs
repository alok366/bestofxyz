import js from "@eslint/js";
import globals from "globals";

// Feature-Sliced Design (FSD) Layer order (index = level, lower cannot import higher)
const FSD_LAYERS = ['shared', 'entities', 'features', 'widgets', 'pages', 'app'];

const fsdPlugin = {
  meta: { name: 'eslint-plugin-fsd' },
  rules: {
    'fsd-layer-imports': {
      meta: {
        type: 'problem',
        docs: { description: 'Enforce Feature-Sliced Design layer boundaries (app -> pages -> widgets -> features -> entities -> shared)' },
        schema: [],
      },
      create(context) {
        const filename = (context.filename || context.getFilename() || '').replace(/\\/g, '/');

        const getLayer = (pathStr) => {
          const match = pathStr.match(/resources\/js\/(app|pages|widgets|features|entities|shared)\//);
          if (match) return match[1];
          if (pathStr.startsWith('@app')) return 'app';
          if (pathStr.startsWith('@pages')) return 'pages';
          if (pathStr.startsWith('@widgets')) return 'widgets';
          if (pathStr.startsWith('@features')) return 'features';
          if (pathStr.startsWith('@entities')) return 'entities';
          if (pathStr.startsWith('@shared')) return 'shared';
          return null;
        };

        const currentLayer = getLayer(filename);
        if (!currentLayer) return {};

        const currentLayerIndex = FSD_LAYERS.indexOf(currentLayer);

        return {
          ImportDeclaration(node) {
            const source = node.source.value;
            const targetLayer = getLayer(source);

            if (!targetLayer) return;

            const targetLayerIndex = FSD_LAYERS.indexOf(targetLayer);

            // Cannot import upward or sideways from higher layer
            if (targetLayerIndex > currentLayerIndex) {
              context.report({
                node,
                message: `FSD Layer Violation: Layer '${currentLayer}' cannot import from higher layer '${targetLayer}'. Layer hierarchy is: app -> pages -> widgets -> features -> entities -> shared.`,
              });
            }
          },
        };
      },
    },
  },
};

export default [
  js.configs.recommended,

  {
    files: ["resources/**/*.{js,jsx}"],
    plugins: {
      fsd: fsdPlugin,
    },
    languageOptions: {
      ecmaVersion: 2024,
      sourceType: "module",
      globals: {
        ...globals.browser,
        $: true,
        document: true,
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
      "no-console": "warn",
      "no-undef": "error",
      "no-unused-vars": ["warn", { "varsIgnorePattern": "^jsxElem$|^[A-Z]|^h$|^Fragment$" }],
      "semi": ["error", "always"],
      "fsd/fsd-layer-imports": "error",
    },
  },

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
