<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record;

use Awd\ValueObject\IDateTime;
use AwdEs\ValueObject\Id;

final readonly class BooleanIndexRecord implements IndexRecord
{
    public function __construct(
        public Id $recordId,
        public string $indexName,
        public string $entityName,
        public Id $entityId,
        public bool $value,
        public IDateTime $recordedAt,
    ) {}
}
