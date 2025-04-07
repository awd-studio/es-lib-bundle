<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle;

use AwdEs\Aggregate\Entity;
use AwdEs\EsLibBundle\AwdEs\Indexes\Record\Factory\IndexRecordFactoryCase;
use AwdEs\EsLibBundle\DependencyInjection\CompilerPass\EntityRegistryCompilerPass;
use AwdEs\EsLibBundle\DependencyInjection\CompilerPass\EventRegistryCompilerPass;
use AwdEs\EsLibBundle\DependencyInjection\CompilerPass\IndexRegistryCompilerPass;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\IDateTimeType;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\JsonbOrJsonType;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\UlidIdType;
use AwdEs\Event\EntityEvent;
use AwdEs\Event\Storage\Fetcher\Handling\CriteriaHandlingCase;
use AwdEs\Indexes\Index;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class EsLibBundle extends AbstractBundle
{
    #[\Override] // @phpstan-ignore missingType.iterableValue
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import($this->getPath() . '/src/Resources/config/services.yaml');
    }

    #[\Override]
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import($this->getPath() . '/src/Resources/config/doctrine.yaml');
        $config = [
            'dbal' => [
                'types' => [
                    UlidIdType::NAME => UlidIdType::class,
                    JsonbOrJsonType::NAME => JsonbOrJsonType::class,
                    IDateTimeType::NAME => IDateTimeType::class,
                ],
            ],
        ];

        $builder->prependExtensionConfig('doctrine', $config);

        $builder
            ->registerForAutoconfiguration(Entity::class)
            ->addTag(EntityRegistryCompilerPass::TAG)
        ;

        $builder
            ->registerForAutoconfiguration(EntityEvent::class)
            ->addTag(EventRegistryCompilerPass::TAG)
        ;

        $builder
            ->registerForAutoconfiguration(Index::class)
            ->addTag(IndexRegistryCompilerPass::TAG)
        ;

        $builder
            ->registerForAutoconfiguration(CriteriaHandlingCase::class)
            ->addTag('awd_es.event.storage.fetcher.handling.criteria_case')
        ;

        $builder
            ->registerForAutoconfiguration(IndexRecordFactoryCase::class)
            ->addTag('awd_es.index_record.factory_case')
        ;
    }

    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Add compiler passes
        $container->addCompilerPass(new EntityRegistryCompilerPass());
        $container->addCompilerPass(new EventRegistryCompilerPass());
        $container->addCompilerPass(new IndexRegistryCompilerPass());
    }

    #[\Override]
    public function boot(): void
    {
        parent::boot();

        // Ensure types are registered
        if (!Type::hasType(UlidIdType::NAME)) {
            Type::addType(UlidIdType::NAME, UlidIdType::class);
        }

        if (!Type::hasType(JsonbOrJsonType::NAME)) {
            Type::addType(JsonbOrJsonType::NAME, JsonbOrJsonType::class);
        }

        if (!Type::hasType(IDateTimeType::NAME)) {
            Type::addType(IDateTimeType::NAME, IDateTimeType::class);
        }
    }
}
