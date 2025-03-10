install:
	./vendor/bin/sail build --no-cache

start:
	./vendor/bin/sail up -d

stop:
	./vendor/bin/sail stop

check-phpstan:
	./vendor/bin/sail composer run phpstan

check-cs-fixer:
	./vendor/bin/sail composer run cs-fixer

check-ide-helper:
	./vendor/bin/sail composer run ide-helper

lint:
	@make check-ide-helper
	@make check-phpstan

migrate:
	./vendor/bin/sail artisan migrate

terminal:
	@docker exec -it laravel_app bash

restart:
	@make stop
	@make start
