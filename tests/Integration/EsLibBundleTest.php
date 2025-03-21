<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Integration;

use AwdEs\EsLibBundle\DependencyInjection\CompilerPass\EntityRegistryCompilerPass;
use AwdEs\EsLibBundle\DependencyInjection\CompilerPass\EventRegistryCompilerPass;
use AwdEs\EsLibBundle\EsLibBundle;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use AwdEs\Registry\Entity\InMemoryEntityRegistry;
use AwdEs\Registry\Event\InMemoryEventRegistry;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\EsLibBundle
 *
 * @internal
 */
final class EsLibBundleTest extends AppTestCase
{
    public function testDoctrineTypesRegistered(): void
    {
        $kernel = $this->createKernel();

        $kernel->boot();
        $kernel->getContainer();

        assertTrue(Type::hasType('ulid_id'), 'Type "ulid_id" is not registered');
        assertTrue(Type::hasType('jsonb_or_json'), 'Type "jsonb_or_json" is not registered');
        assertTrue(Type::hasType('awd_datetime'), 'Type "awd_datetime" is not registered');
    }

    public function testProcessRegistersTaggedEntities(): void
    {
        $container = new ContainerBuilder();

        // Define the InMemoryEntityRegistry service
        $registryDefinition = new Definition(InMemoryEntityRegistry::class);
        $container->setDefinition(InMemoryEntityRegistry::class, $registryDefinition);

        // Add tagged services
        $service1 = new Definition();
        $service1->addTag('awd_es.entity');
        $container->setDefinition('entity_service_1', $service1);

        $service2 = new Definition();
        $service2->addTag('awd_es.entity');
        $container->setDefinition('entity_service_2', $service2);

        // Process the container with the compiler pass
        $compilerPass = new EntityRegistryCompilerPass();
        $compilerPass->process($container);

        // Verify the register method was called with the correct service IDs
        $methodCalls = $registryDefinition->getMethodCalls();

        assertCount(2, $methodCalls);
        assertEquals('register', $methodCalls[0][0]);
        assertEquals(['entity_service_1'], $methodCalls[0][1]);
        assertEquals(['entity_service_2'], $methodCalls[1][1]);
    }

    public function testProcessRegistersTaggedEvents(): void
    {
        $container = new ContainerBuilder();

        // Define the InMemoryEventRegistry service
        $registryDefinition = new Definition(InMemoryEventRegistry::class);
        $container->setDefinition(InMemoryEventRegistry::class, $registryDefinition);

        // Add tagged services for "awd_es.event"
        $service1 = new Definition();
        $service1->addTag('awd_es.event');
        $container->setDefinition('event_service_1', $service1);

        $service2 = new Definition();
        $service2->addTag('awd_es.event');
        $container->setDefinition('event_service_2', $service2);

        // Process the container with the compiler pass
        $compilerPass = new EventRegistryCompilerPass();
        $compilerPass->process($container);

        // Verify the register method was called with the correct service IDs
        $methodCalls = $registryDefinition->getMethodCalls();

        assertCount(2, $methodCalls);
        assertEquals('register', $methodCalls[0][0]);
        assertEquals(['event_service_1'], $methodCalls[0][1]);
        assertEquals('register', $methodCalls[1][0]);
        assertEquals(['event_service_2'], $methodCalls[1][1]);
    }

    private function createKernel(): KernelInterface
    {
        return new class('test', true) extends Kernel {
            public function registerBundles(): iterable
            {
                return [
                    new DoctrineBundle(),
                    new EsLibBundle(),
                ];
            }

            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                $loader->load(function(ContainerBuilder $container) {
                    $container->loadFromExtension('doctrine', [
                        'dbal' => [
                            'url' => 'sqlite:///:memory:',
                        ],
                    ]);
                });
            }
        };
    }
}
