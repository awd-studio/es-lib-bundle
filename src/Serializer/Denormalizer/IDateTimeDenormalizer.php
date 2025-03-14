<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Denormalizer;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class IDateTimeDenormalizer implements DenormalizerInterface
{
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): IDateTime
    {
        if (IDateTime::class !== $type) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', IDateTime::class, get_debug_type($data)));
        }

        if (false === \is_string($data)) {
            throw new InvalidArgumentException(\sprintf('$data is expects to be a string, "%s" provided.', get_debug_type($data)));
        }

        return DateTime::fromString($data);
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return IDateTime::class === $type;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [IDateTime::class => true];
    }
}
