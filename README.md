# Eloquent Message Repository for EventSauce

[![Build Status](https://travis-ci.org/JSellis/eloquent-message-repository.svg?branch=master)](https://travis-ci.org/JSellis/eloquent-message-repository)
[![Latest Stable Version](https://poser.pugx.org/jsellis/eloquent-message-repository/version)](https://packagist.org/packages/jsellis/eloquent-message-repository)

This package allows you to use Eloquent as the message repository for [EventSauce](https://eventsauce.io).

Heavily inspired by [EventSaucePHP/DoctrineMessageRepository](https://github.com/EventSaucePHP/DoctrineMessageRepository).

## Requirements
This package requires PHP 7.2 and Laravel 5.6 or higher.

## Installation

```
composer require jsellis/eloquent-message-repository:^1.0.0
```

## Setup
Publish the migration:
```
php artisan vendor:publish --provider="JSellis\EloquentMessageRepository\EventSauceServiceProvider" --tag="migrations"
```
Migrate your database:
```
php artisan migrate
```

## Usage
The Eloquent Message Repository implements `EventSauce\EventSourcing\MessageRepository` and can be passed to any `AggregateRootRepository` like so:
```
$aggregateRootRepository = new ConstructingAggregateRootRepository(
    SomeProcess::class,
    new EloquentMessageRepository(new ConstructingMessageSerializer())
);
```

## Testing
You can run the tests with:
```
composer test
```