<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantCookWith;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantCookWithRepository extends EntityRepository
{
    public function addCookWithToApplication(Applicant $applicant, Member $cook)
    {
        $cookWith = new ApplicantCookWith();
        $cookWith->setMember($cook);
        $cookWith->setApplicant($applicant);

        $this->getEntityManager()->persist($cookWith);
        $this->getEntityManager()->flush();
    }
}
