# BURNDOWN

A burndown chart application connected to Jira.

## Requirements:
* php >= 7.2.5
* any webserver (If you don't have a webserver you can use the php command by running `php -S 0.0.0.0:8000 -t public`)

## Setup
* Clone the project with `git clone <url-of-the-repository> <project-name> && cd <path-to-cloned-project>`  
* Install dependencies with `composer install`
* Load de data: `bin/console app:cache:warmup` (this command must be under an hourly cron task)
* Run the webserver
