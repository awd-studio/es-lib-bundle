<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Doctrine\DBAL\Types;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class IDateTimeType extends Type
{
    public const string NAME = 'awd_datetime';

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?IDateTime
    {
        if (null === $value) {
            return null;
        }

        if (false === \is_string($value)) {
            throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". Expected one of: null, string', get_debug_type($value), self::NAME));
        }

        return DateTime::fromString($value);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof IDateTime) {
            return $value->format(IDateTime::DATABASE_DATETIME_MICRO_TIME_FORMAT);
        }

        throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". Expected one of: null, %s', get_debug_type($value), self::NAME, IDateTime::class));
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (isset($column['version']) && true === $column['version']) {
            return 'TIMESTAMP';
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return 'TIMESTAMP(6) WITHOUT TIME ZONE';
        }

        return 'DATETIME(6)';
    }

    #[\Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }
}
