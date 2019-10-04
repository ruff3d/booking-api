
default: help

help:
		@echo "The list of supported commands:"
		@echo "start       - Build Images, run container and perform the migrations"
		@echo "stop        - Stop and remove running containers"
		@echo "sh          - Log in to php cntainer"
		@echo "restart     - Restart all containers"
		@echo "test        - Run tests"

start:
		docker-compose up -d  && docker-compose exec php ./bin/console doctrine:migrations:migrate -n

stop:
		docker-compose down --remove-orphans

restart: stop start

sh:
		docker-compose exec php sh