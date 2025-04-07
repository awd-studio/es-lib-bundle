<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IndexRecord;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

interface IndexRecordFactoryCase
{
    /**
     * @phpstan-ignore missingType.generics
     */
    public function handle(
        Id $id,
        Index $index,
        IndexMeta $indexMeta,
        EntityMeta $entityMeta,
        IDateTime $recordedAt,
    ): ?IndexRecord;
}
