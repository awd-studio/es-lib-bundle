<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\FloatIndexRecord;
use AwdEs\Indexes\FloatIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

final readonly class FloatIndexRecordFactoryCase implements IndexRecordFactoryCase
{
    #[\Override]
    public function handle(Id $id, Index $index, IndexMeta $indexMeta, EntityMeta $entityMeta, IDateTime $recordedAt): ?FloatIndexRecord
    {
        if (!$index instanceof FloatIndex) {
            return null;
        }

        return new FloatIndexRecord(
            recordId: $id,
            indexName: $indexMeta->name,
            entityName: $entityMeta->name,
            entityId: $index->aggregateId(),
            value: $index->value(),
            recordedAt: $recordedAt,
        );
    }
}
