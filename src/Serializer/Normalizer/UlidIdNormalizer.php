<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Normalizer;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class UlidIdNormalizer implements NormalizerInterface
{
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        if (false === ($data instanceof UlidId)) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', UlidId::class, get_debug_type($data)));
        }

        return (string) $data;
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof UlidId;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [UlidId::class => true];
    }
}
