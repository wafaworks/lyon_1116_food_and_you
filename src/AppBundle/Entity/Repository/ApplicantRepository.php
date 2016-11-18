<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantRepository extends EntityRepository
{
    /**
     * Calculate how many times the Member participated and was accepted in an event
     *
     * @param Member $member
     * @return mixed
     */
    public function getNumberOfParticipations(Member $member)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->innerJoin('a.member', 'm')
            ->where('m = :member')
            ->andWhere('a.status = :status')
            ->setParameter('member', $member)
            ->setParameter('status', Applicant::STATUS_ACCEPTED);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Calculate average rating of all Member recipes
     *
     * @param Member $member
     * @deprecated not used as rating is cached on Member entity, use that instead
     * @return mixed
     */
    public function getRecipesAverageRating(Member $member)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('AVG(r.rating)')
            ->innerJoin('a.member', 'm')
            ->innerJoin('a.event', 'e')
            ->innerJoin('a.recipes', 'r')
            ->where('m = :member')
            ->andWhere('a.status = :status')
            ->andWhere('e.status = :eventStatus')
            ->setParameter('member', $member)
            ->setParameter('status', Applicant::STATUS_ACCEPTED)
            ->setParameter('eventStatus', Event::STATUS_FINISHED);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get average ratings for all members that ever cooked
     *
     * @return mixed
     */
    public function getAllAverageRates()
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('m.id as id, AVG(r.rating) as rating, COUNT(a.id) as participations')
            ->innerJoin('a.member', 'm')
            ->innerJoin('a.event', 'e')
            ->innerJoin('a.recipes', 'r')
            ->where('a.status = :status')
            ->andWhere('r.selected = true')
            ->andWhere('e.status = :eventStatus')
            ->setParameter('status', Applicant::STATUS_ACCEPTED)
            ->setParameter('eventStatus', Event::STATUS_FINISHED)
            ->groupBy('m.id');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get all applicants with all recipes
     *
     * @param Event $event
     * @return array
     */
    public function getAllApplicants(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a, ar, re, p')
            ->innerJoin('a.event', 'e')
            ->leftJoin('a.recipes', 'ar')
            ->leftJoin('ar.recipe', 're')
            ->leftJoin('re.photo', 'p')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $event->getId())
            ->addOrderBy('re.type', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get chosen applicant with selected recipes
     *
     * @param Event $event
     * @return array
     */
    public function getSelectedApplicants(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a, ar, re, p')
            ->innerJoin('a.event', 'e')
            ->leftJoin('a.recipes', 'ar')
            ->leftJoin('ar.recipe', 're')
            ->leftJoin('re.photo', 'p')
            ->where('e.id = :eventId')
            ->andWhere('a.status = :applicantStatus')
            ->andWhere('ar.selected = true')
            ->setParameter('eventId', $event->getId())
            ->setParameter('applicantStatus', Applicant::STATUS_ACCEPTED)
            ->addOrderBy('re.type', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Applicant $applicant
     */
    public function save(Applicant $applicant)
    {
        $this->getEntityManager()->persist($applicant);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Member $member
     * @param Event $event
     * @return bool
     */
    public function applicationExists(Member $member, Event $event)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->innerJoin('a.member', 'm')
            ->innerJoin('a.event', 'e')
            ->where('m = :member')
            ->andWhere('e = :event')
            ->setParameter('member', $member)
            ->setParameter('event', $event);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
