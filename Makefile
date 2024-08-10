HTTPD_CONTAINER = inertia_httpd
PHP_CONTAINER = inertia_php
REQUIREMENTS = docker docker-compose vi npm node

check:
	$(foreach REQUIREMENT, $(REQUIREMENTS), \
		$(if $(shell command -v $(REQUIREMENT) 2> /dev/null), \
			$(info `$(REQUIREMENT)` has been found. OK!), \
			$(error Please install `$(REQUIREMENT)` before running setup.) \
		) \
	)

setup: check
	cp ./.env.local ./.env
	cp docker-compose.dev.yml docker-compose.override.yml
	vi ./.env
	vi docker-compose.override.yml
	docker-compose up -d --build
	docker exec $(HTTPD_CONTAINER) chmod -R 777 /var/www/inertia/storage
	docker exec $(PHP_CONTAINER) composer install --prefer-dist
	docker exec $(PHP_CONTAINER) php artisan key:generate
	make clear-cache

clear-cache:
	docker exec ${PHP_CONTAINER} php artisan optimize:clear
	docker exec ${PHP_CONTAINER} php artisan optimize
	docker exec ${PHP_CONTAINER} php artisan cache:clear
	docker exec ${PHP_CONTAINER} php artisan config:clear
	docker exec ${PHP_CONTAINER} php artisan route:clear
	docker exec ${PHP_CONTAINER} php artisan view:clear

bash:
	docker exec -it $(PHP_CONTAINER) bash