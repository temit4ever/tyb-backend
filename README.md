# YTB Backend

This is a backend API for the YTB project.

## Requirements
This project has the following requirements:
* PHP 7.3
* Composer

## Getting Started
To get a local copy of this environment set up, follow these steps:
1. Checkout this repository
1. Run `composer install`
1. Copy the environment file `cp .env.example .env`
1. Create the application key `php artisan key:generate`
1. Run the migrations and seed the database: `php artisan migrate:fresh --seed`
1. Run the local dev server `php artisan serve`

## Unit tests
Feature and unit tests can be run via artisan:
```
php artisan test
```

## API Documentation
API documentation is generated using OpenAPI. The documentation is notated in relevant
controllers and support files. To generate the documentation, run
```
php artisan l5-swagger:generate
```

## TODO
Here are some next-steps that need doing:
* Finish API documentation
* Return Task Type titles with Tasks (tricky, based on slug)
* Search/filter for tasks, users, and sprints (Use Spatie plugin)
* Remove the default home page content and replace YTB description
* User authentication and roles
* Move local development to dockerized containers, using MySQL instead of SQLite
