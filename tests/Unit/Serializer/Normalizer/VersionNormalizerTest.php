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

final class VersionNormalizerTest extends AppTestCase
{
    private VersionNormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new VersionNormalizer();
    }

    public function itNormalizesVersionToInteger(): void
    {
        $version = new Version(42); // Assuming Version accepts an int in constructor
        $result = $this->instance->normalize($version);

        assertSame(42, $result);
    }

    public function itThrowsExceptionForNonVersionData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*Version", "string" provided/');

        $this->instance->normalize('not-a-version');
    }

    public function itThrowsExceptionForInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*Version", "stdClass" provided/');

        $this->instance->normalize(new \stdClass());
    }

    public function itSupportsNormalizationForVersionClass(): void
    {
        $version = new Version(42);
        assertTrue($this->instance->supportsNormalization($version));
        assertFalse($this->instance->supportsNormalization(42));
        assertFalse($this->instance->supportsNormalization(new \stdClass()));
    }

    public function itReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(Version::class, $supportedTypes);
        assertTrue($supportedTypes[Version::class]);
        assertCount(1, $supportedTypes);
    }
}
