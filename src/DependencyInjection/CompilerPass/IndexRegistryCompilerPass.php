<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\DependencyInjection\CompilerPass;

use AwdEs\Indexes\Meta\Registry\InMemoryIndexRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class IndexRegistryCompilerPass implements CompilerPassInterface
{
    public const string TAG = 'awd_es.index';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(InMemoryIndexRegistry::class);
        foreach ($container->findTaggedServiceIds(self::TAG) as $id => $tags) {
            $definition->addMethodCall('register', [$id]);
        }
    }
}
