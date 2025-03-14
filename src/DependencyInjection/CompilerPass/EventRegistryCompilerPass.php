<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\DependencyInjection\CompilerPass;

use AwdEs\Registry\Event\InMemoryEventRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class EventRegistryCompilerPass implements CompilerPassInterface
{
    public const string TAG = 'awd_es.event';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(InMemoryEventRegistry::class);
        foreach ($container->findTaggedServiceIds(self::TAG) as $id => $tags) {
            $definition->addMethodCall('register', [$id]);
        }
    }
}
