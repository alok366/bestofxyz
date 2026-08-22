# Contributing to BestOfXYZ

Thank you for contributing to BestOfXYZ! To maintain code quality, security, and architectural consistency across the repository, please adhere to the following guidelines.

---

## 1. Branching Strategy & Workflow

We follow a structured Git branching model:

- **`main`**: Production branch. Must always be deployable and stable.
- **`staging`**: Pre-production integration branch for testing against realistic environments.
- **`feature/<name>`**: New user-facing features or major technical capabilities (e.g., `feature/category-filter-ui`).
- **`bugfix/<name>`**: Bug fixes for staging or development (e.g., `bugfix/jwt-expiry-handling`).
- **`hotfix/<name>`**: Urgent fixes targeted directly at `main`.

### Git Commit Guidelines

Commit messages must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<optional scope>): <description>

[optional body]

[optional footer(s)]
```

**Common Types:**
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes only
- `style`: Changes that do not affect the meaning of the code (formatting, white-space, CSS)
- `refactor`: A code change that neither fixes a bug nor adds a feature
- `perf`: A code change that improves performance
- `test`: Adding missing tests or correcting existing tests
- `chore`: Changes to the build process, dependency updates, or auxiliary tools

*Example:*
```bash
git commit -m "feat(auth): add refresh token rotation endpoint"
```

---

## 2. Architectural Conventions

BestOfXYZ is a **custom PHP framework on top of Illuminate components**, not standard Laravel. You must follow our layered architectural boundaries.

### 2.1 Backend Conventions
Follow the single direction of data flow:
$$\text{Route} \longrightarrow \text{Controller} \longrightarrow \text{Service} \longrightarrow \text{Repository} \longrightarrow \text{Model} \longrightarrow \text{Transformer}$$

- **Controllers** ([`system/App/Controllers`](system/App/Controllers)):
  - Must remain thin orchestrators.
  - No database queries or heavy business logic in controllers.
  - Return RFC 9457-style Problem Details on error.
- **Services** ([`system/App/Services`](system/App/Services)):
  - Contain pure business logic and transaction management.
  - Interact with repositories for data operations.
- **Repositories** ([`system/App/Repositories`](system/App/Repositories)):
  - Encapsulate Eloquent queries and persistence logic.
  - Support lookups by both numeric ID and public UUID/slug where applicable.
- **Models** ([`system/App/Models`](system/App/Models)):
  - Plain Eloquent models defining relations, casts, and guarded attributes.
- **Transformers** ([`system/App/Transformers`](system/App/Transformers)):
  - Transform internal database records into public API JSON structures.

### 2.2 Frontend Conventions (Feature-Sliced Design)
The React application in [`resources/js`](resources/js) follows strict [Feature-Sliced Design](https://feature-sliced.design/):

```
resources/js/
├── app/        # App entry points & composition roots (User, Admin, Login)
├── pages/      # Route-level screens (e.g., CategoryPage, ResourcePage)
├── widgets/    # Self-contained composite UI blocks (e.g., Header, Sidebar)
├── features/   # User actions & interactions (e.g., UpvoteButton, PostComment)
├── entities/   # Business models & scoped state (e.g., Resource, Category, User)
└── shared/     # Generic reusable components, Axios client, design tokens
```

**Import Rule:** Higher layers may only import from lower layers:
$$\text{app} \to \text{pages} \to \text{widgets} \to \text{features} \to \text{entities} \to \text{shared}$$

*Never import sideways within the same layer or upwards!*

---

## 3. Code Quality & Pre-Commit Checks

Before opening a pull request, ensure all linting and automated checks pass locally.

### 3.1 Backend Checks
```bash
# Run architecture scans (controller, service, transformer layer validation)
php artisan checks

# Run PHPUnit test suites
php artisan test
```

### 3.2 Frontend Checks
```bash
# Run ESLint on JavaScript/React code
npm run lint

# Run Vitest test suites
npm test

# Run Stylelint on LESS styles
npx stylelint "resources/less/**/*.less"
```

### 3.3 Git Hooks
We maintain git hooks in [`scripts/git-hooks`](scripts/git-hooks). To enable them:
```bash
git config core.hooksPath scripts/git-hooks
```

---

## 4. Documentation Requirements ("Docs as Code")

Documentation is treated as a first-class citizen in this codebase:

1. **Update API Specs**: If you add or modify an endpoint under [`routes/api`](routes/api), update [`docs/api/openapi.yaml`](docs/api/openapi.yaml).
2. **Update Architectural Docs / ADRs**: If introducing a new architectural pattern, add an ADR under [`docs/architecture/adr/`](docs/architecture/adr/).
3. **DocBlocks & Typing**: All new PHP methods must include descriptive DocBlocks (`@param`, `@return`, `@throws`). React components and Redux slices should have explicit JSDoc / TypeScript typing.

---

## 5. Pull Request (PR) Checklist

Before submitting your PR, verify:

- [ ] Branch is rebased onto the latest `main` (or `staging`).
- [ ] Code strictly follows backend and frontend architectural layers.
- [ ] All tests pass (`php artisan test` and `npm test`).
- [ ] Linter reports no errors (`npm run lint` and `php artisan checks`).
- [ ] Documentation updated (`docs/api/openapi.yaml`, `README.md`, or relevant developer guides).
- [ ] PR description includes a clear summary of changes, motivation, and manual test evidence.
