#!/usr/bin/env bash
# ==============================================================================
# BestOfXYZ — One-Command Docker Environment Initialization
# ==============================================================================
set -eo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# ANSI Colors
CLR_RESET="\033[0m"
CLR_BOLD="\033[1m"
CLR_GREEN="\033[32m"
CLR_CYAN="\033[36m"
CLR_YELLOW="\033[33m"
CLR_RED="\033[31m"

log_info()    { echo -e "${CLR_CYAN}[INFO]${CLR_RESET} $1"; }
log_success() { echo -e "${CLR_GREEN}[SUCCESS]${CLR_RESET} $1"; }
log_warn()    { echo -e "${CLR_YELLOW}[WARNING]${CLR_RESET} $1"; }
log_error()   { echo -e "${CLR_RED}[ERROR]${CLR_RESET} $1"; }

echo -e "${CLR_BOLD}================================================================${CLR_RESET}"
echo -e "${CLR_BOLD}       BestOfXYZ — Docker Local Environment Initializer       ${CLR_RESET}"
echo -e "${CLR_BOLD}================================================================${CLR_RESET}"

# 1. Check prerequisites
log_info "Checking system prerequisites..."
if ! command -v docker &> /dev/null; then
    log_error "Docker is not installed or not available in PATH."
    exit 1
fi

if ! docker compose version &> /dev/null; then
    log_error "Docker Compose (v2+) is required but not found."
    exit 1
fi
log_success "Docker and Docker Compose are available."

# 2. Environment file setup
cd "$PROJECT_ROOT"
if [ ! -f .env ]; then
    if [ -f .env.docker.example ]; then
        log_warn ".env file not found. Creating from .env.docker.example..."
        cp .env.docker.example .env
        log_success "Created .env with Docker default configurations."
    elif [ -f .env.sample ]; then
        log_warn ".env file not found. Creating from .env.sample..."
        cp .env.sample .env
        log_success "Created .env from .env.sample."
    else
        log_error "No .env or .env.docker.example found to initialize."
        exit 1
    fi
else
    log_info ".env file already exists. Skipping creation."
fi

# 3. Directories & storage permissions
log_info "Verifying storage and log directory permissions..."
mkdir -p "$PROJECT_ROOT/storage/framework/cache" \
         "$PROJECT_ROOT/storage/framework/sessions" \
         "$PROJECT_ROOT/storage/framework/views" \
         "$PROJECT_ROOT/storage/logs" \
         "$PROJECT_ROOT/logs"

chmod -R 775 "$PROJECT_ROOT/storage" "$PROJECT_ROOT/logs" 2>/dev/null || true

# Ensure relative symlink: public/storage -> ../storage
log_info "Ensuring relative storage symlink: public/storage -> ../storage"
ln -sfn ../storage "$PROJECT_ROOT/public/storage"
log_success "Storage symlink verified."

# 4. Build and start containers
log_info "Building and starting Docker containers in background..."
docker compose up -d --build --remove-orphans
log_success "Containers started."

# 5. Wait for MariaDB to be healthy
log_info "Waiting for MariaDB database to become healthy..."
MAX_TRIES=30
TRIES=0
until docker compose exec -T db healthcheck.sh --connect &>/dev/null || [ $TRIES -eq $MAX_TRIES ]; do
    TRIES=$((TRIES + 1))
    sleep 2
done

if [ $TRIES -eq $MAX_TRIES ]; then
    log_warn "Database healthcheck timed out. Proceeding, but database might still be initializing."
else
    log_success "MariaDB is healthy and accepting connections."
fi

# 6. Install PHP Composer dependencies (if missing)
if [ ! -f "$PROJECT_ROOT/vendor/autoload.php" ]; then
    log_info "Installing PHP dependencies via Composer inside container..."
    docker compose exec -T app composer install --no-interaction --prefer-dist
    log_success "Composer dependencies installed."
else
    log_info "PHP dependencies already present in vendor/. Skipping full install."
fi

# 7. Install Node dependencies (if missing)
if [ ! -d "$PROJECT_ROOT/node_modules" ]; then
    log_info "Installing Node dependencies inside container..."
    docker compose exec -T node npm install
    log_success "Node dependencies installed."
else
    log_info "Node dependencies already present in node_modules/. Skipping npm install."
fi

# 8. Run database migrations
log_info "Running database migrations..."
docker compose exec -T app php artisan migrate --force
log_success "Database migrations executed."

# 9. Verify storage link via Artisan
log_info "Ensuring Artisan storage:link is configured..."
docker compose exec -T app php artisan storage:link || true

# 10. Clear application and configuration caches
log_info "Clearing application cache and sessions..."
docker compose exec -T app php artisan optimize:clear || true

# 11. Completion Banner
echo ""
echo -e "${CLR_GREEN}${CLR_BOLD}================================================================${CLR_RESET}"
echo -e "${CLR_GREEN}${CLR_BOLD}        Docker Environment Setup Complete!                      ${CLR_RESET}"
echo -e "${CLR_GREEN}${CLR_BOLD}================================================================${CLR_RESET}"
echo ""
echo -e "${CLR_BOLD}Active Access Points:${CLR_RESET}"
echo -e "  • Web Application:  ${CLR_CYAN}http://localhost:8000${CLR_RESET}"
echo -e "  • phpMyAdmin:       ${CLR_CYAN}http://localhost:8082${CLR_RESET}"
echo -e "  • MariaDB (Direct): ${CLR_CYAN}127.0.0.1:3308${CLR_RESET} (User: bestofxyz_user, Pass: secret)"
echo -e "  • Redis (Direct):   ${CLR_CYAN}127.0.0.1:6380${CLR_RESET}"
echo ""
echo -e "${CLR_BOLD}Useful Daily Commands:${CLR_RESET}"
echo -e "  • ${CLR_YELLOW}make artisan migrate${CLR_RESET}   - Run migrations"
echo -e "  • ${CLR_YELLOW}make artisan <cmd>${CLR_RESET}     - Run any Artisan command"
echo -e "  • ${CLR_YELLOW}make composer <cmd>${CLR_RESET}    - Run any Composer command"
echo -e "  • ${CLR_YELLOW}make test${CLR_RESET}            - Run backend tests"
echo -e "  • ${CLR_YELLOW}make test-fe${CLR_RESET}         - Run frontend tests (Vitest)"
echo -e "  • ${CLR_YELLOW}make bash${CLR_RESET}            - Open interactive shell in app container"
echo -e "  • ${CLR_YELLOW}make logs${CLR_RESET}            - Tail container logs"
echo -e "  • ${CLR_YELLOW}make down${CLR_RESET}            - Stop all containers"
echo ""
