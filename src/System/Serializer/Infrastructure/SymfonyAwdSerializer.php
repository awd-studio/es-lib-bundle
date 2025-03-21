<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Serializer\Infrastructure;

use AwdEs\EsLibBundle\System\Serializer\Domain\AwdSerializer;
use AwdEs\EsLibBundle\System\Serializer\Domain\Exception\AwdDeserializationError;
use AwdEs\EsLibBundle\System\Serializer\Domain\Exception\AwdSerializationError;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
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
        try {
            $result = $this->normalizer->normalize($object, 'array');
        } catch (ExceptionInterface $e) {
            throw new AwdSerializationError($object::class, $e);
        }

        if (false === \is_array($result)) {
            throw new AwdSerializationError($object::class);
        }

        /* @phpstan-ignore return.type */
        return $result;
    }

    #[\Override]
    public function deserialize(string $type, array $data): object
    {
        try {
            $result = $this->denormalizer->denormalize($data, $type);
        } catch (ExceptionInterface $e) {
            throw new AwdDeserializationError($type, $e);
        }

        if (false === \is_object($result) || false === is_a($result, $type, true)) {
            throw new AwdDeserializationError($type);
        }

        return $result;
    }
}
