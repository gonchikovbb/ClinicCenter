<?php

namespace App\Repository;

use App\Entity\Weather;
use Doctrine\ORM\EntityRepository;

class WeatherRepository extends EntityRepository
{
    public function save(Weather $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}