<?php

namespace Surgio\EloquentMessageRepository\Tests;

use EventSauce\Clock\TestClock;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Surgio\EloquentMessageRepository\EloquentMessageRepository;

class EloquentIntegrationTest extends TestCase
{
    private TestClock $clock;
    private DefaultHeadersDecorator $decorator;
    private EloquentMessageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(__DIR__.'/../migrations'),
        ]);

        $this->clock = new TestClock();
        $this->decorator = new DefaultHeadersDecorator(null, $this->clock);
        $this->repository = new EloquentMessageRepository(new ConstructingMessageSerializer());
    }

    #[Test]
    public function it_works(): void
    {
        $aggregateRootId = TestAggregateRootId::create();
        $this->repository->persist();
        $this->assertEmpty(iterator_to_array($this->repository->retrieveAll($aggregateRootId)));
        $eventId = Uuid::uuid4()->toString();
        $message = $this->decorator->decorate(new Message(new TestEvent(), [
            Header::EVENT_ID => $eventId,
            Header::AGGREGATE_ROOT_ID => $aggregateRootId->toString(),
        ]));
        $this->repository->persist($message);
        $retrievedMessage = iterator_to_array($this->repository->retrieveAll($aggregateRootId), false)[0];
        $this->assertEquals($message, $retrievedMessage);
    }

    #[Test]
    public function persisting_events_without_aggregate_root_ids(): void
    {
        $eventId = Uuid::uuid4();
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->now()), [
            Header::EVENT_ID => $eventId->toString(),
        ]));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertEquals($message, $persistedMessages[0]);
    }

    #[Test]
    public function persisting_events_without_event_ids(): void
    {
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->now())));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertNotEquals($message, $persistedMessages[0]);
    }
}
