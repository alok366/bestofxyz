// Stylelint config for the new LESS layer (Core/Components/Pages).
// Legacy folders listed in .stylelintignore are excluded wholesale — they
// predate the zl- architecture and will be phased out, not lint-cleaned.

module.exports = {
    extends: ['stylelint-config-standard-less'],
    customSyntax: 'postcss-less',
    rules: {
        // Hard rules from .claude/plans/project_less_architecture.md
        'declaration-no-important': true,
        'selector-max-id': 0,
        'max-nesting-depth': 3,
        'selector-max-compound-selectors': 4,
        'no-duplicate-selectors': true,
        'no-descending-specificity': null,

        // `bmux-*` ids/classes are JS hooks, not style targets. Styling
        // them couples LESS to the JS contract and blocks refactors.
        'selector-disallowed-list': [
            [/\.bmux-/, /#bmux-/],
            { message: 'bmux-* identifiers are JS hooks, not style targets — scope CSS off a dedicated class' },
        ],

        // zl- prefix for keyframes (Core/utilities layer)
        'keyframes-name-pattern': '^zl-[a-z][a-zA-Z0-9-]*$',

        // Class naming:
        //  - utilities & tokens: zl- prefix (.zl-btn, .zl-fade-up, .zl-btn--primary)
        //  - components (target): PascalCase BEM (.ActionTile, .ActionTile__btn, .ActionTile--active)
        //  - components (transitional): kebab-case still allowed for in-flight files (.template-grid__item)
        //  - Bootstrap composition classes allowed (.btn, .btn-primary) during jQuery phase-out
        'selector-class-pattern': [
            '^(zl-[a-z][a-zA-Z0-9-]*(__[a-z][a-zA-Z0-9-]*)?(--[a-z][a-zA-Z0-9-]*)?|[A-Z][a-zA-Z0-9]*(__[a-z][a-zA-Z0-9-]*)?(--[a-z][a-zA-Z0-9-]*)?|[a-z][a-z0-9-]*(__[a-z][a-zA-Z0-9-]*)?(--[a-z][a-zA-Z0-9-]*)?|is-[a-z][a-zA-Z0-9-]*|has-[a-z][a-zA-Z0-9-]*)$',
            {
                message: 'Class must be zl-kebab (utility/token), PascalCase BEM (component), or kebab-case BEM (transitional)',
                resolveNestedSelectors: true,
            },
        ],

        // Property order was previously enforced via stylelint-order. Dropped:
        // it caught zero bugs, fought unlisted properties (inset, white-space,
        // border-color) with pedantic alphabetical warnings, and cost real
        // editing time. The hard architecture rules above carry the weight.

        // Relax noisy style-only rules — keep the hard architecture rules strict
        'color-hex-length': null,
        'color-function-notation': null,
        'alpha-value-notation': null,
        'hue-degree-notation': null,
        'media-feature-range-notation': null,
        'declaration-empty-line-before': null,
        'value-no-vendor-prefix': null,
        'property-no-vendor-prefix': null,
        'at-rule-no-unknown': null,
        'less/no-duplicate-variables': true,
    },
    overrides: [
        {
            // Overwrite/ exists to beat third-party CSS (Minia, Bootstrap).
            // `!important` is the whole point — the upstream rules we're
            // fighting ship with `!important` themselves, so specificity
            // alone can't win. Allowed here and only here.
            files: ['resources/less/Overwrite/**/*.less'],
            rules: {
                'declaration-no-important': null,
            },
        },
    ],
};
