<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\BooleanIndexRecord;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactoryCase;
use AwdEs\Indexes\BooleanIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

final readonly class BooleanIndexRecordFactoryCase implements IndexRecordFactoryCase
{
    #[\Override]
    public function handle(Id $id, Index $index, IndexMeta $indexMeta, EntityMeta $entityMeta, IDateTime $recordedAt): ?BooleanIndexRecord
    {
        if (!$index instanceof BooleanIndex) {
            return null;
        }

        return new BooleanIndexRecord(
            recordId: $id,
            indexName: $indexMeta->name,
            entityName: $entityMeta->name,
            entityId: $index->aggregateId(),
            value: $index->value(),
            recordedAt: $recordedAt,
        );
    }
}
