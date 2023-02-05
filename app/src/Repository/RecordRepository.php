<?php

namespace App\Repository;

use App\Entity\Record;
use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
    public function save(Record $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}