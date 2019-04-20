
.PHONY: start install migrate
start:
	php -S localhost:8080 -t public/
install:
	composer install
	cp example.config.ini config.ini
migrate:
	bin/migrate
	bin/seed

