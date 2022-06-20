# OpenSearch

Plugin designed to connect your WordPress instance with OpenSearch.

## Prerequisites

1. Docker
2. Composer
3. Nodejs

## Included DevTools & Tests

1. XDebug
2. PHPUnit
3. PHPCS & PHPCBF

If you are using VSCode, I'd suggest installing the following extensions to fully take advantage of the tools.

```
PHP Debug
PHP Sniffer & Beautifier
Run on Save
```

## Setup 

```
npm i && composer install
docker-compose build wp (only need to do this once)
docker-compose up -d
```

You can now access the WordPress instance at https://localhost:8080, OpenSearch at https://localhost:9200 and OpenSearch dashboards at http://localhost:5601

### Using Test Suite

Install scripts for PHPUnit `docker-compose exec wp install-wp-tests`

```
docker-compose exec wp cr test # runs PHPUnit and PHPCS
docker-compose exec wp cr phpunit
docker-compose exec wp cr phpcs
docker-compose exec wp cr fix # Runs PHPCBF
```
