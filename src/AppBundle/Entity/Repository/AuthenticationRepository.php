<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Authentication;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class AuthenticationRepository extends EntityRepository
{
    /**
     * @param $user
     */
    public function save(Authentication $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
