<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\ApplicantRecipeRating;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantRecipeRatingRepository extends EntityRepository
{
    /**
     * @param integer $recipeId
     * @return boolean
     */
    public function updateRating($recipeId)
    {
        $qb = $this
            ->createQueryBuilder('arr')
            ->select('AVG(arr.totalRating)')
            ->innerJoin('arr.applicantRecipe', 'ar')
            ->where('ar.id = :recipeId')
            ->setParameter('recipeId', $recipeId);

        $rating = $qb->getQuery()->getSingleScalarResult();

        $query = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:ApplicantRecipe ar
              SET ar.rating = :rating
              WHERE ar.id = :id
            ");

        $query->execute(array(
            'id'     => $recipeId,
            'rating' => $rating,
        ));

        $query2 = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:ApplicantRecipeRating arr
              SET arr.processed = TRUE
              WHERE arr.applicantRecipe = :id
            ");

        $query2->execute(array(
            'id' => $recipeId,
        ));

    }

    /**
     * Return given rates by member
     *
     * @param $member
     * @return array
     */
    public function getMemberRates(Member $member)
    {
        $qb = $this
            ->getEntityManager()
            ->createQuery("
                SELECT arr.visualRating, arr.tasteRating, ar.id
                FROM AppBundle:ApplicantRecipeRating arr
                INNER JOIN arr.applicantRecipe ar
                WHERE arr.voter = :member
            ")
            ->setParameter('member', $member);

        $orig = $qb->getArrayResult();

        return array_combine(array_column($orig, 'id'), $orig);
    }


    /**
     * Return given rates by member and applicantRecipeIds
     *
     * @param Member $member
     * @param array $applicantRecipeIds
     * @return array
     */
    public function getMemberRatesByApplicantRecipeIds(Member $member, $applicantRecipeIds)
    {
        $qb = $this
            ->getEntityManager()
            ->createQuery("
                SELECT arr.visualRating AS visual, arr.tasteRating AS taste, ar.id AS applicant_recipe_id
                FROM AppBundle:ApplicantRecipeRating arr
                LEFT JOIN arr.applicantRecipe ar
                WHERE arr.voter = :member
                AND arr.applicantRecipe IN(:applicantRecipeIds)
            ")
            ->setParameter('member', $member)
            ->setParameter('applicantRecipeIds', $applicantRecipeIds);

        return $qb->getArrayResult();
    }

    /**
     * @param Member $member
     * @param ApplicantRecipe $applicantRecipe
     * @param $rating
     *
     * @param ApplicantRecipeRating $applicantRecipeRating
     * @return ApplicantRecipeRating
     */
    public function save(
        Member $member,
        ApplicantRecipe $applicantRecipe,
        $rating,
        ApplicantRecipeRating $applicantRecipeRating = null
    ) {
        $em = $this->getEntityManager();

        if (!$applicantRecipeRating) {
            $applicantRecipeRating = new ApplicantRecipeRating();
            $applicantRecipeRating->setApplicantRecipe($applicantRecipe);
            $applicantRecipeRating->setVoter($member);
        }
        $applicantRecipeRating->setTasteRating($rating['taste']);
        $applicantRecipeRating->setVisualRating($rating['visual']);

        $applicantRecipeRating->setProcessed(false);
        $em->persist($applicantRecipeRating);
        $em->flush();

        return $applicantRecipeRating;
    }
}
