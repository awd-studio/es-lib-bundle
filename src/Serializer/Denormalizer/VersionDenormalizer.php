<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Denormalizer;

use AwdEs\ValueObject\Version;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class VersionDenormalizer implements DenormalizerInterface
{
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Version
    {
        if (Version::class !== $type) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', Version::class, get_debug_type($data)));
        }

        if (false === \is_int($data)) {
            throw new InvalidArgumentException(\sprintf('$data is expects to be a positive integer, "%s" provided.', get_debug_type($data)));
        }

        if (0 >= $data) {
            throw new InvalidArgumentException(\sprintf('$data is expects to be a positive integer, "%d" provided.', $data));
        }

        return new Version($data);
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Version::class === $type;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [Version::class => true];
    }
}
