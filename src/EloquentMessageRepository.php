<?php

namespace JSellis\EloquentMessageRepository;

use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\AggregateRootId;
use Generator;
use EventSauce\EventSourcing\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class EloquentMessageRepository extends Model implements MessageRepository
{   
    /**
     * @var MessageSerializer
     */
    private $serializer;

    protected $table = 'domain_messages';

    public function __construct(MessageSerializer $serializer, array $attributes = [])
    {
        parent::__construct($attributes);

        $this->serializer = $serializer;
    }

    public function persist(Message ...$messages)
    {
        if (count($messages) === 0) {
            return;
        }

        foreach ($messages as $index => $message) {
            $payload = $this->serializer->serializeMessage($message);
            $params[] = [
                'event_id' => $payload['headers'][Header::EVENT_ID] = $payload['headers'][Header::EVENT_ID] ?? Uuid::uuid4()->toString(),
                'event_type' => $payload['headers'][Header::EVENT_TYPE],
                'aggregate_root_id' => $payload['headers'][Header::AGGREGATE_ROOT_ID] ?? null,
                'time_of_recording' => $payload['headers'][Header::TIME_OF_RECORDING],
                'payload' => json_encode($payload, JSON_PRETTY_PRINT)
            ];
        }

        DB::transaction(function () use ($params) {
            static::insert($params);
        });
    }

    public function retrieveAll(AggregateRootId $id): Generator
    {
        $messages = DB::table($this->table)
            ->select('payload')
            ->where('aggregate_root_id', $id->toString())
            ->orderBy('time_of_recording', 'ASC')
            ->get();

        foreach ($messages as $message) {
            yield from $this->serializer->unserializePayload(json_decode($message->payload, true));
        }
    }

    public function retrieveEverything(): Generator
    {
        $messages = DB::table($this->table)
            ->select('payload')
            ->orderBy('time_of_recording', 'ASC')
            ->get();

        foreach ($messages as $message) {
            yield from $this->serializer->unserializePayload(json_decode($message->payload, true));
        }
    }
}
