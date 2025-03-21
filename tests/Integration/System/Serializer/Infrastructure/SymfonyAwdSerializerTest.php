<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Integration\System\Serializer\Infrastructure;

use Awd\ValueObject\DateTime;
use Awd\ValueObject\IDateTime;
use AwdEs\EsLibBundle\AwdEs\ValueObject\UlidId;
use AwdEs\EsLibBundle\Serializer\Denormalizer\IDateTimeDenormalizer;
use AwdEs\EsLibBundle\Serializer\Denormalizer\UlidIdDenormalizer;
use AwdEs\EsLibBundle\Serializer\Denormalizer\VersionDenormalizer;
use AwdEs\EsLibBundle\Serializer\Normalizer\IDateTimeNormalizer;
use AwdEs\EsLibBundle\Serializer\Normalizer\UlidIdNormalizer;
use AwdEs\EsLibBundle\Serializer\Normalizer\VersionNormalizer;
use AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\ValueObject\Version;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class SampleValueObject
{
    public function __construct(
        public UlidId $id,
        public Version $version,
        public IDateTime $dateTime,
    ) {}
}

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer
 *
 * @internal
 */
final class SymfonyAwdSerializerTest extends AppTestCase
{
    private SymfonyAwdSerializer $instance;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $objectNormalizer = new ObjectNormalizer();

        $customNormalizers = [
            new IDateTimeDenormalizer(),
            new VersionDenormalizer(),
            new UlidIdDenormalizer(),
            new IDateTimeNormalizer(),
            new VersionNormalizer(),
            new UlidIdNormalizer(),
        ];

        $symfonySerializer = new Serializer([...$customNormalizers, $objectNormalizer]);

        $this->instance = new SymfonyAwdSerializer(
            $symfonySerializer, // DenormalizerInterface
            $symfonySerializer,  // NormalizerInterface
        );
    }

    public function testSerializationAndDeserialization(): void
    {
        // Create a simple object with IDateTime and Version value objects
        $id = UlidId::fromString('0195b3f6-1429-5f28-abac-bb7ee6c126fa');
        $dateTime = DateTime::fromString('2023-10-15 14:30:00');
        $version = new Version(1);
        $sampleObject = new SampleValueObject($id, $version, $dateTime);

        // Serialize the object
        $serializedData = $this->instance->serialize($sampleObject);

        // Assert the returned data is in proper array format
        assertIsArray($serializedData);
        assertArrayHasKey('id', $serializedData);
        assertArrayHasKey('dateTime', $serializedData);
        assertArrayHasKey('version', $serializedData);

        // Assert serialized data matches expected output
        assertSame('0195b3f6-1429-5f28-abac-bb7ee6c126fa', $serializedData['id']);
        assertSame('2023-10-15 14:30:00.000000', $serializedData['dateTime']);
        assertSame(1, $serializedData['version']);

        // Deserialize the data back into an object
        /** @var SampleValueObject $deserializedObject */
        $deserializedObject = $this->instance->deserialize(SampleValueObject::class, $serializedData);

        // Assert that deserialized object is similar to the original
        assertInstanceOf(SampleValueObject::class, $deserializedObject);
        assertInstanceOf(UlidId::class, $deserializedObject?->id);
        assertInstanceOf(Version::class, $deserializedObject?->version);
        assertInstanceOf(DateTime::class, $deserializedObject?->dateTime);

        assertTrue($sampleObject->id->isSame($deserializedObject?->id));
        assertTrue($sampleObject->version->value === $deserializedObject?->version->value);
        assertTrue($sampleObject->dateTime->isEqual($deserializedObject?->dateTime));
    }
}
