<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\ValueObject;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidIdCollection;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Id;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\ValueObject\UlidIdCollection
 *
 * @internal
 */
final class UlidIdCollectionTest extends AppTestCase
{
    private UlidId $id1;
    private UlidId $id2;

    #[\Override]
    protected function setUp(): void
    {
        $this->id1 = UlidId::fromString('123e4567-e89b-12d3-a456-426614174000');
        $this->id2 = UlidId::fromString('123e4567-e89b-12d3-a456-426614174111');
    }

    public function testMustCountCorrectNumberOfIds(): void
    {
        $collection = new UlidIdCollection($this->id1, $this->id2);

        assertSame(2, $collection->count());
    }

    public function testMustAppendValidUlidId(): void
    {
        $collection = new UlidIdCollection($this->id1);

        $updatedCollection = $collection->append($this->id2);

        assertSame(2, $updatedCollection->count());
        assertTrue($updatedCollection->has($this->id1));
        assertTrue($updatedCollection->has($this->id2));
    }

    public function testMustNotAppendDuplicateUlidId(): void
    {
        $collection = new UlidIdCollection($this->id1);

        $updatedCollection = $collection->append($this->id1);

        assertSame(1, $updatedCollection->count());
        assertTrue($updatedCollection->has($this->id1));
    }

    public function testMustThrowExceptionForNonUlidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Replace createMock with Prophecy
        $nonUlidId = $this->prophesize(Id::class)->reveal();

        $collection = new UlidIdCollection();
        $collection->append($nonUlidId);
    }

    public function testMustConfirmUlidIdExists(): void
    {
        $collection = new UlidIdCollection($this->id1);

        assertTrue($collection->has($this->id1));
        assertFalse($collection->has($this->id2));
    }

    public function testMustIdentifyCollectionAsEmpty(): void
    {
        $collection = new UlidIdCollection();

        assertTrue($collection->isEmpty());
    }

    public function testMustIdentifyCollectionAsNonEmpty(): void
    {
        $collection = new UlidIdCollection($this->id1);

        assertFalse($collection->isEmpty());
    }

    public function testMustReturnCorrectDifferenceBetweenCollections(): void
    {
        $collection1 = new UlidIdCollection($this->id1, $this->id2);
        $collection2 = new UlidIdCollection($this->id1);

        $diff = $collection1->diff($collection2);

        assertSame(1, $diff->count());
        assertTrue($diff->has($this->id2));
        assertFalse($diff->has($this->id1));
    }

    public function testMustIterateOverAllUlidIds(): void
    {
        $collection = new UlidIdCollection($this->id1, $this->id2);

        $ids = iterator_to_array($collection->getIterator());

        assertCount(2, $ids);
        assertSame([$this->id1, $this->id2], $ids);
    }

    public function testMustIterateWithCorrectKeys(): void
    {
        // Create a collection with two IDs
        $collection = new UlidIdCollection($this->id1, $this->id2);

        // Get expected keys (string representations of UlidId objects)
        $expectedKeys = [(string) $this->id1, (string) $this->id2];

        // Iterate over the collection and collect the keys
        $actualKeys = [];
        foreach ($collection as $value) {
            $actualKeys[] = (string) $value;
        }

        // Assert the keys match
        assertSame($expectedKeys, $actualKeys);
    }
}
