<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class MemberRepository extends EntityRepository
{
    public function save(Member $member)
    {
        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();
    }

    /**
     * List of members that have a reservation for this event
     *
     * @param Event $event
     * @return Member[]
     */
    public function getMembersHaveReservationFor(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('m')
            ->select('m')
            ->innerJoin('m.reservations', 'r')
            ->innerJoin('r.event', 'e')
            ->where('e = :event')
            ->setParameter('event', $event)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Find user by unique table code
     *
     * @param $tableCode
     * @return Member | null
     */
    public function getMemberByTableCode($tableCode)
    {
        $qb = $this
            ->createQueryBuilder('m')
            ->select('m')
            ->where('m.tableCode = :code')
            ->setParameter('code', $tableCode)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Check if a table code is in use
     *
     * @param $hash
     * @return bool
     */
    public function tableCodeInUse($hash)
    {
        $membersFound = $this->findBy(array('tableCode' => $hash));

        return count($membersFound) > 0;
    }

    /**
     * Update rating for a member
     *
     * @param $id
     * @param $rating
     * @return mixed
     */
    public function updateMemberRating($id, $rating)
    {
        $query = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:Member m
              SET m.rating = :rating
              WHERE m.id = :id
            ");

        return $query->execute(array(
            'id' => $id,
            'rating' => $rating,
        ));
    }

    /**
     * Get members that match name
     *
     * @param string $query
     * @return array
     */
    public function getMembersWithNameLike($query)
    {
        $query = str_replace('%', '', $query);
        $query .= '%';

        $qb = $this
            ->createQueryBuilder('m')
            ->select('m.id, m.firstName, m.lastName')
            ->where('m.firstName LIKE :query')
            ->orWhere('m.lastName LIKE :query')
            ->setParameter('query', $query);

        return $qb->getQuery()->getArrayResult();
    }
}
