# Canoe test

This project is composed of several systems:

- A central API that acts as a single source of truth
- A message broker (in this case a container with rabbitMQ)
- A command that runs with supervisord to consume messages from the rabbitMQ queue

The stack is as follows:

- The systems are separated into containers using docker
- There are 3 containers
    - Apache/PHP/Symfony
    - RabbitMQ
    - PostgreSQL

## Schema used for this project:

![image](https://github.com/Aoshido/canoe/assets/1039259/5af48ba9-4e07-467f-9a76-6baeefc3dda2)

## Instructions to run the project:

- Clone the repository
- Run `docker-compose up -d --build --force-recreate`
- Run `docker-compose exec php bin/console doctrine:migrations:migrate`
- Go to `localhost/api` on your browser or you can start using the endpoints of the API with any tool (Postman, etc)
- Optionally you can run  `docker-compose exec php bin/console doctrine:fixtures:load` to get a populated db
- To run the tests you can `use docker-compose exec php bin/phpunit`

## System Capabilities

- CRUD for
    - Companies
    - Funds
    - Aliases
- Detection of possible duplicated funds
- Async marking of possible duplicated funds
- Reliability of processing the duplicates via supervisord
- Sorting of funds
