<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Doctrine\DBAL\Types;

use AwdEs\EsLibBundle\Doctrine\DBAL\Types\JsonbOrJsonType;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Prophecy\Prophecy\ObjectProphecy;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Doctrine\DBAL\Types\JsonbOrJsonType
 *
 * @internal
 */
final class JsonbOrJsonTypeTest extends AppTestCase
{
    private JsonbOrJsonType $instance;
    private AbstractPlatform|ObjectProphecy $platformProphecy;

    #[\Override]
    protected function setUp(): void
    {
        $this->instance = new JsonbOrJsonType();
        $this->platformProphecy = $this->prophesize(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        assertSame('jsonb_or_json', $this->instance->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        assertTrue($this->instance->requiresSQLCommentHint($this->platformProphecy->reveal()));
    }

    public function testGetSQLDeclarationForGenericPlatform(): void
    {
        $this->platformProphecy
            ->hasDoctrineTypeMappingFor('jsonb')
            ->willReturn(false)
        ;

        assertSame('json', $this->instance->getSQLDeclaration([], $this->platformProphecy->reveal()));
    }

    public function testGetSQLDeclarationForPostgreSQL(): void
    {
        $pgPlatform = $this->prophesize(PostgreSQLPlatform::class);
        $pgPlatform
            ->hasDoctrineTypeMappingFor('jsonb')
            ->willReturn(true)
            ->shouldBeCalledOnce()
        ;

        assertSame('jsonb', $this->instance->getSQLDeclaration([], $pgPlatform->reveal()));
    }

    public function testConvertToPHPValueWithNull(): void
    {
        assertNull($this->instance->convertToPHPValue(null, $this->platformProphecy->reveal()));
    }

    public function testConvertToPHPValueWithValidJsonString(): void
    {
        $jsonString = '{"key": "value", "num": 42}';
        $expected = ['key' => 'value', 'num' => 42];

        $result = $this->instance->convertToPHPValue($jsonString, $this->platformProphecy->reveal());
        assertSame($expected, $result);
    }

    public function testConvertToPHPValueWithInvalidJsonThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Invalid JSON data: {"broken":');

        $this->instance->convertToPHPValue('{"broken":', $this->platformProphecy->reveal());
    }

    public function testConvertToPHPValueWithNonStringThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Expected value of type string, but got int.');

        $this->instance->convertToPHPValue(123, $this->platformProphecy->reveal());
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        assertNull($this->instance->convertToDatabaseValue(null, $this->platformProphecy->reveal()));
    }

    public function testConvertToDatabaseValueWithArray(): void
    {
        $array = ['key' => 'value', 'num' => 42];
        $expected = '{"key":"value","num":42}';

        $result = $this->instance->convertToDatabaseValue($array, $this->platformProphecy->reveal());
        assertSame($expected, $result);
    }

    public function testConvertToDatabaseValueWithNonArrayThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert PHP value of type "string" to type "jsonb_or_json". Expected one of: null, json');

        $this->instance->convertToDatabaseValue('not-an-array', $this->platformProphecy->reveal());
    }

    public function testConvertToDatabaseValueWithUnencodableValueThrowsException(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessageMatches('/Could not convert PHP value of type "array" to type "jsonb_or_json". The JSON is invalid:/');

        // JSON_THROW_ON_ERROR will fail with circular references or invalid UTF-8
        $array = [];
        $array['self'] = &$array; // Circular reference
        $this->instance->convertToDatabaseValue($array, $this->platformProphecy->reveal());
    }
}
