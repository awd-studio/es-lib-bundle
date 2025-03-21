<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Serializer\Domain\Exception;

use AwdEs\Exception\RuntimeException;

final class AwdDeserializationError extends RuntimeException
{
    public function __construct(string $deserializableObjectType, ?\Throwable $previous = null)
    {
        $message = \sprintf('Unable to deserialize object of type "%s": %s', $deserializableObjectType, $previous?->getMessage() ?? 'Unknown reason.');

        parent::__construct($message, $previous?->getCode() ?? 0, $previous);
    }
}
