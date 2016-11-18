<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RecipeRepository")
 * @ORM\Table(name="recipe")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Recipe
{
    const TYPE_ENTRY = '0entry';
    const TYPE_MAIN = '1main';
    const TYPE_DESSERT = '2dessert';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="recipes")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     *
     * @var Member
     */
    private $member;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $publicDescription;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $privateDescription;

    /**
     * @ORM\OneToOne(targetEntity="Media", orphanRemoval=true)
     *
     * @var Media
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="ApplicantRecipe", mappedBy="recipe")
     *
     * @var ArrayCollection | ApplicantRecipe[]
     */
    private $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     */
    public function setMember(Member $member)
    {
        $this->member = $member;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array(
            $type,
            array(
                self::TYPE_ENTRY,
                self::TYPE_MAIN,
                self::TYPE_DESSERT,
            )
        )) {
            throw new \InvalidArgumentException("Invalid type");
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPublicDescription()
    {
        return $this->publicDescription;
    }

    /**
     * @param string $publicDescription
     */
    public function setPublicDescription($publicDescription)
    {
        $this->publicDescription = $publicDescription;
    }

    /**
     * @return string
     */
    public function getPrivateDescription()
    {
        return $this->privateDescription;
    }

    /**
     * @param string $privateDescription
     */
    public function setPrivateDescription($privateDescription)
    {
        $this->privateDescription = $privateDescription;
    }

    /**
     * @return Media
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param Media $photo
     */
    public function setPhoto(Media $photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return ApplicantRecipe[]|ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param ApplicantRecipe[]|ArrayCollection $applications
     */
    public function setApplications($applications)
    {
        foreach ($applications as $application) {
            $application->setRecipe($this);
        }

        $this->applications = $applications;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        $types =  array(
            self::TYPE_ENTRY,
            self::TYPE_MAIN,
            self::TYPE_DESSERT
        );

        return array_combine($types, $types);
    }
}
