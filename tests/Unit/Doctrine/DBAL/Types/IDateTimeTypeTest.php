<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Doctrine\DBAL\Types;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\IDateTimeType;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Prophecy\Prophecy\ObjectProphecy;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Doctrine\DBAL\Types\IDateTimeType
 *
 * @internal
 */
final class IDateTimeTypeTest extends AppTestCase
{
    private IDateTimeType $instance;
    private AbstractPlatform|ObjectProphecy $platform;

    #[\Override]
    protected function setUp(): void
    {
        $this->instance = new IDateTimeType();
        $this->platform = $this->prophesize(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        assertSame('awd_datetime', $this->instance->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        assertTrue($this->instance->requiresSQLCommentHint($this->platform->reveal()));
    }

    public function testGetSQLDeclarationForGenericPlatform(): void
    {
        assertSame('DATETIME(6)', $this->instance->getSQLDeclaration([], $this->platform->reveal()));
    }

    public function testGetSQLDeclarationForPostgreSQL(): void
    {
        $pgPlatform = $this->prophesize(PostgreSQLPlatform::class);
        assertSame('TIMESTAMP(6) WITHOUT TIME ZONE', $this->instance->getSQLDeclaration([], $pgPlatform->reveal()));
    }

    public function testGetSQLDeclarationWithVersion(): void
    {
        assertSame('TIMESTAMP', $this->instance->getSQLDeclaration(['version' => true], $this->platform->reveal()));
    }

    public function testConvertToPHPValueWithNull(): void
    {
        assertNull($this->instance->convertToPHPValue(null, $this->platform->reveal()));
    }

    public function testConvertToPHPValueWithValidString(): void
    {
        $dateString = '2025-03-08 12:34:56.123456';
        $result = $this->instance->convertToPHPValue($dateString, $this->platform->reveal());

        assertInstanceOf(IDateTime::class, $result);
        assertSame($dateString, $result->format(IDateTime::DATABASE_DATETIME_MICRO_TIME_FORMAT));
    }

    public function testConvertToPHPValueWithNonStringThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert PHP value of type "int" to type "awd_datetime". Expected one of: null, string');

        $this->instance->convertToPHPValue(123, $this->platform->reveal());
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        assertNull($this->instance->convertToDatabaseValue(null, $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueWithIDateTime(): void
    {
        $dateString = '2025-03-08 12:34:56.123456';
        $dateTime = DateTime::fromString($dateString);

        $result = $this->instance->convertToDatabaseValue($dateTime, $this->platform->reveal());
        assertSame($dateString, $result);
    }

    public function testConvertToDatabaseValueWithNonIDateTimeThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert PHP value of type "string" to type "awd_datetime". Expected one of: null, ' . IDateTime::class);

        $this->instance->convertToDatabaseValue('not-a-datetime', $this->platform->reveal());
    }
}
