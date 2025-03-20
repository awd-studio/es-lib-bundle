<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Serializer\Denormalizer;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\Serializer\Denormalizer\IDateTimeDenormalizer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Serializer\Denormalizer\IDateTimeDenormalizer
 */
final class IDateTimeDenormalizerTest extends AppTestCase
{
    private IDateTimeDenormalizer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new IDateTimeDenormalizer();
    }

    public function itDenormalizesValidStringToIDateTime(): void
    {
        $data = '2023-10-15 14:30:00';
        $result = $this->instance->denormalize($data, IDateTime::class);

        assertInstanceOf(IDateTime::class, $result);
        assertInstanceOf(DateTime::class, $result); // Assuming DateTime implements IDateTime
        assertSame($data, $result->__toString()); // Assuming IDateTime has toString()
    }

    public function itThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expects to ".*IDateTime", "integer" provided/');

        $this->instance->denormalize(123, IDateTime::class);
    }

    public function itThrowsExceptionForNonStringData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/\$data is expects to be a string, "integer" provided/');

        $this->instance->denormalize(42, IDateTime::class);
    }

    public function itSupportsDenormalizationForIDateTimeClass(): void
    {
        assertTrue($this->instance->supportsDenormalization('2023-10-15', IDateTime::class));
        assertFalse($this->instance->supportsDenormalization('2023-10-15', \stdClass::class));
    }

    public function itReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertArrayHasKey(IDateTime::class, $supportedTypes);
        assertTrue($supportedTypes[IDateTime::class]);
        assertCount(1, $supportedTypes);
    }
}
