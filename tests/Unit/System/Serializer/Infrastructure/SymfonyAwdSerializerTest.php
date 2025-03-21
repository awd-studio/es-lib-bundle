<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\System\Serializer\Infrastructure;

use AwdEs\EsLibBundle\System\Serializer\Domain\AwdSerializer;
use AwdEs\EsLibBundle\System\Serializer\Domain\Exception\AwdDeserializationError;
use AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer
 *
 * @internal
 */
final class SymfonyAwdSerializerTest extends AppTestCase
{
    private SymfonyAwdSerializer $instance;
    private ObjectProphecy $normalizer;
    private ObjectProphecy $denormalizer;

    private object $testObject;
    private array $testArray;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize shared test data
        $this->testObject = new class {
            public function __construct(public string $key = 'value') {}
        };
        $this->testArray = ['key' => 'value'];

        // Create Prophecy doubles for normalizer and denormalizer
        $this->normalizer = $this->prophesize(NormalizerInterface::class);
        $this->denormalizer = $this->prophesize(DenormalizerInterface::class);

        // Inject mocks into the SymfonyAwdSerializer instance
        $this->instance = new SymfonyAwdSerializer(
            $this->denormalizer->reveal(),
            $this->normalizer->reveal(),
        );
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsAwdSerializerInterface(): void
    {
        // Assert that the instance implements the AwdSerializer interface
        assertInstanceOf(AwdSerializer::class, $this->instance);
    }

    /**
     * @covers ::serialize
     */
    public function testSerializesObjectToArray(): void
    {
        $expectedArray = ['key' => 'value']; // Expected output from normalizer

        // Define mock behavior for `normalize`
        $this->normalizer
            ->normalize($this->testObject, 'array')
            ->willReturn($expectedArray)
            ->shouldBeCalled()
        ;

        // Call the `serialize` method and assert the result
        $result = $this->instance->serialize($this->testObject);
        assertSame($expectedArray, $result);
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserializesArrayToObject(): void
    {
        $expectedObject = $this->testObject; // Expected output from denormalizer
        $type = $this->testObject::class; // Target type for deserialization

        // Define mock behavior for `denormalize`
        $this->denormalizer
            ->denormalize($this->testArray, $type)
            ->willReturn($expectedObject)
            ->shouldBeCalled()
        ;

        // Call the `deserialize` method and assert the result
        $result = $this->instance->deserialize($type, $this->testArray);
        assertSame($expectedObject, $result);
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserializeThrowsExceptionForInvalidInput(): void
    {
        $invalidInput = ['invalid']; // Input that cannot be denormalized
        $type = $this->testObject::class;

        // Define mock behavior to throw exception
        $this->denormalizer
            ->denormalize($invalidInput, $type)
            ->willThrow(new AwdDeserializationError('Cannot denormalize input'))
            ->shouldBeCalled()
        ;

        // Expect the exception during `deserialize` call
        $this->expectException(AwdDeserializationError::class);
        $this->expectExceptionMessage('Cannot denormalize input');

        $this->instance->deserialize($type, $invalidInput);
    }
}
