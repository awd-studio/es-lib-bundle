<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Normalizer;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\Serializer\Normalizer\IDateTimeNormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Normalizer\IDateTimeNormalizer
 */
final class IDateTimeNormalizerTest extends AppTestCase
{
    private IDateTimeNormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new IDateTimeNormalizer();
    }

    public function itNormalizesIDateTimeToString(): void
    {
        $dateTime = $this->prophesize(IDateTime::class);
        $dateTime->format(IDateTime::DATABASE_DATETIME_MICRO_TIME_FORMAT)
            ->willReturn('2023-10-15 14:30:00.123456');

        $result = $this->instance->normalize($dateTime->reveal());

        assertSame('2023-10-15 14:30:00.123456', $result);
    }

    public function itThrowsExceptionForNonIDateTimeData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*IDateTime", "string" provided/');

        $this->instance->normalize('2023-10-15');
    }

    public function itThrowsExceptionForInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*IDateTime", "stdClass" provided/');

        $this->instance->normalize(new \stdClass());
    }

    public function itSupportsNormalizationForIDateTimeClass(): void
    {
        $dateTime = $this->prophesize(IDateTime::class);
        assertTrue($this->instance->supportsNormalization($dateTime->reveal()));
        assertFalse($this->instance->supportsNormalization('2023-10-15'));
        assertFalse($this->instance->supportsNormalization(new \stdClass()));
    }

    public function itReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(IDateTime::class, $supportedTypes);
        assertTrue($supportedTypes[IDateTime::class]);
        assertCount(1, $supportedTypes);
    }
}
