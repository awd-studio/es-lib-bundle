<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\BooleanIndexRecord;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\BooleanIndexRecordFactoryCase;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\BooleanIndex;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\BooleanIndexRecordFactoryCase
 *
 * @internal
 */
final class BooleanIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnBooleanIndexRecordWhenIndexIsNotBooleanIndex(): void
    {
        // Arrange
        $factory = new BooleanIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not a BooleanIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        // Act
        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        // Assert
        assertNull($result, 'Expected null when index is not an instance of BooleanIndex.');
    }

    public function testMustReturnBooleanIndexRecordWhenValidBooleanIndexProvided(): void
    {
        // Arrange
        $factory = new BooleanIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock of the Id object for the `aggregateId` method
        $index = $this->prophesize(BooleanIndex::class);
        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn(true); // Mocking `value()` to return a boolean

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
            BooleanIndexRecord::class,
            $result,
            'Expected a valid instance of BooleanIndexRecord.',
        );

        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame(true, $result->value); // Asserting the boolean value
        assertSame($recordedAt, $result->recordedAt);
    }
}
