<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Serializer\Denormalizer;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\ValueObject\Id;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class UlidIdDenormalizer implements DenormalizerInterface
{
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Id
    {
        if (UlidId::class !== $type) {
            throw new InvalidArgumentException(\sprintf('Expects to "%s", "%s" provided.', UlidId::class, get_debug_type($data)));
        }

        if (false === \is_string($data)) {
            throw new InvalidArgumentException(\sprintf('$data is expects to be a string, "%s" provided.', get_debug_type($data)));
        }

        return UlidId::fromString($data);
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return UlidId::class === $type;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [Id::class => true, UlidId::class => true];
    }
}
