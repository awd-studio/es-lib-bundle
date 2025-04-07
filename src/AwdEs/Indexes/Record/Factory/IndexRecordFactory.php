<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory;

use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\IndexRecord;
use AwdEs\Indexes\Index;

interface IndexRecordFactory
{
    /**
     * @template T
     *
     * @param Index<T> $index
     *
     * @throws \AwdEs\Indexes\Recording\Exception\IndexRecordingError
     */
    public function build(Index $index, IDateTime $recordedAt): IndexRecord;
}
