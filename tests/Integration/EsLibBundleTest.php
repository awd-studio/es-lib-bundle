<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\Tests\Integration;

use AwdEs\EsLibBundle\EsLibBundle;
use AwdEs\EsLibBundle\Tests\Shared\AppTestCase;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

use function PHPUnit\Framework\assertTrue;

/**
 * @coversDefaultClass \AwdEs\EsLibBundle\EsLibBundle
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

    private function createKernel(): KernelInterface
    {
        return new class ('test', true) extends Kernel {
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
