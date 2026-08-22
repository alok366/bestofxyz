# Developer Local Environment Setup Guide

This guide walks you through setting up and running **BestOfXYZ** on your local development machine.

---

## 1. System Requirements

Ensure your machine has the following installed:
- **PHP**: `8.2` or later
  - Required PHP Extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `json`, `redis`, `gd`, `zip`
- **Composer**: `2.5.0` or higher
- **Node.js**: `20.x` or `22.x` (LTS)
- **npm**: `10.x` or higher
- **MySQL / MariaDB**: MySQL `8.0+`
- **Redis Server**: `6.0+`

---

## 2. Step-by-Step Installation

### 2.1 Repository Setup
```bash
# Clone the repository
git clone git@github.com:your-org/bestofxyz.git
cd bestofxyz

# Install PHP dependencies
composer install

# Install Frontend Node dependencies
npm install
```

### 2.2 Environment Configuration
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

### 2.3 Database Setup & Migrations
Create your local MySQL database:
```sql
CREATE DATABASE bestofxyz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run database migrations to initialize tables and default seeds:
```bash
php artisan migrate
```

---

## 3. Running the Application

### 3.1 Local Web Server
You can use Apache, Nginx, or the built-in PHP development server:

```bash
# Using PHP built-in server (from repo root)
php -S 127.0.0.1:8000 -t public
```

### 3.2 Frontend Development Server (Vite)
Start the Vite development server for Hot Module Replacement (HMR):
```bash
npm run dev
```

The frontend development server will run at `http://localhost:5173`.

### 3.3 Building Production Frontend Assets
```bash
npm run build
```

---

## 4. Verifying Installation

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
