<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Recording;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactory;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Recording\IndexRecorder;
use Doctrine\ORM\EntityManagerInterface;

final readonly class IndexRecordIndexRecorder implements IndexRecorder
{
    public function __construct(
        private IndexRecordFactory $factory,
        private EntityManagerInterface $em,
    ) {}

    #[\Override]
    public function record(Index $index, IDateTime $recordedAt): void
    {
        $record = $this->factory->build($index, $recordedAt);

        $this->em->persist($record);
        $this->em->flush();
    }
}
