<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Record\Factory;

use AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord;
use AwdEs\EsLibBundle\AwdEs\Id\UlidIdQueue;
use AwdEs\EsLibBundle\System\Domain\AppClock;
use AwdEs\Event\EntityEvent;
use AwdEs\Meta\Entity\Reading\EntityMetaReader;
use AwdEs\Meta\Event\Reading\EventMetaReader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class SerializerEntityEventRecordFactory implements EntityEventRecordFactory
{
    public function __construct(
        private UlidIdQueue $ids,
        private NormalizerInterface $normalizer,
        private EventMetaReader $eventMetaReader,
        private EntityMetaReader $entityMetaReader,
        private AppClock $clock,
    ) {}

    #[\Override]
    public function build(EntityEvent $event): EntityEventRecord
    {
        $id = $this->ids->next();

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize($event, 'array');
        $eventMeta = $this->eventMetaReader->read($event::class);
        $entityMeta = $this->entityMetaReader->read($eventMeta->entityFqn);

        return new EntityEventRecord(
            recordId: $id,
            eventType: $eventMeta->name,
            entityId: $event->entityId(),
            entityType: $entityMeta->name,
            data: $data,
            recordedAt: $this->clock->now(),
        );
    }
}
