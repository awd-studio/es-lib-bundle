<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\ReferenceIndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\ReferenceIndexRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Indexes\ReferenceIndex;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\ReferenceIndexRecordFactoryCase
 *
 * @internal
 */
final class ReferenceIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnReferenceIndexRecordWhenIndexIsNotReferenceIndex(): void
    {
        // Arrange
        $factory = new ReferenceIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not a ReferenceIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        // Assert
        assertNull($result, 'Expected null when the index is not an instance of ReferenceIndex.');
    }

    public function testMustReturnReferenceIndexRecordWhenValidReferenceIndexProvided(): void
    {
        // Arrange
        $factory = new ReferenceIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock for the `aggregateId` method
        $valueId = $this->prophesize(Id::class)->reveal(); // Mock for the `value` method
        $index = $this->prophesize(ReferenceIndex::class);
        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn($valueId); // Mocking `value()` to return an Id object

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
            ReferenceIndexRecord::class,
            $result,
            'Expected a valid instance of ReferenceIndexRecord.',
        );

        // Assertions for fields in ReferenceIndexRecord
        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame($valueId, $result->value); // Asserting the Id value
        assertSame($recordedAt, $result->recordedAt);
    }
}
