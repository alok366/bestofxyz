# BestOfXYZ

> A modern, high-performance web platform and resource directory built with a custom PHP framework on top of Illuminate components, paired with a React + Redux Toolkit frontend structured with Feature-Sliced Design.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Directory Structure](#directory-structure)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Environment Configuration](#environment-configuration)
  - [Database Setup & Migrations](#database-setup--migrations)
- [Development Workflow](#development-workflow)
  - [Running the Frontend](#running-the-frontend)
  - [Artisan CLI Commands](#artisan-cli-commands)
  - [Running Tests](#running-tests)
  - [Code Quality & Linting](#code-quality--linting)
- [Documentation & Architecture](#documentation--architecture)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

**BestOfXYZ** is an enterprise-grade curated directory and resource platform featuring:
- **Fast, Layered Backend**: Custom lightweight PHP framework leveraging selected `Illuminate` (Laravel 11) packages (`database`, `routing`, `container`, `events`, `validation`, `http`).
- **Feature-Sliced React Frontend**: React 19 SPA built with Vite, Redux Toolkit, React Router v7, and Less modules.
- **Dual Authentication**: Stateful session-based auth for browser clients and stateless JWT authentication for mobile/API clients.
- **Modular Codebase**: Strict separation of concerns following `Route -> Controller -> Service -> Repository -> Model -> Transformer`.

---

## Tech Stack

### Backend
- **PHP**: 8.2+
- **Database ORM**: `illuminate/database` (Eloquent 11)
- **Routing & Container**: `illuminate/routing`, `illuminate/container`, `illuminate/events`
- **Validation**: `illuminate/validation` with centralized validation layer
- **Authentication**: `firebase/php-jwt` + session handling
- **Database**: MySQL 8.0+ / MariaDB
- **Caching & Queues**: Redis & local database drivers

### Frontend
- **Runtime & Bundler**: Vite 6, Node.js 20+
- **UI Library**: React 19, React DOM
- **State Management**: `@reduxjs/toolkit` 2.8+ & `react-redux`
- **Routing**: `react-router-dom` v7
- **HTTP Client**: Axios with centralized request/response interceptors
- **Styles**: Less with component-scoped modules and centralized design tokens

---

## Directory Structure

```
├── artisan                     # CLI entry point (Custom Console Registry)
├── config/                     # Application configurations (app, database, cache, mail, etc.)
├── cronjobs/                   # Scheduled cron processors
├── database/
│   ├── migrations/             # Timestamped migration files
│   └── seeders/                # Database seeders
├── docs/                       # Project documentation & browser-accessible portal
│   ├── api/                    # OpenAPI 3.0 specification & Swagger UI
│   ├── architecture/           # Architecture overviews & ADRs
│   └── developer/              # Developer guides & CLI reference
├── framework/                  # Custom framework kernel & bootstrap
│   ├── Bootstrap/              # App bootstrap loaders (web, cli, router, database)
│   ├── Console/                # Artisan command registry & command classes
│   └── Http/                   # Base controllers, responses, and middleware
├── guidelines/                 # PHP_CodeSniffer rules and architectural sniffs
├── instructions/               # AI & developer architectural context
│   ├── SKILL.md                # Framework rules & conventions
│   └── references/             # Backend & frontend in-depth guides
├── resources/
│   ├── js/                     # Feature-Sliced Design React source code
│   └── less/                   # Global Less styles & design tokens
├── routes/
│   ├── api/                    # API route definitions (public, user, admin, developer)
│   └── web/                    # Web route definitions (public, user, admin)
├── system/
│   └── App/                    # Core business logic (Controllers, Services, Repositories, Models)
├── tests/                      # Unit and Integration test suites (PHPUnit + Vitest)
└── workers/                    # Background worker scripts
```

---

## Getting Started

### Prerequisites

Ensure you have the following installed on your machine:
- **PHP**: `^8.2` with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `redis` extensions
- **Composer**: `^2.5`
- **Node.js**: `^20.0.0` or `^22.0.0`
- **npm**: `^10.0.0`
- **MySQL**: `^8.0`
- **Redis**: `^6.0` (optional for local file cache, recommended)

### Installation

1. **Clone the repository**:
   ```bash
   git clone git@github.com:your-org/bestofxyz.git
   cd bestofxyz
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

### Environment Configuration

1. Copy the sample environment configuration:
   ```bash
   cp .env.sample .env
   ```

2. Open `.env` and configure your database and app credentials:
   ```env
   APP_ENV="development"
   APP_URL="http://localhost:8000/"

   DB_HOST="127.0.0.1"
   DB_USERNAME="root"
   DB_PASSWORD="secret"
   DB_DATABASE="bestofxyz"

   REDIS_ENABLED="1"
   CACHE_DRIVER="redis"
   REDIS_HOST="127.0.0.1"
   ```

3. Generate a secure JWT secret key:
   ```bash
   php artisan jwt:secret
   ```

### Database Setup & Migrations

Run database migrations and seed default records:
```bash
# Run pending migrations
php artisan migrate

# Or run tests / check system status
php artisan checks
```

---

## Development Workflow

### Running the Frontend

```bash
# Start Vite development server with HMR
npm run dev

# Build production assets and shell templates
npm run build

# Watch mode for building bundle changes
npm run watch
```

### Artisan CLI Commands

BestOfXYZ features a custom `artisan` CLI with code generators and management tools:

```bash
# List all available commands
php artisan

# Code generators
php artisan make:controller PostController
php artisan make:service PostService
php artisan make:repository PostRepository
php artisan make:model Post
php artisan make:transformer PostTransformer
php artisan make:module Blog

# Database & Storage
php artisan migrate                     # Run pending migrations
php artisan make:migration create_posts  # Create a new migration file
php artisan storage:link                # Link public storage directory

# Maintenance & Documentation
php artisan optimize:clear              # Clear cached views and compiled files
php artisan docs                        # Generate PHP API documentation
```

### Running Tests

```bash
# Run backend PHPUnit test suites
php artisan test
# or directly via PHPUnit
./vendor/bin/phpunit

# Run frontend Vitest test suite
npm test

# Run Vitest in interactive watch mode
npm run test:watch
```

### Code Quality & Linting

```bash
# Lint JavaScript/React codebase
npm run lint

# Automatically fix JS/React lint errors
npm run lint:fix

# Run architectural integrity checks
php artisan checks
```

---

## Documentation & Architecture

Comprehensive documentation is available within the repository:

- **[Interactive API Documentation](file:///var/www/bestofxyz/public_html/docs/api/index.html)**: OpenAPI 3.0 specification & interactive UI (`docs/api/`).
- **[Architecture Overview](file:///var/www/bestofxyz/public_html/docs/architecture/overview.md)**: Request lifecycle, container singletons, and layered boundaries.
- **[Architecture Decision Records (ADRs)](file:///var/www/bestofxyz/public_html/docs/architecture/adr/)**: Records of key technical decisions.
- **[Developer Setup Guide](file:///var/www/bestofxyz/public_html/docs/developer/setup-guide.md)**: Detailed environment configuration and services runbook.
- **[CLI Reference](file:///var/www/bestofxyz/public_html/docs/developer/cli-reference.md)**: Comprehensive guide for every `php artisan` command.
- **[Background Jobs & Workers](file:///var/www/bestofxyz/public_html/docs/developer/background-jobs-and-workers.md)**: Worker daemon operations and cron configuration.
- **[Framework Instructions (`SKILL.md`)](file:///var/www/bestofxyz/public_html/instructions/SKILL.md)**: AI agent and developer architectural guide.

---

## Contributing

Please read **[CONTRIBUTING.md](file:///var/www/bestofxyz/public_html/CONTRIBUTING.md)** for details on our code of conduct, branch conventions, and the process for submitting pull requests.

---

## License

Proprietary & Confidential. All rights reserved.
