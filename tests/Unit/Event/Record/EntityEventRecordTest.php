<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Event\Record;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Id;
use Prophecy\Prophecy\ObjectProphecy;

use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord
 *
 * @internal
 */
final class EntityEventRecordTest extends AppTestCase
{
    public function testEntityEventRecordCreation(): void
    {
        /** @var Id|ObjectProphecy $recordId */
        $recordId = $this->prophesize(Id::class);

        /** @var Id|ObjectProphecy $entityId */
        $entityId = $this->prophesize(Id::class);

        /** @var IDateTime|ObjectProphecy $recordedAt */
        $recordedAt = $this->prophesize(IDateTime::class);

        $eventRecord = new EntityEventRecord(
            $recordId->reveal(),
            'UserCreated',
            $entityId->reveal(),
            'User',
            ['name' => 'John Doe'],
            $recordedAt->reveal(),
        );

        assertSame('UserCreated', $eventRecord->eventType);
        assertSame('User', $eventRecord->entityType);
        assertSame(['name' => 'John Doe'], $eventRecord->data);
    }
}
