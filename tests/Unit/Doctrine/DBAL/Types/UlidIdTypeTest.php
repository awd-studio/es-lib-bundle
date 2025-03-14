<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\Doctrine\DBAL\Types;

use AwdEs\ValueObject\Id;
use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Symfony\Component\Uid\Ulid;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\Doctrine\DBAL\Types\UlidIdType
 */
final class UlidIdTypeTest extends AppTestCase
{
    private UlidId $instance;

    #[\Override]
    protected function setUp(): void
    {
        $ulid = Ulid::fromString('01957575-f44b-c994-9c31-40fb07e316e1');
        $this->instance = new UlidId($ulid);
    }

    public function testFromString(): void
    {
        $ulidString = '01957575-f44b-c994-9c31-40fb07e316e1';
        $result = UlidId::fromString($ulidString);

        assertInstanceOf(UlidId::class, $result);
        assertSame($ulidString, (string) $result);
    }

    public function testIsSameWithIdenticalUlid(): void
    {
        $sameUlid = UlidId::fromString('01957575-f44b-c994-9c31-40fb07e316e1');
        assertTrue($this->instance->isSame($sameUlid));
    }

    public function testIsSameWithDifferentUlid(): void
    {
        $differentUlid = UlidId::fromString('01957575-f44b-c995-9c31-40fb07e316e2');
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
        $expectedString = '01957575-f44b-c994-9c31-40fb07e316e1';
        assertSame($expectedString, (string) $this->instance);
    }

    public function testFromStringWithInvalidUlidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid ULID: "invalid-ulid"/');

        UlidId::fromString('invalid-ulid');
    }
}
