<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Id;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use Symfony\Component\Uid\Ulid;

final readonly class SymfonyUlidIdQueue implements UlidIdQueue
{
    #[\Override]
    public function next(): UlidId
    {
        return new UlidId(new Ulid());
    }
}
