
default: help

help:
		@echo "The list of supported commands:"
		@echo "init        - Init command for freash setup"
		@echo "migration   - Perform the migrations"
		@echo "start       - Build Images, run container"
		@echo "stop        - Stop and remove running containers"
		@echo "sh          - Log in to php cntainer"
		@echo "restart     - Restart all containers"
		@echo "test        - Run tests"

start:
		docker-compose up -d

stop:
		docker-compose down --remove-orphans

restart: stop start

sh:
		docker-compose exec php sh

migration:
		docker-compose exec php ./bin/console doctrine:migrations:migrate -n

init: start migration

test:
		docker-compose exec php ./bin/phpunit
