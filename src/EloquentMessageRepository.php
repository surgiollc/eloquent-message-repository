<?php

namespace Surgio\EloquentMessageRepository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\PaginationCursor;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class EloquentMessageRepository implements MessageRepository
{
    private MessageSerializer $serializer;
    protected string $table = 'domain_messages';

    public function __construct(MessageSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function persist(Message ...$messages): void
    {
        if (count($messages) === 0) {
            return;
        }

        $params = [];
        foreach ($messages as $message) {
            $payload = $this->serializer->serializeMessage($message);
            $params[] = [
                'time_of_recording' => $payload['headers'][Header::TIME_OF_RECORDING],
                'event_id' => $payload['headers'][Header::EVENT_ID] = $payload['headers'][Header::EVENT_ID] ?? Uuid::uuid4()->toString(),
                'payload' => json_encode($payload, JSON_PRETTY_PRINT),
                'event_type' => $payload['headers'][Header::EVENT_TYPE],
                'aggregate_root_id' => $payload['headers'][Header::AGGREGATE_ROOT_ID] ?? null,
            ];
        }

        DB::transaction(function () use ($params) {
            DB::table($this->table)->insert($params);
        });
    }

    public function retrieveAll(AggregateRootId $id): Generator
    {
        $messages = DB::table($this->table)
            ->where('aggregate_root_id', $id->toString())
            ->orderBy('time_of_recording', 'ASC')
            ->get(['payload']);

        foreach ($messages as $message) {
            yield $this->serializer->unserializePayload(json_decode($message->payload, true));
        }
    }

    public function retrieveEverything(): Generator
    {
        $messages = DB::table($this->table)
            ->orderBy('time_of_recording', 'ASC')
            ->get(['payload']);

        foreach ($messages as $message) {
            yield $this->serializer->unserializePayload(json_decode($message->payload, true));
        }
    }

    public function retrieveAllAfterVersion(AggregateRootId $id, int $aggregateRootVersion): Generator
    {
        $messages = DB::table($this->table)
            ->where('aggregate_root_id', $id->toString())
            ->orderBy('time_of_recording', 'ASC')
            ->get(['payload']);

        foreach ($messages as $message) {
            $decoded = json_decode($message->payload, true);
            if (($decoded['headers'][Header::AGGREGATE_ROOT_VERSION] ?? 0) > $aggregateRootVersion) {
                yield $this->serializer->unserializePayload($decoded);
            }
        }
    }

    public function paginate(PaginationCursor $cursor): Generator
    {
        yield from [];
    }
}
