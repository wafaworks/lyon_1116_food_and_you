<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicantRecipeRepository")
 * @ORM\Table(name="applicant_recipe")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicantRecipe
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Applicant", inversedBy="recipes")
     * @ORM\JoinColumn(name="applicant_id", referencedColumnName="id")
     *
     * @var Applicant
     */
    private $applicant;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="applications")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     *
     * @var Recipe
     */
    private $recipe;

    /**
     * @ORM\Column(name="rating", type="float", options={"default"=0})
     *
     * @var float
     */
    private $rating;

    /**
     * @ORM\Column(name="selected", type="boolean", options={"default"=false})
     *
     * @var boolean
     */
    private $selected;

    /**
     * @ORM\Column(name="winner", type="boolean", options={"default"=false})
     *
     * @var boolean
     */
    private $winner;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MemberEventMedia", mappedBy="applicantRecipe", cascade={"remove"})
     *
     * @var ArrayCollection $memberEventMedias
     */
    private $memberEventMedias;

    public function __construct()
    {
        $this->setRating(0);
        $this->setSelected(false);
        $this->setWinner(false);
        $this->memberEventMedias = new ArrayCollection();
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
    public function setApplicant(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * @return Recipe
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * @param Recipe $recipe
     */
    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return boolean
     */
    public function isWinner()
    {
        return $this->winner;
    }

    /**
     * @param boolean $winner
     */
    public function setWinner($winner)
    {
        $this->winner = $winner;
    }

    /**
     * @return ArrayCollection
     */
    public function getMemberEventMedias()
    {
        return $this->memberEventMedias;
    }

    /**
     * @param ArrayCollection $memberEventMedias
     */
    public function setMemberEventMedias($memberEventMedias)
    {
        $this->memberEventMedias = $memberEventMedias;
    }
}
