# Developer Local Environment Setup Guide

This guide walks you through setting up and running **BestOfXYZ** on your local development machine.

You can develop using **Docker (recommended)** or via a **native local host setup**.

---

## 1. Quick Start with Docker (Recommended)

The easiest and fastest way to get started is using the containerized development environment, which bundles PHP 8.4-FPM, Nginx 1.27, MariaDB 11.4 LTS, Redis 7, phpMyAdmin, and Node 22 (asset watcher).

### 1.1 Prerequisites
- **Docker** and **Docker Compose** (v2+)
- **Make** (`build-essential` on Linux/macOS)

### 1.2 One-Command Bootstrap
Run the automated initialization script via Make:
```bash
make init
```
This will automatically:
1. Validate system prerequisites.
2. Initialize `.env` from `.env.docker.example` if not present.
3. Verify directory permissions and configure portable relative storage symlinks (`public/storage -> ../storage`).
4. Build and start all Docker containers in background.
5. Wait for the database healthcheck.
6. Install Composer & NPM dependencies inside containers.
7. Run database migrations and optimize/clear caches.

### 1.3 Service URLs & Access Points
Once started, your local endpoints are:
- **Web Application**: `http://localhost:8000` (or `$FORWARD_HTTP_PORT`)
- **phpMyAdmin**: `http://localhost:8082` (or `$FORWARD_PMA_PORT`)
- **Documentation Portal**: `http://localhost:8000/docs/`
- **MariaDB Direct**: `127.0.0.1:3308` (User: `bestofxyz_user`, DB: `bestofxyz`)
- **Redis Direct**: `127.0.0.1:6380`

### 1.4 Daily Docker Commands
```bash
make up                 # Start containers and display access URLs
make down               # Stop and remove containers and network
make ps                 # Show container statuses and healthchecks
make logs [svc]         # Tail logs (e.g., make logs app, make logs web)
make bash               # Open interactive bash in PHP app container (as www-data)
make bash-node          # Open interactive bash in Node container
make artisan <cmd>      # Run Artisan command (e.g., make artisan migrate)
make composer <cmd>     # Run Composer inside container
make npm <cmd>          # Run npm inside container
make test               # Run PHPUnit backend tests inside container
make test-fe            # Run Vitest frontend tests inside container
make lint               # Run PHPCS and ESLint checks
make lint-fix           # Auto-fix code standard violations
make db-dump            # Export timestamped database dump to database/dumps/
make db-restore <file>  # Restore gzipped database dump into MariaDB
```

---

## 2. Native Host Setup (Alternative)

If you prefer running services directly on your host machine without Docker:

### 2.1 System Requirements

Ensure your machine has the following installed:
- **PHP**: `8.2` or later (8.4 recommended)
  - Required PHP Extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `json`, `redis`, `gd`, `zip`, `bcmath`, `pcntl`, `intl`
- **Composer**: `2.5.0` or higher
- **Node.js**: `20.x` or `22.x` (LTS)
- **npm**: `10.x` or higher
- **MySQL / MariaDB**: MySQL `8.0+` or MariaDB `10.11+` / `11.4`
- **Redis Server**: `6.0+`

---

## 3. Step-by-Step Native Installation

### 3.1 Repository Setup
```bash
# Clone the repository
git clone git@github.com:your-org/bestofxyz.git
cd bestofxyz

# Install PHP dependencies
composer install

# Install Frontend Node dependencies
npm install
```

### 3.2 Environment Configuration
```bash
# Copy sample configuration
cp .env.sample .env
```

Open `.env` and set your local environment settings:
```ini
APP_ENV="development"
APP_DEBUG="1"
APP_DEBUG_STRICT_MODE="1"

APP_URL="http://localhost:8000/"
APP_PATH="/var/www/bestofxyz/public_html/"

# Database Credentials
DB_HOST="127.0.0.1"
DB_USERNAME="your_mysql_user"
DB_PASSWORD="your_mysql_password"
DB_DATABASE="bestofxyz"

# Redis
REDIS_ENABLED="1"
CACHE_DRIVER="redis"
REDIS_HOST="127.0.0.1"
REDIS_PASSWORD=null

# Session and App Encryption
APP_KEY="your_32_character_app_key"
```

Generate the JWT authentication secret:
```bash
php artisan jwt:secret
```

### 3.3 Database Setup & Migrations
Create your local MySQL database:
```sql
CREATE DATABASE bestofxyz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run database migrations to initialize tables and default seeds:
```bash
php artisan migrate
```

---

## 4. Running the Application (Native)

### 4.1 Local Web Server
You can use Apache, Nginx, or the built-in PHP development server:

```bash
# Using PHP built-in server (from repo root)
php -S 127.0.0.1:8000 -t public
```

### 4.2 Frontend Development Server (Vite)
Start the Vite development server for Hot Module Replacement (HMR):
```bash
npm run dev
```

The frontend development server will run at `http://localhost:5173`.

### 4.3 Building Production Frontend Assets
```bash
npm run build
```

---

## 5. Verifying Installation

Run the project health and architecture checks:
```bash
php artisan checks
```

Run the backend and frontend test suites:
```bash
# Backend tests (PHPUnit)
php artisan test

# Frontend tests (Vitest)
npm test
```

Access the documentation portal in your browser:
```
http://localhost:8000/docs/
```
