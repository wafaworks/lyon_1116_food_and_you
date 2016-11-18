<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberEventMedia
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MemberEventMediaRepository")
 * @ORM\Table(name="member_event_media")
 */
class MemberEventMedia
{
    const TYPE_RECIPE = 'recipe';
    const TYPE_EVENT = 'event';
    const TYPE_RESTAURANT = 'restaurant';

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     *
     * @var string $type
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Media", orphanRemoval=true, cascade={"persist"})
     *
     * @var Media
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApplicantRecipe", inversedBy="memberEventMedias")
     * @ORM\JoinColumn(name="applicant_recipe_id", referencedColumnName="id")
     *
     * @var ApplicantRecipe|null
     */
    private $applicantRecipe;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @var Member $member
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event", inversedBy="memberEventMedias")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *
     * @var Event $event
     */
    private $event;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
        $this->type = $type;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return ApplicantRecipe|null
     */
    public function getApplicantRecipe()
    {
        return $this->applicantRecipe;
    }

    /**
     * @param ApplicantRecipe|null $applicantRecipe
     */
    public function setApplicantRecipe($applicantRecipe)
    {
        $this->applicantRecipe = $applicantRecipe;
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
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * Return types of media
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_EVENT,
            self::TYPE_RESTAURANT,
            self::TYPE_RECIPE,
        ];
    }
}
