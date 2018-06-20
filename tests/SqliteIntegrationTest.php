<?php

namespace JSellis\EloquentMessageRepository\Tests;

use JSellis\EloquentMessageRepository\EloquentMessageRepository;
use JSellis\EloquentMessageRepository\SqliteEloquentMessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

class SqliteIntegrationTest extends EloquentIntegrationTestCase
{
    protected function connection(): array
    {
        return require __DIR__ . '/sqlite-connection.php';
    }

    protected function messageRepository(
        array $connection,
        MessageSerializer $serializer,
        string $tableName
    ): EloquentMessageRepository {
        return new SqliteEloquentMessageRepository($connection, $serializer, $tableName);
    }
}