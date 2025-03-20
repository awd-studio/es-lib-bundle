<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Denormalizer;

use AwdEs\EsLibBundle\Serializer\Denormalizer\VersionDenormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Version;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Denormalizer\VersionDenormalizer
 */
final class VersionDenormalizerTest extends AppTestCase
{
    private VersionDenormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new VersionDenormalizer();
    }

    public function itDenormalizesValidIntegerToVersion(): void
    {
        $data = 42;
        $result = $this->instance->denormalize($data, Version::class);

        assertInstanceOf(Version::class, $result);
        assertSame($data, $result->value);
    }

    public function itThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*Version", "string" provided/');

        $this->instance->denormalize('not-an-int', Version::class);
    }

    public function itThrowsExceptionForNonIntegerData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/\$data is expects to be a positive integer, "string" provided/');

        $this->instance->denormalize('42', Version::class);
    }

    public function itThrowsExceptionForNonPositiveInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/\$data is expects to be a positive integer, "0" provided/');

        $this->instance->denormalize(0, Version::class);
    }

    public function itThrowsExceptionForNegativeInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/\$data is expects to be a positive integer, "-1" provided/');

        $this->instance->denormalize(-1, Version::class);
    }

    public function itSupportsDenormalizationForVersionClass(): void
    {
        assertTrue($this->instance->supportsDenormalization(42, Version::class));
        assertFalse($this->instance->supportsDenormalization(42, \stdClass::class));
    }

    public function itReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(Version::class, $supportedTypes);
        assertTrue($supportedTypes[Version::class]);
        assertCount(1, $supportedTypes);
    }
}
