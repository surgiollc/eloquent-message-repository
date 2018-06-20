<?php

namespace JSellis\EloquentMessageRepository\Tests;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\Time\Clock;
use EventSauce\EventSourcing\Time\TestClock;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\UuidAggregateRootId;
use Illuminate\Database\Capsule\Manager as Capsule;
use JSellis\EloquentMessageRepository\EloquentMessageRepository;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Ramsey\Uuid\Uuid;

abstract class EloquentIntegrationTestCase extends TestCase
{
    /**
     * @var Capsule
     */
    private $capsule;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var DefaultHeadersDecorator
     */
    private $decorator;
    /**
     * @var EloquentMessageRepository
     */
    private $repository;

    abstract protected function connection(): array;

    abstract protected function messageRepository(
        array $connection,
        MessageSerializer $serializer,
        string $tableName
    ): EloquentMessageRepository;

    protected function setUp()
    {
        parent::setUp();
        $connection = $this->connection();
        $this->capsule = new Capsule();
        $this->capsule->setAsGlobal();
        $this->capsule->addConnection($connection);
        $this->capsule->schema()->dropIfExists('domain_messages');
        $this->capsule->schema()->create('domain_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event_id', 36);
            $table->string('event_type', 100);
            $table->string('aggregate_root_id', 36)->nullable()->index();
            $table->dateTime('time_of_recording', 6)->index();
            $table->text('payload');
        });
        $this->capsule->table('domain_messages')->truncate();
        $serializer = new ConstructingMessageSerializer();
        $this->clock = new TestClock();
        $this->decorator = new DefaultHeadersDecorator(null, $this->clock);
        $this->repository = $this->messageRepository($connection, $serializer, 'domain_messages');
    }

    /**
    * @test
    */
    public function it_works()
    {
        $aggregateRootId = UuidAggregateRootId::create();
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

    /**
     * @test
     */
    public function persisting_events_without_aggregate_root_ids()
    {
        $eventId = Uuid::uuid4();
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->pointInTime()), [
            Header::EVENT_ID => $eventId->toString(),
        ]));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertEquals($message, $persistedMessages[0]);
    }

    /**
     * @test
     */
    public function persisting_events_without_event_ids()
    {
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->pointInTime())));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertNotEquals($message, $persistedMessages[0]);
    }
}
