<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class JsonbOrJsonType extends Type
{
    public const string NAME = 'jsonb_or_json';

    /**
     * Convert the database value to a PHP value (array).
     */
    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        // Ensure $value is a string or null before attempting to decode
        if (null === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new ConversionException(\sprintf('Expected value of type string, but got %s.', get_debug_type($value)));
        }

        // Decode JSON string to array
        try {
            $decodedValue = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ConversionException(\sprintf('Invalid JSON data: %s', $value), $e->getCode(), $e);
        }

        return $decodedValue;
    }

    /**
     * Convert the PHP value to a database value (JSON string).
     */
    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        // Ensure the value is an array or null
        if (null === $value) {
            return null;
        }

        if (!\is_array($value)) {
            throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". Expected one of: null, json', get_debug_type($value), self::NAME));
        }

        // Encode array to JSON string
        try {
            $encodedValue = json_encode($value, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ConversionException(\sprintf('Could not convert PHP value of type "%s" to type "%s". The JSON is invalid: %s', get_debug_type($value), self::NAME, $e->getMessage()), $e->getCode(), $e);
        }

        return $encodedValue;
    }

    /**
     * Get the SQL declaration for the column type.
     */
    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Ensure platform is PostgreSQL
        if (($platform instanceof PostgreSQLPlatform) && $platform->hasDoctrineTypeMappingFor('jsonb')) {
            return 'jsonb'; // Use jsonb if supported by PostgreSQL
        }

        return 'json'; // Fallback to json
    }

    /**
     * Get the name of the custom type.
     */
    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[\Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }
}
