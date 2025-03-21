<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Denormalizer;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\Serializer\Denormalizer\UlidIdDenormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Id;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Denormalizer\UlidIdDenormalizer
 */
final class UlidIdDenormalizerTest extends AppTestCase
{
    private UlidIdDenormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new UlidIdDenormalizer();
    }

    public function testDenormalizesValidStringToUlidId(): void
    {
        $data = '0195af26-5d96-56fb-77ee-b88f3f158624'; // Example ULID string
        $result = $this->instance->denormalize($data, Id::class);

        assertInstanceOf(Id::class, $result);
        assertInstanceOf(UlidId::class, $result); // UlidId implements Id
        assertSame($data, $result->__toString()); // Assuming UlidId has toString()
    }

    public function testThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize(123, Id::class);
    }

    public function testThrowsExceptionForNonStringData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize(42, Id::class);
    }

    public function testSupportsDenormalizationForIdClass(): void
    {
        assertTrue($this->instance->supportsDenormalization('0195af26-5d96-56fb-77ee-b88f3f158624', Id::class));
        assertFalse($this->instance->supportsDenormalization('0195af26-5d96-56fb-77ee-b88f3f158624', \stdClass::class));
    }

    public function testReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(Id::class, $supportedTypes);
        assertArrayHasKey(UlidId::class, $supportedTypes);
        assertTrue($supportedTypes[Id::class]);
        assertTrue($supportedTypes[UlidId::class]);
        assertCount(2, $supportedTypes);
    }

    public function testSupportsDenormalizationReturnsFalseForUnsupportedClass(): void
    {
        assertFalse($this->instance->supportsDenormalization('dummy-string', \stdClass::class));
        assertFalse($this->instance->supportsDenormalization('dummy-string', 'InvalidClass'));
    }
}
