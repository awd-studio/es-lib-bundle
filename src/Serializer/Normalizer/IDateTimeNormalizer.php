<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Normalizer;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class IDateTimeNormalizer implements NormalizerInterface
{
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        if (false === ($data instanceof IDateTime)) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', IDateTime::class, get_debug_type($data)));
        }

        return $data->format(IDateTime::DATABASE_DATETIME_MICRO_TIME_FORMAT);
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof IDateTime;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [IDateTime::class => true, DateTime::class => true];
    }
}
