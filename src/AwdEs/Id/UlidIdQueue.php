<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Id;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;

interface UlidIdQueue
{
    public function next(): UlidId;
}
