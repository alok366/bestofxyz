# ==============================================================================
# BestOfXYZ — Developer Makefile & Docker Shortcuts
# ==============================================================================

SHELL := /bin/bash
.DEFAULT_GOAL := help
.PHONY: $(MAKECMDGOALS) help

# ------------------------------------------------------------------------------
# Dynamic Argument Forwarding (e.g., 'make artisan migrate', 'make logs app')
# ------------------------------------------------------------------------------
ARGS_TARGETS := artisan composer npm logs restart db-restore test
ifeq ($(firstword $(MAKECMDGOALS)),$(filter $(firstword $(MAKECMDGOALS)),$(ARGS_TARGETS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
endif

# Colors for help output
CLR_RESET   := \033[0m
CLR_BOLD    := \033[1m
CLR_GREEN   := \033[32m
CLR_CYAN    := \033[36m
CLR_YELLOW  := \033[33m

# Port discovery from .env with fallback defaults
HTTP_PORT  ?= $(shell grep -E '^FORWARD_HTTP_PORT=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
HTTP_PORT  := $(if $(HTTP_PORT),$(HTTP_PORT),8000)

PMA_PORT   ?= $(shell grep -E '^FORWARD_PMA_PORT=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
PMA_PORT   := $(if $(PMA_PORT),$(PMA_PORT),8082)

DB_PORT    ?= $(shell grep -E '^FORWARD_DB_PORT=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
DB_PORT    := $(if $(DB_PORT),$(DB_PORT),3308)

REDIS_PORT ?= $(shell grep -E '^FORWARD_REDIS_PORT=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
REDIS_PORT := $(if $(REDIS_PORT),$(REDIS_PORT),6380)

DB_NAME    ?= $(shell grep -E '^DB_DATABASE=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
DB_NAME    := $(if $(DB_NAME),$(DB_NAME),bestofxyz)

DB_USER    ?= $(shell grep -E '^DB_USERNAME=' .env 2>/dev/null | cut -d= -f2 | tr -d ' "\r')
DB_USER    := $(if $(DB_USER),$(DB_USER),bestofxyz_user)

# ------------------------------------------------------------------------------
# Help Screen
# ------------------------------------------------------------------------------
help:
	@echo ""
	@echo -e "${CLR_BOLD}BestOfXYZ — Docker Developer CLI Commands${CLR_RESET}"
	@echo ""
	@echo -e "${CLR_CYAN}Environment Lifecycle:${CLR_RESET}"
	@echo -e "  ${CLR_YELLOW}make init${CLR_RESET}              Run full 1-command project onboarding/bootstrap"
	@echo -e "  ${CLR_YELLOW}make up${CLR_RESET}                Start all containers in background and show URLs"
	@echo -e "  ${CLR_YELLOW}make down${CLR_RESET}              Stop and remove containers and network"
	@echo -e "  ${CLR_YELLOW}make stop${CLR_RESET}              Stop containers without removing them"
	@echo -e "  ${CLR_YELLOW}make restart [svc]${CLR_RESET}     Restart all or a specific container (e.g. make restart app)"
	@echo -e "  ${CLR_YELLOW}make build${CLR_RESET}             Rebuild Docker images"
	@echo -e "  ${CLR_YELLOW}make ps${CLR_RESET}                Show status of all running containers"
	@echo -e "  ${CLR_YELLOW}make logs [svc]${CLR_RESET}        Tail container logs (e.g. make logs, make logs app)"
	@echo -e "  ${CLR_YELLOW}make urls${CLR_RESET}              Display active access points and service URLs"
	@echo ""
	@echo -e "${CLR_CYAN}Application & Generators:${CLR_RESET}"
	@echo -e "  ${CLR_YELLOW}make artisan <cmd>${CLR_RESET}     Run Artisan command (e.g. make artisan migrate, make artisan make:controller)"
	@echo -e "  ${CLR_YELLOW}make composer <cmd>${CLR_RESET}    Run Composer command (e.g. make composer install, make composer update)"
	@echo -e "  ${CLR_YELLOW}make npm <cmd>${CLR_RESET}         Run npm command in node container (e.g. make npm run build)"
	@echo -e "  ${CLR_YELLOW}make clean${CLR_RESET}             Clear cache, sessions, and JWT refresh tokens"
	@echo ""
	@echo -e "${CLR_CYAN}Testing & Code Quality:${CLR_RESET}"
	@echo -e "  ${CLR_YELLOW}make test [args]${CLR_RESET}       Run PHP backend test suite (e.g. make test, make test unit)"
	@echo -e "  ${CLR_YELLOW}make test-fe${CLR_RESET}           Run React frontend test suite (Vitest)"
	@echo -e "  ${CLR_YELLOW}make checks${CLR_RESET}            Run PHPCS architecture & coding standard checks"
	@echo -e "  ${CLR_YELLOW}make lint${CLR_RESET}              Run PHPCS and ESLint code quality checks"
	@echo -e "  ${CLR_YELLOW}make lint-fix${CLR_RESET}          Auto-fix PHPCS PSR-12 violations and ESLint issues"
	@echo ""
	@echo -e "${CLR_CYAN}Database & Fixtures:${CLR_RESET}"
	@echo -e "  ${CLR_YELLOW}make migrate${CLR_RESET}           Run pending database migrations"
	@echo -e "  ${CLR_YELLOW}make db-dump${CLR_RESET}           Export timestamped MariaDB dump to database/dumps/"
	@echo -e "  ${CLR_YELLOW}make db-restore <file>${CLR_RESET} Restore a .sql.gz dump into MariaDB (or FILE=path/to/dump.sql.gz)"
	@echo ""
	@echo -e "${CLR_CYAN}Interactive Shells:${CLR_RESET}"
	@echo -e "  ${CLR_YELLOW}make bash${CLR_RESET}              Open interactive bash shell inside PHP app container"
	@echo -e "  ${CLR_YELLOW}make bash-node${CLR_RESET}         Open interactive shell inside Node container"
	@echo ""

# ------------------------------------------------------------------------------
# Primary CLI Targets with Dynamic Arguments
# ------------------------------------------------------------------------------
artisan:
	docker compose exec -u www-data app php artisan $(RUN_ARGS)

composer:
	docker compose exec -u www-data app composer $(RUN_ARGS)

npm:
	docker compose exec -u node node npm $(RUN_ARGS)

logs:
	docker compose logs -f $(RUN_ARGS)

restart:
	docker compose restart $(RUN_ARGS)

test:
	docker compose exec -u www-data app php artisan test $(RUN_ARGS)

db-restore:
	@RESTORE_FILE="$(or $(FILE),$(RUN_ARGS))"; \
	if [ -z "$$RESTORE_FILE" ]; then \
		echo "Error: Please specify the dump file to restore."; \
		echo "Usage: make db-restore database/dumps/your_dump.sql.gz"; \
		echo "   or: make db-restore FILE=database/dumps/your_dump.sql.gz"; \
		exit 1; \
	fi; \
	if [ ! -f "$$RESTORE_FILE" ]; then \
		echo "Error: File '$$RESTORE_FILE' not found."; \
		exit 1; \
	fi; \
	echo "Restoring database from $$RESTORE_FILE ..."; \
	gunzip -c "$$RESTORE_FILE" | docker compose exec -T db sh -c 'mariadb -u"$$MARIADB_USER" -p"$$MARIADB_PASSWORD" "$$MARIADB_DATABASE"'; \
	echo "Database restore completed."

# ------------------------------------------------------------------------------
# Standard Direct Targets (Suppressed when running argument-forwarding targets)
# ------------------------------------------------------------------------------
ifneq ($(firstword $(MAKECMDGOALS)),$(filter $(firstword $(MAKECMDGOALS)),$(ARGS_TARGETS)))

init:
	@./scripts/docker-init.sh

up:
	docker compose up -d --remove-orphans
	@$(MAKE) --no-print-directory urls

urls:
	@echo ""
	@echo -e "${CLR_BOLD}Active Access Points & Service URLs:${CLR_RESET}"
	@echo -e "  • ${CLR_BOLD}Web Application:${CLR_RESET}    ${CLR_CYAN}http://localhost:${HTTP_PORT}${CLR_RESET}"
	@echo -e "  • ${CLR_BOLD}phpMyAdmin:${CLR_RESET}         ${CLR_CYAN}http://localhost:${PMA_PORT}${CLR_RESET}"
	@echo -e "  • ${CLR_BOLD}Documentation:${CLR_RESET}      ${CLR_CYAN}http://localhost:${HTTP_PORT}/docs/${CLR_RESET}"
	@echo -e "  • ${CLR_BOLD}API Docs (Swagger):${CLR_RESET} ${CLR_CYAN}http://localhost:${HTTP_PORT}/docs/api/${CLR_RESET}"
	@echo -e "  • ${CLR_BOLD}MariaDB (Direct):${CLR_RESET}   ${CLR_CYAN}127.0.0.1:${DB_PORT}${CLR_RESET} (User: ${DB_USER}, DB: ${DB_NAME})"
	@echo -e "  • ${CLR_BOLD}Redis (Direct):${CLR_RESET}     ${CLR_CYAN}127.0.0.1:${REDIS_PORT}${CLR_RESET}"
	@echo ""

down:
	docker compose down --remove-orphans

stop:
	docker compose stop

build:
	docker compose build

ps: status
status:
	docker compose ps

bash:
	docker compose exec -u www-data -it app bash
shell: bash

bash-node:
	docker compose exec -u node -it node bash

clean:
	docker compose exec -u www-data app php artisan optimize:clear

test-fe:
	docker compose exec -u node node npm run test

checks:
	docker compose exec -u www-data app php artisan checks

lint:
	docker compose exec -u www-data app php artisan checks
	docker compose exec -u node node npm run lint

lint-fix:
	docker compose exec -u www-data app php artisan checks psr:fix
	docker compose exec -u node node npm run lint:fix

migrate:
	docker compose exec -u www-data app php artisan migrate --force

db-dump:
	@mkdir -p database/dumps
	@DUMP_FILE="database/dumps/dump_$$(date +%Y%m%d_%H%M%S).sql.gz"; \
	echo "Exporting MariaDB database to $$DUMP_FILE ..."; \
	docker compose exec -T db sh -c 'mariadb-dump -u"$$MARIADB_USER" -p"$$MARIADB_PASSWORD" "$$MARIADB_DATABASE"' | gzip > "$$DUMP_FILE"; \
	echo "Database dump created: $$DUMP_FILE"

endif

# Fallback pattern rule to catch all trailing argument goals without errors
%:
	@:
