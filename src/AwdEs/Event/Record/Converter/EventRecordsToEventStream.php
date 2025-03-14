<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Record\Converter;

use AwdEs\Event\EventStream;

interface EventRecordsToEventStream
{
    /**
     * @param array<\AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord> $records
     */
    public function convert(array $records): EventStream;
}
