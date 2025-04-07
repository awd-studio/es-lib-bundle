<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Indexes\Recording;

use Awd\ValueObject\IDateTime;
use AwdEs\Indexes\Index;
use AwdEs\Indexes\Recording\IndexRecorder;

final readonly class IndexRecordIndexRecorder implements IndexRecorder
{
    #[\Override]
    public function record(Index $index, IDateTime $recordedAt): void
    {
        // TODO: Implement record() method.
    }
}
