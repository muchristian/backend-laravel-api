## News-Feed

Laravel backend apis for a news app

## Testing Guide

To run the backend, you can use the guideline below.

### Prerequisites

-   Have docker installed on Local environment.
-   Have as well mysql installed locally

### Installation

Create a folder and populate in the files below

1. Create an `.env` file, and use the provided `env.example` file as a boilerplate to make configurations.
2. Create a `docker-compose.yml` file, and paste the following configuration:

```shell
version: "3.7"

services:
    newswebapi:
        image: mchris12/news-web-api:1.0
        env_file:
            - .env
        ports:
            - 8000:8000
        restart: unless-stopped
        depends_on:
            - newsfeeddb
        networks:
            - news-feed

    newsfeeddb:
        image: mysql:5.7
        environment:
            MYSQL_ROOT_PASSWORD: password
            MYSQL_USER: news_web
            MYSQL_PASSWORD: password
            MYSQL_DATABASE: news_web
        ports:
            - 3306:3306
        volumes:
            - newsfeeddb-volume:/var/lib/mysql
        restart: unless-stopped
        networks:
            - news-feed

    newsfeedcronjob:
        build:
            context: .
            dockerfile: cron.dockerfile
        volumes:
            - ./:/var/www/html
        depends_on:
            - newswebapi
        networks:
            - news-feed

volumes:
    newsfeeddb-volume:

networks:
    news-feed:

```

The above configurations pulls our backend service and mysql image, and configure the cron job execution.

3. Create the `crontab` and `cron.dockerfile` files, and paste in the following configuration

`crontab`

```shell
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1

```

This file carries the configurations of running the laravel cron job in background.

`cron.dockerfile`

```shell
FROM php:7.4-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY crontab /etc/crontabs/root

CMD ["crond", "-f"]

```

This are configurations for creating a cron job image, and execute.

### Execution

After populating the files with their configurations, Now it's possible to run our backend service.

First, pull and run our backend service image

```shell
  sudo docker compose up -d --build newswebapi
```

Then open another terminal and migrate tables and seed reference data

```shell
  sudo docker-compose run --rm newswebapi php artisan migrate
  sudo docker-compose run --rm newswebapi php artisan db:seed
```

after, run the cron job in detached mode

```shell
  sudo docker compose up -d newsfeedcronjob
```

**Note:** we are using cron job to get real time data, which is scheduled to execute every minute for testing purpose.

Finally, use the application

**Note:** Please remember to stop running service in detached after testing
