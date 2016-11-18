<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicantVoteRepository")
 * @ORM\Table(name="applicant_vote")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantVote
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Applicant")
     * @ORM\JoinColumn(name="applicant_id", referencedColumnName="id",  onDelete="CASCADE")
     *
     * @var Applicant
     */
    private $applicant;

    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="voter_id", referencedColumnName="id",  onDelete="CASCADE")
     *
     * @var Member
     */
    private $voter;

    /**
     * @ORM\Column(name="processed", type="boolean", options={"default"=false})
     *
     * @var boolean
     */
    private $processed;

    public function __construct()
    {
        $this->processed = 0;
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Applicant
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * @param Applicant $applicant
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * @return Member
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param Member $voter
     */
    public function setVoter($voter)
    {
        $this->voter = $voter;
    }

    /**
     * @return boolean
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @param boolean $processed
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }
}
