<?php

namespace JSellis\EloquentMessageRepository;

use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\AggregateRootId;
use Generator;

class EloquentMessageRepository implements MessageRepository
{
    public function persist(Message ...$messages)
    {

    }

    public function retrieveAll(AggregateRootId $id): Generator
    {

    }

    public function retrieveEverything(): Generator
    {

    }
}
