<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\IDateTimeIndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IDateTimeIndexRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\IDateTimeIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\IDateTimeIndexRecordFactoryCase
 *
 * @internal
 */
final class IDateTimeIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnIDateTimeIndexRecordWhenIndexIsNotIDateTimeIndex(): void
    {
        // Arrange
        $factory = new IDateTimeIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not an IDateTimeIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        // Assert
        assertNull($result, 'Expected null when index is not an instance of IDateTimeIndex.');
    }

    public function testMustReturnIDateTimeIndexRecordWhenValidIDateTimeIndexProvided(): void
    {
        // Arrange
        $factory = new IDateTimeIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock of the Id object for the `aggregateId` method
        $index = $this->prophesize(IDateTimeIndex::class);
        $value = $this->prophesize(IDateTime::class)->reveal(); // Mock IDateTime for the `value` method

        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn($value); // Mocking `value()` to return an IDateTime object

        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle(
            $id,
            $index->reveal(),
            $indexMeta,
            $entityMeta,
            $recordedAt,
        );

        // Assert
        assertInstanceOf(
            IDateTimeIndexRecord::class,
            $result,
            'Expected a valid instance of IDateTimeIndexRecord.',
        );

        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame($value, $result->value); // Asserting the IDateTime value
        assertSame($recordedAt, $result->recordedAt);
    }
}
