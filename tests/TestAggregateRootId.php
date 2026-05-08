<?php

namespace Surgio\EloquentMessageRepository\Tests;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;

readonly class TestAggregateRootId implements AggregateRootId
{
    private function __construct(private string $id)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new self($aggregateRootId);
    }

    public static function create(): static
    {
        return new self(Uuid::uuid4()->toString());
    }
}
