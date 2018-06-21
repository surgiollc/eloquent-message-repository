<?php

namespace JSellis\EloquentMessageRepository\Tests;

use EventSauce\EventSourcing\Serialization\SerializableEvent;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/database.sqlite',
        ]);
    }
}
