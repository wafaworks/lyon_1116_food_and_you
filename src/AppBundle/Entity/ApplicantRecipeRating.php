<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicantRecipeRatingRepository")
 * @ORM\Table(name="applicant_recipe_rating")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantRecipeRating
{
    const DEFAULT_RATING = -1;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ApplicantRecipe")
     * @ORM\JoinColumn(name="applicant_recipe_id", referencedColumnName="id",  onDelete="CASCADE")
     *
     * @var ApplicantRecipe
     */
    private $applicantRecipe;

    /**
     * @ORM\Column(name="visual_rating", type="integer")
     *
     * @var integer
     */
    private $visualRating;

    /**
     * @ORM\Column(name="taste_rating", type="integer")
     *
     * @var  integer
     */
    private $tasteRating;

    /**
     * @ORM\Column(name="total_rating", type="float")
     *
     * @var float
     */
    private $totalRating;

    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
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
        $this->visualRating = self::DEFAULT_RATING;
        $this->tasteRating = self::DEFAULT_RATING;
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ApplicantRecipe
     */
    public function getApplicantRecipe()
    {
        return $this->applicantRecipe;
    }

    /**
     * @param ApplicantRecipe $applicantRecipe
     */
    public function setApplicantRecipe($applicantRecipe)
    {
        $this->applicantRecipe = $applicantRecipe;
    }

    /**
     * @return int
     */
    public function getVisualRating()
    {
        return $this->visualRating;
    }

    /**
     * @param int $visualRating
     */
    public function setVisualRating($visualRating)
    {
        $this->visualRating = $visualRating;

        $this->totalRating = ((int)$this->getTasteRating() + $visualRating) / 2;
    }

    /**
     * @return int
     */
    public function getTasteRating()
    {
        return $this->tasteRating;
    }

    /**
     * @param int $tasteRating
     */
    public function setTasteRating($tasteRating)
    {
        $this->tasteRating = $tasteRating;

        $this->totalRating = ((int)$this->getVisualRating() + $tasteRating) / 2;
    }

    /**
     * @return float
     */
    public function getTotalRating()
    {
        return $this->totalRating;
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
