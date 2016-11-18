<?php

namespace AppBundle\Model;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Application
{
    private $event;

    private $member;

    /**
     * @Assert\File(
     *     maxSize = "3072k",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "app.application.image.format"
     * )
     */
    private $uploadedFile;

    /**
     * @Assert\NotBlank(
     *      message = "app.application.biography.required"
     * )
     * @Assert\Length(
     *      max = 5000,
     *      maxMessage = "app.application.biography.maxlength"
     * )
     */
    private $biography;

    /**
     * @Assert\NotBlank(
     *      message = "app.application.profession.required"
     * )
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "app.application.profession.maxlength"
     * )
     */
    private $profession;

    /**
     * @Assert\NotBlank(
     *      message = "app.application.signature.required"
     * )
     * @Assert\Length(
     *      max = 2000,
     *      maxMessage = "app.application.signature.maxlength"
     * )
     */
    private $signature;

    /**
     * @Assert\NotBlank(
     *      message = "app.application.phone.required"
     * )
     */
    private $phone;

    private $cookWith;
    /**
     * @Assert\Count(
     *     min="1"
     * )
     */
    private $recipes;

    private $dishes;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->cookWith = new ArrayCollection(array(new Member(), new Member()));
        $this->recipes = new ArrayCollection();
        // $this->recipes = new ArrayCollection(array(new Recipe(), new Recipe()));
    }

    /**
     * @param Member $member
     * @param Event $event
     * @return Application
     */
    public static function create(Member $member, Event $event)
    {
        $application = new Application();
        $application->setEvent($event);
        $application->setMember($member);
        $application->setBiography($member->getBiography());
        $application->setSignature($member->getSignature());
        $application->setProfession($member->getProfession());
        $application->setPhone($member->getPhone());

        return $application;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @param UploadedFile $file
     */
    public function setUploadedImage(UploadedFile $file)
    {
        $this->uploadedFile = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedImage()
    {
        return $this->uploadedFile;
    }

    /**
     * @return mixed
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param mixed $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return ArrayCollection
     */
    public function getCookWith()
    {
        return $this->cookWith;
    }

    /**
     * @param ArrayCollection $cookWith
     */
    public function setCookWith($cookWith)
    {
        $this->cookWith = $cookWith;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param ArrayCollection $recipes
     */
    public function setRecipes($recipes)
    {
        $this->recipes  = $recipes;
    }
}
