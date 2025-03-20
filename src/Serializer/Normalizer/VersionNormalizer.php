<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Normalizer;

use AwdEs\ValueObject\Version;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class VersionNormalizer implements NormalizerInterface
{
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): int
    {
        if (false === ($data instanceof Version)) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', Version::class, get_debug_type($data)));
        }

        return $data->value;
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Version;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [Version::class => true];
    }
}
