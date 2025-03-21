<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\System\Infrastructure;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\System\Infrastructure\DateTimeAppClock;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;

use function PHPUnit\Framework\assertInstanceOf;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\System\Infrastructure\DateTimeAppClock
 *
 * @internal
 */
final class DateTimeAppClockTest extends AppTestCase
{
    private DateTimeAppClock $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = new DateTimeAppClock();
    }

    public function testReturnsCurrentDateTime(): void
    {
        $result = $this->instance->now();

        assertInstanceOf(IDateTime::class, $result);
        assertInstanceOf(DateTime::class, $result);
    }
}
