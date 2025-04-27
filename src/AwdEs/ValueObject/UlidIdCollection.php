<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\ValueObject;

use AwdEs\ValueObject\Id;
use AwdEs\ValueObject\IdCollection;

final readonly class UlidIdCollection implements IdCollection
{
    /** @var array<string, UlidId> */
    private array $ids;

    public function __construct(
        UlidId ...$ids,
    ) {
        $this->ids = array_combine(array_map(static fn(UlidId $id) => (string) $id, $ids), $ids);
    }

    #[\Override]
    public function count(): int
    {
        return \count($this->ids);
    }

    #[\Override]
    public function append(Id $id): static
    {
        if (!$id instanceof UlidId) {
            throw new \InvalidArgumentException(\sprintf('Only UlidId is supported. %s provided.', get_debug_type($id)));
        }

        if (true === $this->has($id)) {
            return $this;
        }

        $newIds = $this->ids;
        $newIds[(string) $id] = $id;

        return new self(...array_values($newIds));
    }

    #[\Override]
    public function has(Id $id): bool
    {
        return \array_key_exists((string) $id, $this->ids);
    }

    #[\Override]
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    #[\Override]
    public function diff(IdCollection $other): IdCollection
    {
        if (!$other instanceof static) {
            throw new \InvalidArgumentException(\sprintf('Only %s is supported. %s provided.', self::class, get_debug_type($other)));
        }

        $diff = array_diff_key($this->ids, $other->ids);

        return new self(...array_values($diff));
    }

    #[\Override]
    public function getIterator(): \Generator
    {
        yield from $this->ids;
    }
}
