<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Serializer\Domain\Exception;

use AwdEs\Exception\RuntimeException;

final class AwdSerializationError extends RuntimeException
{
    public function __construct(string $serializableObjectType, ?\Throwable $previous = null)
    {
        $message = \sprintf('Unable to serialize object of type "%s": %s', $serializableObjectType, $previous?->getMessage() ?? 'Unknown reason.');

        parent::__construct($message, $previous?->getCode() ?? 0, $previous);
    }
}
