<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Record\Converter;

use AwdEs\EsLibBundle\System\Serializer\Domain\AwdSerializer;
use AwdEs\Event\EventStream;
use AwdEs\Registry\Event\EventRegistry;

final readonly class SerializerEventRecordsToEventStream implements EventRecordsToEventStream
{
    public function __construct(
        private AwdSerializer $serializer,
        private EventRegistry $eventRegistry,
    ) {}

    #[\Override]
    public function convert(array $records): EventStream
    {
        $eventStream = new EventStream();
        foreach ($records as $record) {
            $eventType = $this->eventRegistry->eventFqnFor($record->eventType);
            $event = $this->serializer->deserialize($eventType, $record->data);
            $eventStream->append($event);
        }

        return $eventStream;
    }
}
