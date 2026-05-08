<?php

namespace Surgio\EloquentMessageRepository\Tests;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

class TestEvent implements SerializablePayload
{
    public function __construct(mixed $ignored = null)
    {
    }

    public function toPayload() : array
    {
        return [];
    }

    public static function fromPayload(array $payload) : static
    {
        return new self();
    }
}
