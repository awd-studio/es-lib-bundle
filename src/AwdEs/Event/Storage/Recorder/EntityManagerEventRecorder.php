<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Storage\Recorder;

use AwdEs\EsLibBundle\AwdEs\Event\Record\Factory\EntityEventRecordFactory;
use AwdEs\Event\EntityEvent;
use AwdEs\Event\Storage\Recorder\EventRecorder;
use Doctrine\ORM\EntityManagerInterface;

final readonly class EntityManagerEventRecorder implements EventRecorder
{
    public function __construct(
        private EntityEventRecordFactory $factory,
        private EntityManagerInterface $em,
    ) {}

    #[\Override]
    public function record(EntityEvent $event): void
    {
        $record = $this->factory->build($event);
        $this->em->persist($record);
        $this->em->flush();
    }
}
