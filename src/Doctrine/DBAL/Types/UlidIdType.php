<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Doctrine\DBAL\Types;

use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class UlidIdType extends Type
{
    public const string NAME = 'ulid_id';

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UlidId
    {
        if ($value instanceof UlidId || null === $value) {
            return $value;
        }

        if (false === \is_string($value)) {
            throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". Expected one of: null, string', get_debug_type($value), self::NAME));
        }

        try {
            return UlidId::fromString($value);
        } catch (\InvalidArgumentException $e) {
            throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". %s', get_debug_type($value), self::NAME, $e->getMessage()), $e->getCode(), $e);
        }
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof UlidId) {
            return $this->hasNativeGuidType($platform) ? $value->__toString() : $value->toBinary();
        }

        throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". Expected one of: null, %s', self::NAME, get_debug_type($value), UlidId::class));
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($this->hasNativeGuidType($platform)) {
            return $platform->getGuidTypeDeclarationSQL($column);
        }

        return $platform->getBinaryTypeDeclarationSQL([
            'length' => '16',
            'fixed' => true,
        ]);
    }

    #[\Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }

    private function hasNativeGuidType(AbstractPlatform $platform): bool
    {
        return $platform->getGuidTypeDeclarationSQL([]) !== $platform->getStringTypeDeclarationSQL(['fixed' => true, 'length' => 36]);
    }
}
