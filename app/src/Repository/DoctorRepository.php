<?php

namespace App\Repository;

use App\Entity\Doctor;
use Doctrine\ORM\EntityRepository;

class DoctorRepository extends EntityRepository
{
    public function save(Doctor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}