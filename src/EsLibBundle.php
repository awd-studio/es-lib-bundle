<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle;

use AwdEs\EsLibBundle\Doctrine\DBAL\Types\IDateTimeType;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\JsonbOrJsonType;
use AwdEs\EsLibBundle\Doctrine\DBAL\Types\UlidIdType;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class EsLibBundle extends AbstractBundle
{
    #[\Override] // @phpstan-ignore missingType.iterableValue
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
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
