<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IDateTimeIndexRecord;
use AwdEs\Indexes\IDateTimeIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

final readonly class IDateTimeIndexRecordFactoryCase implements IndexRecordFactoryCase
{
    #[\Override]
    public function handle(Id $id, Index $index, IndexMeta $indexMeta, EntityMeta $entityMeta, IDateTime $recordedAt): ?IDateTimeIndexRecord
    {
        if (!$index instanceof IDateTimeIndex) {
            return null;
        }

        return new IDateTimeIndexRecord(
            recordId: $id,
            indexName: $indexMeta->name,
            entityName: $entityMeta->name,
            entityId: $index->aggregateId(),
            value: $index->value(),
            recordedAt: $recordedAt,
        );
    }
}
