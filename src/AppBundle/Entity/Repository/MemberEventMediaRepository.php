<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventMedia;
use AppBundle\Filter\FilterInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MemberEventMediaRepository extends EntityRepository
{
    const MEDIA_PER_PAGE = 6;

    /**
     * @param Event $event
     * @param FilterInterface $filter
     * @return \AppBundle\Entity\MemberEventMedia[]|array
     */
    public function getEventMediaFiltered(Event $event, FilterInterface $filter)
    {
        $qb = $this
            ->createQueryBuilder('mm')
            ->select('mm, m')
            ->innerJoin('mm.event', 'e')
            ->innerJoin('mm.media', 'm')
            ->where('e = :event')
            ->setParameter('event', $event);

        $this->filterPaginate($qb, $filter);

        return $qb->getQuery()->getResult();
    }

    public function getEventMediaByApplicationRecipeIds(Member $member, $applicantRecipeIds)
    {
        $qb = $this
            ->createQueryBuilder('mm')
            ->select('mm, m')
            ->innerJoin('mm.media', 'm')
            ->where('mm.member = :member')
            ->andWhere('mm.applicantRecipe IN(:applicantRecipeIds)')
            ->setParameters(array(
                'member' => $member,
                'applicantRecipeIds' => $applicantRecipeIds,
            ));

        return $qb->getQuery()->getResult();
    }

    public function getRestaurantEventMedia(Member $member, Event $event)
    {
        $qb = $this
            ->createQueryBuilder('mm')
            ->select('mm, m')
            ->innerJoin('mm.media', 'm')
            ->where('mm.member = :member')
            ->andWhere('mm.event = :event')
            ->andWhere('mm.type IN(:type)')
            ->setParameters(array(
                'member' => $member,
                'event' => $event,
                'type' => array(
                    MemberEventMedia::TYPE_EVENT,
                    MemberEventMedia::TYPE_RESTAURANT,
                )
            ))
            ->setMaxResults(2);

        return $qb->getQuery()->getResult();
    }

    public function save(MemberEventMedia $memberEventMedia)
    {
        $this->getEntityManager()->persist($memberEventMedia);
        $this->getEntityManager()->flush();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     */
    private function filterPaginate(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        $page = $filters->getFilter('page');

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * self::MEDIA_PER_PAGE;

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults(self::MEDIA_PER_PAGE);
    }
}
