<?php

namespace Surgio\EloquentMessageRepository\Tests;

use EventSauce\EventSourcing\Serialization\SerializableEvent;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

class TestEvent implements SerializablePayload
{
    public function toPayload() : array
    {
        return [];
    }

    public static function fromPayload(array $payload) : SerializablePayload
    {
        return new self();
    }
}
