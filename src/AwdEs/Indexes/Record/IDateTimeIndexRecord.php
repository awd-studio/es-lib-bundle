<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record;

use Awd\ValueObject\IDateTime;
use AwdEs\ValueObject\Id;

final readonly class IDateTimeIndexRecord implements IndexRecord
{
    public function __construct(
        public Id $recordId,
        public string $indexName,
        public string $entityName,
        public Id $entityId,
        public IDateTime $value,
        public IDateTime $recordedAt,
    ) {}
}
