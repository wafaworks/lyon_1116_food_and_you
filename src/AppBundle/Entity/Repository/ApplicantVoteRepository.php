<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantVote;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantVoteRepository extends EntityRepository
{
    /**
     * @param integer $applicantId
     * @return boolean
     */
    public function updateVoteCount($applicantId)
    {
        // get nr of votes
        $qb = $this
            ->createQueryBuilder('av')
            ->select('COUNT(av)')
            ->innerJoin('av.applicant', 'a')
            ->where('a.id = :applicantId')
            ->setParameter('applicantId', $applicantId);
        $votes = $qb->getQuery()->getSingleScalarResult();

        // update vote count
        $query = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:Applicant a
              SET a.nrVotes = :votes
              WHERE a.id = :id
            ");
        $query->execute(array(
            'id'    => $applicantId,
            'votes' => $votes,
        ));

        // mark votes as counted
        $query2 = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:ApplicantVote av
              SET av.processed = TRUE
              WHERE av.applicant = :id
            ");
        $query2->execute(array(
            'id' => $applicantId,
        ));

    }

    /**
     * Get IDs of applicants for which we haven't updated the nrVotes column
     *
     * @return array
     */
    public function getUnaccountedVoteApplicantIds()
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('a.id')
            ->from('AppBundle:ApplicantVote', 'av')
            ->innerJoin('av.applicant', 'a')
            ->where('av.processed = false')
            ->groupBy('a');

        return array_map(
            function ($element) {
                return $element['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }

    public function checkMemberVoted($ids, Member $member)
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('a.id')
            ->from('AppBundle:ApplicantVote', 'av')
            ->innerJoin('av.applicant', 'a')
            ->where('a.id IN (:ids)')
            ->andWhere('av.voter = :member')
            ->setParameter('member', $member)
            ->setParameter('ids', $ids);

        return array_map(
            function ($element) {
                return $element['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }

    public function checkVotes(Member $profile, Member $voter)
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('a.id')
            ->from('AppBundle:ApplicantVote', 'av')
            ->innerJoin('av.applicant', 'a')
            ->where('a.member = :profile')
            ->andWhere('av.voter = :voter')
            ->setParameter('profile', $profile)
            ->setParameter('voter', $voter);

        $result = $qb->getQuery()->getArrayResult();
        return array_map(
            function ($element) {
                return $element['id'];
            },
            $result
        );
    }

    /**
     * Insert a new vote and increment the counter
     *
     * @param Applicant $applicant
     * @param Member $member
     */
    public function registerVote(Applicant $applicant, Member $member)
    {
        if (count($this->checkMemberVoted(array($applicant->getId()), $member)) === 0) {
            $applicant->setNrVotes($applicant->getNrVotes() + 1);

            $vote = new ApplicantVote();
            $vote->setApplicant($applicant);
            $vote->setVoter($member);

            $this->getEntityManager()->persist($applicant);
            $this->getEntityManager()->persist($vote);
            $this->getEntityManager()->flush();
        }
    }
}
