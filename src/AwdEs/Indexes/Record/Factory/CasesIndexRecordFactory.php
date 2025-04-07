<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Id\UlidIdQueue;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IndexRecord;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\Reading\IndexMetaReader;
use AwdEs\Indexes\Recording\Exception\IndexRecordingError;
use AwdEs\Meta\Entity\Reading\EntityMetaReader;

final readonly class CasesIndexRecordFactory implements IndexRecordFactory
{
    /**
     * @param iterable<IndexRecordFactoryCase> $cases
     */
    public function __construct(
        private iterable $cases,
        private UlidIdQueue $ids,
        private IndexMetaReader $indexMetaReader,
        private EntityMetaReader $entityMetaReader,
    ) {}

    #[\Override]
    public function build(Index $index, IDateTime $recordedAt): IndexRecord
    {
        $id = $this->ids->next();
        $indexMeta = $this->indexMetaReader->read($index::class);
        $entityMeta = $this->entityMetaReader->read($indexMeta->entityFqn);

        /** @var IndexRecordFactoryCase $case */
        foreach ($this->cases as $case) {
            $result = $case->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);
            if (null === $result) {
                continue;
            }

            return $result;
        }

        throw new IndexRecordingError(\sprintf('No case matched for index %s', $index::class));
    }
}
