<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Unit\System\Serializer\Infrastructure;

use AwdEs\EsLibBundle\System\Serializer\Domain\AwdSerializer;
use AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\System\Serializer\Infrastructure\SymfonyAwdSerializer
 */
final class SymfonyAwdSerializerTest extends AppTestCase
{
    private SymfonyAwdSerializer $instance;
    private NormalizerInterface|ObjectProphecy $normalizer;
    private DenormalizerInterface|ObjectProphecy $denormalizer;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->normalizer = $this->prophesize(NormalizerInterface::class);
        $this->denormalizer = $this->prophesize(DenormalizerInterface::class);

        $this->instance = new SymfonyAwdSerializer(
            $this->denormalizer->reveal(),
            $this->normalizer->reveal(),
        );
    }

    /**
     * @test
     */
    public function itImplementsAwdSerializerInterface(): void
    {
        assertInstanceOf(AwdSerializer::class, $this->instance);
    }

    /**
     * @test
     */
    public function itSerializesObjectToArray(): void
    {
        $testObject = new \stdClass();
        $expectedArray = ['key' => 'value'];

        $this->normalizer->normalize($testObject, 'array')->willReturn(
            $expectedArray,
        );

        $result = $this->instance->serialize($testObject);

        assertSame($expectedArray, $result);
    }

    /**
     * @test
     */
    public function itDeserializesArrayToObject(): void
    {
        $testArray = ['key' => 'value'];
        $expectedObject = new \stdClass();
        $type = \stdClass::class;

        $this->denormalizer->denormalize($testArray, $type)->willReturn(
            $expectedObject,
        );

        $result = $this->instance->deserialize($type, $testArray);

        assertSame($expectedObject, $result);
    }
}
