<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\DependencyInjection;

use AwdEs\Aggregate\Persistence\Transaction\NestedTransactionManagerDecorator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final readonly class Configuration implements ConfigurationInterface
{
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('awd_es');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('transaction_manager')
            ->defaultValue(NestedTransactionManagerDecorator::class)
            ->info('Class for the transaction manager')
            ->end()
            ->scalarNode('event_recorder')
            ->defaultNull()
            ->info('Optional custom event recorder class')
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
