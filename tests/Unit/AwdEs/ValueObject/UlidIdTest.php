<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\AwdEs\ValueObject;

use AwdEs\ValueObject\Id;
use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Symfony\Component\Uid\Ulid;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId
 */
final class UlidIdTest extends AppTestCase
{
    private UlidId $instance;

    #[\Override]
    protected function setUp(): void
    {
        $ulid = Ulid::fromString('01913f7e-d0b5-44cf-04d8-581e81b2c4cb');
        $this->instance = new UlidId($ulid);
    }

    public function testFromString(): void
    {
        $ulidString = '01913f7e-d0b5-44cf-04d8-581e81b2c4cb';
        $result = UlidId::fromString($ulidString);

        assertInstanceOf(UlidId::class, $result);
        assertSame($ulidString, (string) $result);
    }

    public function testIsSameWithIdenticalUlid(): void
    {
        $sameUlid = UlidId::fromString('01913f7e-d0b5-44cf-04d8-581e81b2c4cb');
        assertTrue($this->instance->isSame($sameUlid));
    }

    public function testIsSameWithDifferentUlid(): void
    {
        $differentUlid = UlidId::fromString('01J4ZQXP5N8K7G9P2R3T0V5H6C');
        assertFalse($this->instance->isSame($differentUlid));
    }

    public function testIsSameWithDifferentIdType(): void
    {
        $mockId = new class implements Id {
            public function isSame(Id $anotherId): bool
            {
                return false;
            }
            public static function fromString(string $value): static
            {
                return new static();
            }
            public function __toString(): string
            {
                return 'mock';
            }
        };

        assertFalse($this->instance->isSame($mockId));
    }

    public function testToBinary(): void
    {
        $expectedBinary = $this->instance->uuid->toBinary(); // 16-byte binary representation
        assertSame($expectedBinary, $this->instance->toBinary());
    }

    public function testToString(): void
    {
        $expectedString = '01913f7e-d0b5-44cf-04d8-581e81b2c4cb';
        assertSame($expectedString, (string) $this->instance);
    }

    public function testFromStringWithInvalidUlidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid ULID: "01H8W7Q8K9P5N2M4R6T0J3X1Y"/');

        UlidId::fromString('01H8W7Q8K9P5N2M4R6T0J3X1Y');
    }
}
