<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Restaurant;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class CityRepository extends EntityRepository
{
    public function getRestaurants()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c,r')
            ->leftJoin('c.restaurants', 'r')
            ->where('r.status = :status')
            ->setParameters(array(
                'status' => Restaurant::STATUS_VALIDATED
            ))
            ->orderBy('r.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
