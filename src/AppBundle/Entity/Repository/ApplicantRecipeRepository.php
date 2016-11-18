<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Recipe;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantRecipeRepository extends EntityRepository
{
    /**
     * Get the list of recipes chosen for the event
     *
     * @param Event $event
     * @return ApplicantRecipe[]
     */
    public function getRecipesChosenForEvent(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('ar')
            ->select('ar')
            ->innerJoin('ar.applicant', 'a')
            ->innerJoin('a.event', 'e')
            ->where('e = :event')
            ->andWhere('a.status = :status')
            ->andWhere('ar.selected = TRUE')
            ->setParameter('event', $event)
            ->setParameter('status', Applicant::STATUS_ACCEPTED);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get recipes that need their rating updated
     *
     * @param Event $event
     * @return array
     */
    public function getUnaccountedRecipeIds(Event $event = null)
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('ar.id')
            ->from('AppBundle:ApplicantRecipeRating', 'arr')
            ->innerJoin('arr.applicantRecipe', 'ar')
            ->where('arr.processed = false')
            ->groupBy('ar');

        if ($event) {
            $qb
                ->innerJoin('ar.applicant', 'a')
                ->innerJoin('a.event', 'e')
                ->andWhere('e.id = :event')
                ->andWhere('ar.selected = :selected')
                ->setParameters(array(
                    'event'    => $event,
                    'selected' => true,
                ));
        }

        return array_map(
            function ($element) {
                return $element['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }

    /**
     * @param Event $event
     */
    public function chooseWinner(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('ar')
            ->select('ar')
            ->innerJoin('ar.applicant', 'a')
            ->innerJoin('a.event', 'e')
            ->where('e.id = :event')
            ->andWhere('ar.selected = :selected')
            ->andWhere('a.status = :userSelected')
            ->setParameters(array(
                'event'        => $event,
                'selected'     => true,
                'userSelected' => Applicant::STATUS_ACCEPTED,
            ))
            ->orderBy('ar.rating', 'DESC');

        /** @var ApplicantRecipe $applicantRecipe */
        $applicantsRecipes = $qb->getQuery()->getResult();

        if ($applicantsRecipes) {
            $em = $this->getEntityManager();

            $max = $applicantsRecipes[0]->getRating();
            foreach ($applicantsRecipes as $applicantRecipe) {
                if ($max == $applicantRecipe->getRating()) {
                    $applicantRecipe->setWinner(true);
                    $em->persist($applicantRecipe);
                }
            }
            $em->flush();
        }
    }

    public function addRecipeToApplicant(Applicant $applicant, Recipe $recipe)
    {
        $applicantRecipe    = new ApplicantRecipe();
        $applicantRecipe->setApplicant($applicant);
        $applicantRecipe->setRecipe($recipe);
        $this->getEntityManager()->persist($applicantRecipe);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Event $event
     * @param string $direction
     *
     * @return array
     */
    public function getBoApplicants(Event $event, $direction = 'DESC')
    {
        $qb = $this
            ->createQueryBuilder('ar')
            ->select('ar, a, re, c, m, au')
            ->innerJoin('ar.applicant', 'a')
            ->innerJoin('a.event', 'e')
            ->innerJoin('ar.recipe', 're')
            ->innerJoin('a.member', 'm')
            ->innerJoin('m.authentication', 'au')
            ->leftJoin('a.cookWith', 'c')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $event->getId())
            ->addOrderBy('ar.winner', 'DESC')
            ->addOrderBy('ar.selected', 'DESC')
            ->addOrderBy('a.nrVotes', $direction);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Event $event
     * @param array $ids
     *
     * @return array|ApplicantRecipe[]
     */
    public function getApplicantRecipesByIds(Event $event, array $ids)
    {
        $qb = $this->createQueryBuilder('ar')
            ->select('ar,a')
            ->innerJoin('ar.applicant', 'a')
            ->innerJoin('a.event', 'e')
            ->where('ar.id IN (:ids)')
            ->andWhere('e = :event')
            ->setParameters(
                array(
                    'ids' => $ids,
                    'event' => $event,
                )
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Event $event
     * @return array
     */
    public function getSelectedRecipes(Event $event)
    {
        $qb = $this->createQueryBuilder('ar')
            ->select('ar.id')
            ->innerJoin('ar.applicant', 'a')
            ->innerJoin('a.event', 'e')
            ->where('e = :event')
            ->andWhere('ar.selected = TRUE')
            ->setParameters(
                array(
                    'event' => $event,
                )
            );

        return array_map(
            function ($item) {
                return $item['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }
}
