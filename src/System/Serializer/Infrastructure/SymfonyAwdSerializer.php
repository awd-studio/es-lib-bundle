<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Serializer\Infrastructure;

use AwdEs\EsLibBundle\System\Serializer\Domain\AwdSerializer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class SymfonyAwdSerializer implements AwdSerializer
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private NormalizerInterface $normalizer,
    ) {}

    #[\Override]
    public function serialize(object $object): array
    {
        // @phpstan-ignore return.type
        return $this->normalizer->normalize($object, 'array');
    }

    #[\Override]
    public function deserialize(string $type, array $data): object
    {
        // @phpstan-ignore return.type
        return $this->denormalizer->denormalize($data, $type);
    }
}
