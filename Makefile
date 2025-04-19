install:
	./vendor/bin/sail build --no-cache

stop:
	./vendor/bin/sail stop

check-phpstan:
	@docker exec -it laravel_app composer run phpstan

check-cs-fixer:
	@docker exec -it laravel_app composer run cs-fixer

check-ide-helper:
	@docker exec -it laravel_app composer run ide-helper

lint:
	@make check-ide-helper
	@make check-phpstan

migrate:
	./vendor/bin/sail artisan migrate

terminal:
	@docker exec -it laravel_app bash

start:
	./vendor/bin/sail up -d
	@docker exec -it laravel_app npm run dev
restart:
	@make stop
	@make start
