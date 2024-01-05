# Eloquent Message Repository for EventSauce

This package allows you to use Eloquent as a custom message repository for [EventSauce](https://eventsauce.io/docs/advanced/custom-repository/).

Heavily inspired by [EventSaucePHP/DoctrineMessageRepository](https://github.com/EventSaucePHP/DoctrineMessageRepository).

## Requirements
This package requires PHP 8.1 and Laravel 9 or higher.

## Installation

```
composer require surgio/eloquent-message-repository:^1.0.0
```

## Setup
Publish the migration:
```
php artisan vendor:publish --provider="Surgio\EloquentMessageRepository\EventSauceServiceProvider" --tag="migrations"
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
