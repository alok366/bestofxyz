import js from "@eslint/js";
import globals from "globals";

// Feature-Sliced Design (FSD) Layer order (index = level, lower cannot import higher)
const FSD_LAYERS = ['shared', 'entities', 'features', 'widgets', 'pages', 'app'];

// Layers where cross-slice (sideways) imports should be blocked.
// 'shared' is excluded — its internal folders (ui/, api/, lib/, config/) are meant to reference each other.
const SLICE_ENFORCED_LAYERS = ['entities', 'features', 'widgets', 'pages'];

const fsdPlugin = {
  meta: { name: 'eslint-plugin-fsd' },
  rules: {
    'fsd-layer-imports': {
      meta: {
        type: 'problem',
        docs: { description: 'Enforce Feature-Sliced Design layer boundaries and slice isolation' },
        schema: [],
      },
      create(context) {
        const filename = (context.filename || context.getFilename() || '').replace(/\\/g, '/');

        const getLayerAndSlice = (pathStr) => {
          // Matches resources/js/<layer>/<slice>/...
          const fsMatch = pathStr.match(/resources\/js\/(app|pages|widgets|features|entities|shared)\/([^/]+)/);
          if (fsMatch) return { layer: fsMatch[1], slice: fsMatch[2] };

          // Matches alias form @widgets/<slice>/...
          const aliasMatch = pathStr.match(/^@(app|pages|widgets|features|entities|shared)\/([^/]+)/);
          if (aliasMatch) return { layer: aliasMatch[1], slice: aliasMatch[2] };

          return { layer: null, slice: null };
        };

        const current = getLayerAndSlice(filename);
        if (!current.layer) return {};

        const currentLayerIndex = FSD_LAYERS.indexOf(current.layer);

        return {
          ImportDeclaration(node) {
            const source = node.source.value;
            const target = getLayerAndSlice(source);
            if (!target.layer) return;

            const targetLayerIndex = FSD_LAYERS.indexOf(target.layer);

            // Rule 1: cannot import upward
            if (targetLayerIndex > currentLayerIndex) {
              context.report({
                node,
                message: `FSD Layer Violation: Layer '${current.layer}' cannot import from higher layer '${target.layer}'. Layer hierarchy is: app -> pages -> widgets -> features -> entities -> shared.`,
              });
              return;
            }

            // Rule 2: cannot import sideways — same layer, different slice
            if (
              targetLayerIndex === currentLayerIndex &&
              SLICE_ENFORCED_LAYERS.includes(current.layer) &&
              target.slice &&
              current.slice &&
              target.slice !== current.slice
            ) {
              context.report({
                node,
                message: `FSD Sideways Import Violation: '${current.layer}/${current.slice}' cannot import directly from sibling slice '${current.layer}/${target.slice}'. Same-layer slices must not import each other — compose them from a higher layer (e.g. a page or app) instead.`,
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