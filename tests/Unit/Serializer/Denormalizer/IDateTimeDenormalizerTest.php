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
 *
 * @internal
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

    public function testDenormalizesValidStringToIDateTime(): void
    {
        $data = '2023-10-15 14:30:00';
        $result = $this->instance->denormalize($data, IDateTime::class);

        assertInstanceOf(IDateTime::class, $result);
        assertInstanceOf(DateTime::class, $result); // Assuming DateTime implements IDateTime
        assertSame($data, $result->__toString());
    }

    public function testThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize(123, IDateTime::class);
    }

    public function testThrowsExceptionForNonStringData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize(42, IDateTime::class);
    }

    public function testThrowsExceptionWhenInputIsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize(null, IDateTime::class);
    }

    public function testSupportsDenormalizationForIDateTimeClass(): void
    {
        // Supported class
        assertTrue($this->instance->supportsDenormalization('2023-10-15 14:30:00', IDateTime::class));

        // Unsupported class
        assertFalse($this->instance->supportsDenormalization('2023-10-15 14:30:00', \stdClass::class));
        assertFalse($this->instance->supportsDenormalization('dummy-data', 'NonExistentType'));
    }

    public function testReturnsSupportedTypes(): void
    {
        $supportedTypes = $this->instance->getSupportedTypes(null);

        assertCount(2, $supportedTypes);
        assertArrayHasKey(IDateTime::class, $supportedTypes);
        assertArrayHasKey(DateTime::class, $supportedTypes);
        assertTrue($supportedTypes[DateTime::class]);
        assertTrue($supportedTypes[IDateTime::class]);
    }

    public function testThrowsExceptionForInvalidDateString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize('invalid-date', IDateTime::class);
    }

    public function testThrowsExceptionForImpossibleDate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize('30-30-3030 99:99:99', IDateTime::class);
    }

    public function testDenormalizesValidStringToDateTime(): void
    {
        $data = '2023-10-15 14:30:00';
        $result = $this->instance->denormalize($data, DateTime::class);

        assertInstanceOf(DateTime::class, $result);
        assertSame($data, $result->__toString());
    }

    public function testDenormalizesLeapYearDates(): void
    {
        $data = '2024-02-29 12:00:00';
        $result = $this->instance->denormalize($data, IDateTime::class);

        assertInstanceOf(DateTime::class, $result);
        assertSame($data, $result->__toString());
    }

    public function testDenormalizationWithCustomFormat(): void
    {
        $data = '15-10-2023 14:30:00';
        $context = ['datetime_format' => 'd-m-Y H:i:s'];
        $result = $this->instance->denormalize($data, IDateTime::class, null, $context);

        assertInstanceOf(IDateTime::class, $result);
        assertInstanceOf(DateTime::class, $result);
        assertSame('2023-10-15 14:30:00', $result->__toString());
    }

    public function testDenormalizationFailsForUnsupportedType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instance->denormalize('some-data', \stdClass::class);
    }
}
