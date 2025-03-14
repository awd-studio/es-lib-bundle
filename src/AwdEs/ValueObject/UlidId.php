<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\ValueObject;

use AwdEs\ValueObject\Id;
use Symfony\Component\Uid\Ulid;

final readonly class UlidId implements Id
{
    public function __construct(
        public Ulid $uuid,
    ) {}

    #[\Override]
    public function isSame(Id $anotherId): bool
    {
        // @phpstan-ignore instanceof.alwaysTrue
        return $anotherId instanceof self && $this->uuid->equals($anotherId->uuid);
    }

    #[\Override]
    public static function fromString(string $value): static
    {
        return new self(Ulid::fromString($value));
    }

    public function toBinary(): string
    {
        return $this->uuid->toBinary();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->uuid->toRfc4122();
    }
}
