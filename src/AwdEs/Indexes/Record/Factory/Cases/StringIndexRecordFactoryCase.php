<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\StringIndexRecord;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Indexes\StringIndex;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

final readonly class StringIndexRecordFactoryCase implements IndexRecordFactoryCase
{
    #[\Override]
    public function handle(Id $id, Index $index, IndexMeta $indexMeta, EntityMeta $entityMeta, IDateTime $recordedAt): ?StringIndexRecord
    {
        if (!$index instanceof StringIndex) {
            return null;
        }

        return new StringIndexRecord(
            recordId: $id,
            indexName: $indexMeta->name,
            entityName: $entityMeta->name,
            entityId: $index->aggregateId(),
            value: $index->value(),
            recordedAt: $recordedAt,
        );
    }
}
