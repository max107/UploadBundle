.PHONY: test
test: vendor/bin/phpunit
	./vendor/bin/phpunit -c phpunit.xml.dist

.PHONY: test-coverage
test-coverage: vendor/bin/phpunit
	./vendor/bin/phpunit -c phpunit.xml.dist --coverage-html=./coverage

.PHONY: open
test-open: test-coverage
	open ./coverage/index.html

.PHONY: php-cs-fixer
php-cs-fixer:
	vendor/bin/php-cs-fixer fix --allow-risky=yes

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 2G .

.PHONY: clean
clean:
	rm -rf ./coverage ./composer.lock ./db.sqlite ./.phpunit.result.cache ./vendor

vendor/bin/phpunit:
	composer install --prefer-dist
