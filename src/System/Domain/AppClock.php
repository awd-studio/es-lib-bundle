<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Domain;

use Awd\ValueObject\IDateTime;

interface AppClock
{
    public function now(): IDateTime;
}
