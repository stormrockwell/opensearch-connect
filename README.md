# OpenSearch

## Prerequisite

1. Docker
2. Composer
3. Nodejs

## Setup 

```
npm i && composer install
docker-compose build wp (only need to do this once)
docker-compose up -d
```

You can now access the WordPress instance at https://localhost:8080, OpenSearch at https://localhost:9200 and OpenSearch dashboards at https://localhost:5601

### Using Test Suite

Install scripts for PHPUnit `docker-compose exec wp install-wp-tests`

```
docker-compose exec wp composer test # runs PHPUnit and PHPCS
docker-compose exec wp composer phpunit
docker-compose exec wp composer phpcs
docker-compose exec wp composer fix
```
