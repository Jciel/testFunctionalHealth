CONTAINER_SERVICE=app

d-install:
	docker exec -it $(CONTAINER_SERVICE) composer install

d-up:
	docker-compose up

d-down:
	docker-compose down

d-build:
	docker-compose build --no-cache

d-up-build:
	docker-compose up --build

migrate:
	docker-compose exec $(CONTAINER_SERVICE) php artisan migrate

migrate-refresh:
	docker-compose exec $(CONTAINER_SERVICE) php artisan migrate:refresh

migrate-down:
	docker-compose exec $(CONTAINER_SERVICE) php artisan migrate:fresh

db-seeder:
	docker-compose exec $(CONTAINER_SERVICE) php artisan db:seed

test:
	docker-compose exec $(CONTAINER_SERVICE) vendor/bin/phpunit

testfile:
	 docker-compose exec $(CONTAINER_SERVICE) vendor/bin/phpunit --filter $(file)

require:
	docker-compose exec $(CONTAINER_SERVICE) composer require $(lib)

art:
	docker-compose exec $(CONTAINER_SERVICE) php artisan make:$(filter-out $@,$(MAKECMDGOALS))

%:
    @: