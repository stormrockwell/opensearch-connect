# THIS IS A WORK IN PROGRESS. NOT COMPLETED

# OpenSearch Connect

Plugin designed to connect your WordPress instance with OpenSearch.

## TODO

1. Zero downtime reindexer using a index manager
2. Search
3. Admin interface - Hosts, indexables, 
4. Maybe using an action scheduler if large syncs hurt the site.

## Prerequisites

1. Docker
2. Composer
3. Nodejs
4. PHP - Optional to power PHP Code Sniffer for VSCode

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
