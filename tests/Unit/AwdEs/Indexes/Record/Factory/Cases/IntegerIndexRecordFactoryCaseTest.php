<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\IntegerIndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IntegerIndexRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\IntegerIndex;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\IntegerIndexRecordFactoryCase
 *
 * @internal
 */
final class IntegerIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnIntegerIndexRecordWhenIndexIsNotIntegerIndex(): void
    {
        // Arrange
        $factory = new IntegerIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not an IntegerIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        // Assert
        assertNull($result, 'Expected null when index is not an instance of IntegerIndex.');
    }

    public function testMustReturnIntegerIndexRecordWhenValidIntegerIndexProvided(): void
    {
        // Arrange
        $factory = new IntegerIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock of the Id object for the `aggregateId` method
        $index = $this->prophesize(IntegerIndex::class);
        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn(42); // Mocking `value()` to return an integer

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
            IntegerIndexRecord::class,
            $result,
            'Expected a valid instance of IntegerIndexRecord.',
        );

        // Assertions for fields in IntegerIndexRecord
        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame(42, $result->value); // Asserting the integer value
        assertSame($recordedAt, $result->recordedAt);
    }
}
