# Local Environment Visual
![Alt text](.docker/dev-env-visual.png?raw=true "wot a lovely visual")


# Prerequisites

- [Docker](https://docs.docker.com/get-docker/) + [Docker Compose](https://docs.docker.com/compose/install/) are required to run the project

# Starting the project

```
cp .env.example .env
docker network create backend
docker-compose up -d  
```

# QA Tooling

#### [Lint](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
```
docker-compose exec api vendor/bin/php-cs-fixer fix
``` 
#### [Static Analysis](https://github.com/phpstan/phpstan)
```
docker-compose exec api vendor/bin/phpstan
```