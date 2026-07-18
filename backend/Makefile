.DEFAULT_GOAL := help

build: ## Build all images
	docker compose build

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

shell: ## Open a shell inside the app container
	docker compose exec app bash

install: ## Composer install
	docker compose exec app composer install

key: ## Generate app key
	docker compose exec app php artisan key:generate

migrate: ## Run migrations
	docker compose exec app php artisan migrate

artisan: ## Run any artisan command, e.g. make artisan args="make:model Group -m"
	docker compose exec app php artisan $(args)

composer: ## Run any composer command, e.g. make composer args="require spatie/laravel-activitylog"
	docker compose exec app composer $(args)

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "%-12s %s\n", $$1, $$2}'
