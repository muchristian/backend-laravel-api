## News-Feed

Laravel backend apis for a news app

## Execution Guide

To run the backend, you can use the guideline below.

### Requirements

-   Have docker installed on Local environment.
-   Have as well mysql installed locally
-   Then create database called `news_web`

### Installation

Create a `docker-compose.yml` file and add the following instructions

```shell
version: "3.7"

services:
  newswebapi:
    image: mchris12/news-web-api:1.0
    env_file:
      - .env
    networks:
      - news-feed
    ports:
      - 8000:8000

networks:
  news-feed:
    external: true
    name: news-feed

```

After create `.env` file within the same folder and paste in variable from `.env.example` file. Then modify complying with your configuration especially database credentials.

Once done with above steps, run `docker compose up`
