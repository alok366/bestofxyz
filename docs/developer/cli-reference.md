# Artisan CLI Reference

BestOfXYZ includes a lightweight command-line interface via `php artisan` to assist with development, database migrations, code generation, testing, and background workers.

---

## Command Overview

```bash
php artisan <command> [arguments] [options]
```

To view all available commands, run:
```bash
php artisan
```

---

## 1. Code Generators

### `make:controller <name>`
Generates a new Controller class in `system/App/Controllers/`.
```bash
php artisan make:controller ResourceController
```

### `make:service <name>`
Generates a new Service class in `system/App/Services/`.
```bash
php artisan make:service ResourceService
```

### `make:repository <name>`
Generates a new Repository interface and Eloquent implementation in `system/App/Repositories/`.
```bash
php artisan make:repository ResourceRepository
```

### `make:model <name>`
Generates a new Eloquent Model in `system/App/Models/`.
```bash
php artisan make:model Resource
```

### `make:transformer <name>`
Generates a new Transformer class in `system/App/Transformers/` for API output serialization.
```bash
php artisan make:transformer ResourceTransformer
```

### `make:module <name>`
Scaffolds an entire modular domain unit (Model, Controller, Service, Repository, Transformer, Migration) in one command:
```bash
php artisan make:module Bookmark
```

### `make:migration <name>`
Creates a new timestamped migration file in `database/migrations/`.
```bash
php artisan make:migration create_bookmarks_table
```

---

## 2. Database & Storage

### `migrate`
Runs all pending database migrations against the configured database.
```bash
php artisan migrate
```

### `storage:link`
Creates a symbolic link from `storage/app/public` to `public/storage` to make uploads publicly accessible.
```bash
php artisan storage:link
```

---

## 3. Testing & Code Quality

### `checks`
Runs architectural integrity checks, verifying that controllers, services, and transformers follow repository layer boundaries.
```bash
php artisan checks
```

### `test`
Runs PHPUnit test suites.
```bash
php artisan test
```

### `test:list`
Lists all discovered unit and integration tests across the project.
```bash
php artisan test:list
```

---

## 5. Security & Maintenance

### `jwt:secret`
Generates a cryptographically secure 256-bit base64 secret key and updates the `JWT_SECRET` in `.env`.
```bash
php artisan jwt:secret
```

### `cache:sessions`
Flushes or manages session storage caches in Redis / files.
```bash
php artisan cache:sessions
```

### `optimize:clear`
Clears cached compiled classes, view caches, and temporary application runtime caches.
```bash
php artisan optimize:clear
```

### `docs`
Generates offline PHP API documentation using phpDocumentor to `docs/generated/php/`.
```bash
php artisan docs
```
