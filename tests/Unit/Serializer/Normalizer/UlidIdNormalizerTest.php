<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Normalizer;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\Serializer\Normalizer\UlidIdNormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Normalizer\UlidIdNormalizer
 */
final class UlidIdNormalizerTest extends AppTestCase
{
    private UlidIdNormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new UlidIdNormalizer();
    }

    public function testNormalizesUlidIdToString(): void
    {
        $ulidId = UlidId::fromString('0195af26-5d96-56fb-77ee-b88f3f158624'); // Example ULID
        $result = $this->instance->normalize($ulidId);

        assertSame('0195af26-5d96-56fb-77ee-b88f3f158624', $result);
    }

    public function testThrowsExceptionForNonUlidIdData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->normalize('0195af26-5d96-56fb-77ee-b88f3f158624');
    }

    public function testThrowsExceptionForInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->normalize(new \stdClass());
    }

    public function testSupportsNormalizationForUlidIdClass(): void
    {
        $ulidId = UlidId::fromString('0195af26-5d96-56fb-77ee-b88f3f158624');
        assertTrue($this->instance->supportsNormalization($ulidId));
        assertFalse($this->instance->supportsNormalization('0195af26-5d96-56fb-77ee-b88f3f158624'));
        assertFalse($this->instance->supportsNormalization(new \stdClass()));
    }

    public function testReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(UlidId::class, $supportedTypes);
        assertTrue($supportedTypes[UlidId::class]);
        assertCount(2, $supportedTypes);
    }
}
