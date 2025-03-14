<?php

declare(strict_types=1);

namespace AwdEs\EsLibBundle\AwdEs\Aggregate\Persistence\Transaction;

use AwdEs\Aggregate\Persistence\Transaction\TransactionManager;
use Doctrine\ORM\EntityManagerInterface;

final readonly class EntityManagerTransactionManager implements TransactionManager
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    #[\Override]
    public function begin(): void
    {
        $this->em->beginTransaction();
    }

    #[\Override]
    public function commit(): void
    {
        $this->em->commit();
    }

    #[\Override]
    public function rollback(): void
    {
        $this->em->rollback();
    }
}
