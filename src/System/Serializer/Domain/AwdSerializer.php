<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\System\Serializer\Domain;

interface AwdSerializer
{
    /**
     * @return array<string, mixed>
     */
    public function serialize(object $object): array;

    /**
     * @template T of object
     *
     * @param class-string<T>      $type
     * @param array<string, mixed> $data
     *
     * @return T
     */
    public function deserialize(string $type, array $data): object;
}
