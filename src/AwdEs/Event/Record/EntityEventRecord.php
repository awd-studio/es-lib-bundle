<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Record;

use Awd\ValueObject\IDateTime;
use AwdEs\ValueObject\Id;

final readonly class EntityEventRecord
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public Id $recordId,
        public string $eventType,
        public Id $entityId,
        public string $entityType,
        public array $data,
        public IDateTime $recordedAt,
    ) {}
}
