<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Infrastructure;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\System\Domain\AppClock;

final readonly class DateTimeAppClock implements AppClock
{
    #[\Override]
    public function now(): IDateTime
    {
        return DateTime::now();
    }
}
