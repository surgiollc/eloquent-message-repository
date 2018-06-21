# Eloquent Message Repository for EventSauce

[![Build Status](https://travis-ci.org/JSellis/eloquent-message-repository.svg?branch=master)](https://travis-ci.org/JSellis/eloquent-message-repository)

Inspired by [EventSaucePHP/DoctrineMessageRepository](https://github.com/EventSaucePHP/DoctrineMessageRepository).

## Installation & setup

```
composer require jsellis/eloquent-message-repository:^1.0.0
```
```
php artisan vendor:publish --provider="JSellis\EloquentMessageRepository\EventSauceServiceProvider" --tag="migrations"
php artisan migrate
```
