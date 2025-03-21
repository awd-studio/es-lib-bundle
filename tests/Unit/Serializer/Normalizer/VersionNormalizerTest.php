<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Normalizer;

use AwdEs\EsLibBundle\Serializer\Normalizer\VersionNormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Version;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Normalizer\VersionNormalizer
 *
 * @internal
 */
final class VersionNormalizerTest extends AppTestCase
{
    private VersionNormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new VersionNormalizer();
    }

    public function testNormalizesVersionToInteger(): void
    {
        $version = new Version(42); // Assuming Version accepts an int in its constructor
        $result = $this->instance->normalize($version);

        assertSame(42, $result);
    }

    public function testThrowsExceptionForNonVersionData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->normalize('not-a-version');
    }

    public function testThrowsExceptionForInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->normalize(new \stdClass());
    }

    public function testSupportsNormalizationForVersionClass(): void
    {
        $version = new Version(42);
        assertTrue($this->instance->supportsNormalization($version));
        assertFalse($this->instance->supportsNormalization(42));
        assertFalse($this->instance->supportsNormalization(new \stdClass()));
    }

    public function testReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(Version::class, $supportedTypes);
        assertTrue($supportedTypes[Version::class]);
        assertCount(1, $supportedTypes);
    }
}
