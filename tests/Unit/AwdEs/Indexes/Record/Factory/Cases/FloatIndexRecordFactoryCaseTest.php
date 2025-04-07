<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\FloatIndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\FloatIndexRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\FloatIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\FloatIndexRecordFactoryCase
 *
 * @internal
 */
final class FloatIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnFloatIndexRecordWhenIndexIsNotFloatIndex(): void
    {
        // Arrange
        $factory = new FloatIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not a FloatIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        // Assert
        assertNull($result, 'Expected null when index is not an instance of FloatIndex.');
    }

    public function testMustReturnFloatIndexRecordWhenValidFloatIndexProvided(): void
    {
        // Arrange
        $factory = new FloatIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock of the Id object for the `aggregateId` method
        $index = $this->prophesize(FloatIndex::class);
        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn(123.45); // Mocking `value()` to return a float

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
            FloatIndexRecord::class,
            $result,
            'Expected a valid instance of FloatIndexRecord.',
        );

        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame(123.45, $result->value); // Asserting the float value
        assertSame($recordedAt, $result->recordedAt);
    }
}
