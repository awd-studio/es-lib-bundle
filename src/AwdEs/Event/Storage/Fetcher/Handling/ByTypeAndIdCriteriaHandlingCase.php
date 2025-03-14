<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Storage\Fetcher\Handling;

use AwdEs\EsLibBundle\AwdEs\Event\Record\Converter\EventRecordsToEventStream;
use AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord;
use AwdEs\Event\EventStream;
use AwdEs\Event\Storage\Fetcher\Criteria\ByTypeAndIdCriteria;
use AwdEs\Event\Storage\Fetcher\Criteria\Criteria;
use AwdEs\Event\Storage\Fetcher\Handling\CriteriaHandlingCase;
use AwdEs\Meta\Entity\Reading\EntityMetaReader;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ByTypeAndIdCriteriaHandlingCase implements CriteriaHandlingCase
{
    public function __construct(
        private EntityManagerInterface $em,
        private EntityMetaReader $entityMetaReader,
        private EventRecordsToEventStream $converter,
    ) {}

    #[\Override]
    public function handle(Criteria $criterion): ?EventStream
    {
        if (false === ($criterion instanceof ByTypeAndIdCriteria)) {
            return null;
        }

        $id = $criterion->entityId;
        $entityMeta = $this->entityMetaReader->read($criterion->entityType);

        $r = $this->em->getRepository(EntityEventRecord::class);
        $records = $r->findBy(['entityId' => $id, 'entityType' => $entityMeta->name]);

        return $this->converter->convert($records);
    }
}
