# System Architecture Overview

This document provides a comprehensive overview of the architecture of **BestOfXYZ**, describing the backend request lifecycle, service boundaries, frontend design, and asynchronous workers.

---

## 1. High-Level Architecture

BestOfXYZ is architected as a modular, high-throughput application with decoupled backend services and a reactive single-page frontend.

```
                    ┌─────────────────────────┐
                    │     Client Browsers     │
                    │  (React 19 / Vite SPA)  │
                    └────────────┬────────────┘
                                 │ HTTP / JSON
                                 ▼
                    ┌─────────────────────────┐
                    │     Web Server Entry    │
                    │ (Apache/Nginx/index.php)│
                    └────────────┬────────────┘
                                 │
                   ┌─────────────┴─────────────┐
                   ▼                           ▼
       ┌──────────────────────┐    ┌──────────────────────┐
       │   Frontend Assets    │    │  Framework Bootstrap │
       │  (/public/build/...) │    │  (Container, Router) │
       └──────────────────────┘    └───────────┬──────────┘
                                               │
                                               ▼
                                   ┌──────────────────────┐
                                   │  Middleware Pipeline │
                                   │ (Auth, CORS, CSRF)   │
                                   └───────────┬──────────┘
                                               │
                                               ▼
                                   ┌──────────────────────┐
                                   │      Controller      │
                                   └───────────┬──────────┘
                                               │
                                               ▼
                                   ┌──────────────────────┐
                                   │    Service Layer     │
                                   └───────────┬──────────┘
                                               │
                                               ▼
                                   ┌──────────────────────┐
                                   │  Repository / Model  │
                                   └───────────┬──────────┘
                                               │
                                               ▼
                                   ┌──────────────────────┐
                                   │ MySQL / Redis Store  │
                                   └──────────────────────┘
```

---

## 2. Backend Architecture

### 2.1 Framework Philosophy
Instead of bundling a heavy full-stack framework with unused features, BestOfXYZ uses a **curated PHP framework** powered by selected, decoupled components from `illuminate/*` (Laravel 11):
- **`illuminate/container`**: Inversion of control & dependency injection container.
- **`illuminate/routing` & `illuminate/http`**: Fast, flexible request routing and PSR-7/Symfony HTTP abstractions.
- **`illuminate/database`**: Eloquent ORM and Fluent query builder.
- **`illuminate/events`**: Application event dispatcher.
- **`illuminate/validation`**: Request data validation engine.

### 2.2 Layered Application Flow

All backend business features live in [`system/App`](../../system/App) and adhere to a strict unidirectional flow:

```
[Request] ──► Controller ──► Service ──► Repository ──► Eloquent Model ──► Database
                  │                                         │
                  ▼                                         ▼
            Transformer ◄─────────────────────────── [Result Data]
                  │
                  ▼
              [Response]
```

1. **Routing (`routes/`)**:
   - Split logically into `routes/api/` (REST/JSON endpoints) and `routes/web/` (HTML shells).
2. **Controllers (`system/App/Controllers/`)**:
   - Thin orchestration layer. Handles HTTP status codes, deserializes parameters, and invokes the appropriate service.
3. **Services (`system/App/Services/`)**:
   - Domain business logic, transaction handling, email triggers, cache coordination.
4. **Repositories (`system/App/Repositories/`)**:
   - Data access abstraction over Eloquent models. Handles query construction, pagination, and multi-field key lookups.
5. **Models (`system/App/Models/`)**:
   - Eloquent models representing database tables with explicit relations, scopes, and casts.
6. **Transformers (`system/App/Transformers/`)**:
   - Formats internal data models into clean public JSON payloads (protecting internal columns).
7. **Validation (`system/App/Validation/`)**:
   - Centralized validation rules and custom sanitization.

### 2.3 Error Handling & RFC 9457
All API failure responses use RFC 9457 Problem Details format:
```json
{
  "type": "https://api.bestofxyz.com/errors/not-found",
  "title": "Resource Not Found",
  "status": 404,
  "detail": "The requested resource 'ai-tools-2026' was not found.",
  "instance": "/api/categories/ai/resources/ai-tools-2026"
}
```

---

## 3. Frontend Architecture (Feature-Sliced Design)

The frontend is located in [`resources/js`](../../resources/js) and structured using **Feature-Sliced Design (FSD)**:

```
resources/js/
├── app/                  # Application initialization, root providers, router
│   ├── User/             # Main user portal bundle
│   ├── Admin/            # Admin management dashboard bundle
│   └── Login/            # Auth portal bundle
├── pages/                # High-level page components matching URL routes
├── widgets/              # Large composite UI components (Navigation, Header, Footer)
├── features/             # Interactive user capabilities (Upvote, Comment, Filter)
├── entities/             # Domain business objects (Resource, User, Category, Tag)
└── shared/               # Reusable primitives, API client, UI components, helpers
```

### Key Frontend Rules:
1. **Unidirectional Import Rule**: Modules may only import from layers strictly below them (`app -> pages -> widgets -> features -> entities -> shared`).
2. **Colocated State**: Entity-specific Redux state lives in `entities/<name>/model/`; feature-specific state lives in `features/<name>/model/`.
3. **Shared HTTP Client**: All HTTP requests use `shared/api/Http.jsx` with automatic CSRF management and interceptors.

---

## 4. Background Workers & Asynchronous Tasks

- **Queue Workers (`framework/Console/Commands/WorkerCommand.php`)**:
  - Run continuous worker processes for processing background jobs, notifications, and async exports.
- **Scheduled Cronjobs (`cronjobs/`)**:
  - Standalone cron scripts designed to run via crontab for periodic updates (e.g. login stats calculation, ranking snapshots).
