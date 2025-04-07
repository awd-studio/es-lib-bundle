<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\Indexes\Record\Factory\Cases;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\StringIndexRecordFactoryCase;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\StringIndexRecord;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Meta\IndexMeta;
use AwdEs\Indexes\StringIndex;
use AwdEs\Meta\Entity\EntityMeta;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\Cases\StringIndexRecordFactoryCase
 *
 * @internal
 */
final class StringIndexRecordFactoryCaseTest extends AppTestCase
{
    public function testMustNotReturnStringIndexRecordWhenIndexIsNotStringIndex(): void
    {
        $factory = new StringIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();
        $index = $this->prophesize(Index::class)->reveal(); // Not a StringIndex instance
        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        $result = $factory->handle($id, $index, $indexMeta, $entityMeta, $recordedAt);

        assertNull($result, 'Expected null when index is not an instance of StringIndex.');
    }

    public function testMustReturnStringIndexRecordWhenValidStringIndexProvided(): void
    {
        $factory = new StringIndexRecordFactoryCase();

        $id = $this->prophesize(Id::class)->reveal();

        $aggregateId = $this->prophesize(Id::class)->reveal(); // Mock of the Id object for the `aggregateId` method
        $index = $this->prophesize(StringIndex::class);
        $index->aggregateId()->willReturn($aggregateId); // Mocking `aggregateId` to return an Id object
        $index->value()->willReturn('test-value');

        $indexMeta = new IndexMeta('test-index-name', 'Test\Index\FQN', 'Test\Entity\FQN');
        $entityMeta = new EntityMeta('test-entity-name', 'Test\Entity\FQN', 'Aggregate\Root\FQN');
        $recordedAt = $this->prophesize(IDateTime::class)->reveal();

        $result = $factory->handle(
            $id,
            $index->reveal(),
            $indexMeta,
            $entityMeta,
            $recordedAt,
        );

        assertInstanceOf(
            StringIndexRecord::class,
            $result,
            'Expected a valid instance of StringIndexRecord.',
        );

        assertSame($id, $result->recordId);
        assertSame('test-index-name', $result->indexName);
        assertSame('test-entity-name', $result->entityName);
        assertSame($aggregateId, $result->entityId); // Correctly asserting the Id object
        assertSame('test-value', $result->value);
        assertSame($recordedAt, $result->recordedAt);
    }
}
