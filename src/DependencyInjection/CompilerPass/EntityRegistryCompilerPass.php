<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\DependencyInjection\CompilerPass;

use AwdEs\Registry\Entity\InMemoryEntityRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class EntityRegistryCompilerPass implements CompilerPassInterface
{
    public const string TAG = 'awd_es.entity';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(InMemoryEntityRegistry::class);

        foreach ($container->findTaggedServiceIds(self::TAG) as $id => $tags) {
            $definition->addMethodCall('register', [$id]);
        }
    }
}
