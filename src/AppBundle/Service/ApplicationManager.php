<?php

namespace AppBundle\Service;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\ApplicantCookWithRepository;
use AppBundle\Entity\Repository\ApplicantRecipeRepository;
use AppBundle\Entity\Repository\ApplicantRepository;
use AppBundle\Entity\Repository\MemberRepository;
use AppBundle\Model\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ApplicationManager
{
    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var ApplicantCookWithRepository
     */
    private $applicantCookWithRepository;
    /**
     * @var ApplicantRepository
     */
    private $applicantRepository;

    /**
     * @var ApplicantRecipeRepository
     */
    private $applicantRecipeRepository;

    /**
     * @param MemberRepository $memberRepository
     * @param ApplicantRepository $applicantRepository
     * @param ApplicantCookWithRepository $applicantCookWithRepository
     * @param ApplicantRecipeRepository $applicantRecipeRepository
     */
    public function __construct(
        MemberRepository $memberRepository,
        ApplicantRepository $applicantRepository,
        ApplicantCookWithRepository $applicantCookWithRepository,
        ApplicantRecipeRepository $applicantRecipeRepository
    ) {
        $this->memberRepository = $memberRepository;
        $this->applicantCookWithRepository = $applicantCookWithRepository;
        $this->applicantRepository = $applicantRepository;
        $this->applicantRecipeRepository = $applicantRecipeRepository;
    }

    public function process(Application $application)
    {
        if (!$this->applicationAlreadyExists($application->getEvent(), $application->getMember())) {
            $applicant = $this->createApplicant($application);
            $this->updateMemberInfo($application);
            $this->processCookWith($application, $applicant);
            $this->processDishes($application, $applicant);
        }
    }

    /**
     * @param Application $application
     */
    private function updateMemberInfo(Application $application)
    {
        $member = $application->getMember();
        $member->setBiography($application->getBiography());
        $member->setSignature($application->getSignature());
        $member->setPhone($application->getPhone());
        $member->setProfession($application->getProfession());

        if ($application->getUploadedImage() instanceof UploadedFile) {
            $member->setUploadedImage($application->getUploadedImage());
        }

        $this->memberRepository->save($member);
    }

    /**
     * Creates and persists a new applicant
     *
     * @param Application $application
     * @return Applicant
     */
    private function createApplicant(Application $application)
    {
        $applicant = new Applicant();
        $applicant->setEvent($application->getEvent());
        $applicant->setMember($application->getMember());
        $applicant->setNrVotes(0);
        $applicant->setAppliedAt(new \DateTime());
        $applicant->setStatus(Applicant::STATUS_PENDING);

        $this->applicantRepository->save($applicant);

        return $applicant;
    }

    /**
     * Persist cook with member info
     *
     * @param $application
     * @param $applicant
     */
    private function processCookWith(Application $application, Applicant $applicant)
    {
        $cooks = $application->getCookWith();

        foreach ($cooks as $cook) {
            if ($cook instanceof Member) {
                $this->applicantCookWithRepository->addCookWithToApplication($applicant, $cook);
            }
        }
    }

    /**
     * Persist applicant recipes
     *
     * @param $application
     * @param $applicant
     */
    private function processDishes(Application $application, Applicant $applicant)
    {
        foreach ($application->getRecipes() as $recipe) {
            $this->applicantRecipeRepository->addRecipeToApplicant($applicant, $recipe);
        }
    }

    /**
     * Check if user already applied to event
     *
     * @param Event $event
     * @param Member $member
     * @return bool
     */
    public function applicationAlreadyExists(Event $event, Member $member)
    {
        return $this->applicantRepository->applicationExists(
            $member,
            $event
        );
    }
}
