<?php

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MemberEventMedia
{
    /**
     * @Assert\NotBlank()
     *
     * @var string $type
     */
    private $type;

    /**
     * @Assert\NotBlank()
     *
     * @var UploadedFile $media
     */
    private $media;

    private $applicantRecipe;

    /**
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param mixed $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     *
     * @return mixed
     */
    public function getApplicantRecipe()
    {
        return $this->applicantRecipe;
    }

    /**
     * @param mixed $applicantRecipe
     */
    public function setApplicantRecipe($applicantRecipe)
    {
        $this->applicantRecipe = $applicantRecipe;
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     * @internal param $index
     */
    public function isValidType(ExecutionContextInterface $context)
    {
        $type = $this->getType();
        if (!in_array($type, \AppBundle\Entity\MemberEventMedia::getTypes())) {
            $context->buildViolation('The value {{ value }} is invalid.')
                ->atPath('type')
                ->setParameter('{{ value }}', $type)
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     * @internal param $index
     */
    public function isApplicantRecipeBlank(ExecutionContextInterface $context)
    {
        $type = $this->getType();
        if ($type == \AppBundle\Entity\MemberEventMedia::TYPE_RECIPE && !$this->getApplicantRecipe()) {
            $context->buildViolation('Parameter applicant_recipe_id required ')
                ->atPath('applicantRecipe')
                ->addViolation();
        }
    }
}
