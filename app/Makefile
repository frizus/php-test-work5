install-libs:
	composer install

setup: install-libs run-composer-scripts seed-db

run-composer-scripts:
	composer run-script post-root-package-install
	composer run-script post-create-project-cmd

seed-db:
	php artisan db:seed

linter:
	./vendor/bin/php-cs-fixer fix

test:
	php artisan test

lint: linter

.PHONY: test
