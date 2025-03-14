<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Event\Record\Factory;

use AwdEs\EsLibBundle\AwdEs\Event\Record\EntityEventRecord;
use AwdEs\Event\EntityEvent;

interface EntityEventRecordFactory
{
    /**
     * @throws Exception\EntityEventRecordBuildingError
     */
    public function build(EntityEvent $event): EntityEventRecord;
}
