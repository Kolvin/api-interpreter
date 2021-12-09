# Symfony API JSON [DSL](https://en.wikipedia.org/wiki/Domain-specific_language) - [Spec](app/SPEC.md)
### Local Environment Visual
![Alt text](.docker/dev-env-visual.png?raw=true "wot a lovely visual")


# Prerequisites
- [Docker](https://docs.docker.com/get-docker/) + [Docker Compose](https://docs.docker.com/compose/install/) are required to run the project

## 1. Starting the project
#### As long as there is no conflicting local services, the API will be served on [localhost](http://localhost)
```
cp .env.example .env
docker network create backend
docker-compose up -d  
```
___
## 2. Dependencies
#### As long as there is no conflicting local services, the API will be served on [localhost](http://localhost)
```
docker-compose exec api composer install
```
___
## 3. Schema Management
#### append ``--env test`` to target the test env (required for passing test suite)
```
docker-compose exec api bin/console d:d:c 
docker-compose exec api bin/console d:m:e App\\Migrations\\Version20211209180121
docker-compose exec api bin/console d:s:v
```
___
## 4. Load Project Data
#### append ``--env test`` to target the test env (required for passing test suite)
```
docker-compose exec api bin/console app:import:data
```
___

# QA Tooling

#### [lint](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
```
docker-compose exec api vendor/bin/php-cs-fixer fix
``` 
#### [static analysis](https://github.com/phpstan/phpstan)
```
docker-compose exec api vendor/bin/phpstan
```
___

# Considerations
1. Chose Postgres over MySQL for the datastore
   - From the spec we knew we need a relation store 
   - Never touched postgres before, so nice learning exercise
   - Researched the differences, postgres apparently the better option for analytical calculations
2. Setup Dockerfile in order to easily control our php extensions/environment
3. Used [symfony/skeleton](https://github.com/symfony/skeleton) to scaffold our Symfony API
4. No authentication requirement but crossed my mind regardless, could be achieved with an API/JWS key but this would then require some sort of identify provided
5. Created an [InterperterInterface](app/src/Services/Interpreter/InterpreterInterface.php) so we can enforce new methods to handle expressions for other entities. ie; Attributes
___
# Improvements
1. Keen to add [symfony/validator](https://symfony.com/doc/current/validation.html) to the project and use this to validate Custom Request Classes before heading into the service layer
2. Not quite happy with [JSONInterpreterService](app/src/Services/Interpreter/JSONInterpreterService.php) yet, I feel it could be improved by splitting it down into smaller functions further, potentially moving the calculation part out to its own service
3. Keen to add more unit/integration tests across the board;
4. I love automating project checks like testing, qa tooling through CI jobs
   1. could achieve this using the existing image and some CI vendor like [Github Actions](https://github.com/features/actions) or [Gitlab CI](https://docs.gitlab.com/ee/ci/)