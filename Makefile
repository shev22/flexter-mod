install:
	./vendor/bin/sail build

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
	@make check-phpstan
