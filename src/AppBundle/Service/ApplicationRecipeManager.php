<?php

namespace AppBundle\Service;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Repository\ApplicantRecipeRepository;
use AppBundle\Notification\Event\ApplicantChosenEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationRecipeManager
{
    /**
     * @var ApplicantRecipeRepository
     */
    private $applicantRecipeRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param ApplicantRecipeRepository $applicantRecipeRepository
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager $entityManager
     */
    public function __construct(ApplicantRecipeRepository $applicantRecipeRepository, EventDispatcherInterface $dispatcher, EntityManager $entityManager)
    {
        $this->applicantRecipeRepository = $applicantRecipeRepository;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * Select and deselect applicant recipes
     *
     * @param Event $event
     * @param $applicantRecipeIds
     */
    public function markSelectedRecipes(Event $event, $applicantRecipeIds)
    {
        $prevSelectedRecipeIds = $this->applicantRecipeRepository->getSelectedRecipes($event);

        $ids = array_unique(array_merge($applicantRecipeIds, $prevSelectedRecipeIds));
        $applicantRecipes = $this->applicantRecipeRepository->getApplicantRecipesByIds($event, $ids);

        // deselect applicant and his recipe
        foreach ($applicantRecipes as $applicantRecipe) {
            $this->deselect($applicantRecipe, $applicantRecipeIds, $prevSelectedRecipeIds);
        }

        // do not join loops!!! as possible Applicant status conflict
        foreach ($applicantRecipes as $applicantRecipe) {
            $this->select($applicantRecipe, $applicantRecipeIds, $prevSelectedRecipeIds);
            $this->entityManager->persist($applicantRecipe);
            $this->entityManager->persist($applicantRecipe->getApplicant());
            $this->entityManager->flush();
        }
    }

    /**
     * @param ApplicantRecipe $applicantRecipe
     * @param array $applicantRecipeIds
     * @param array $prevSelectedRecipeIds
     */
    protected function deselect(ApplicantRecipe $applicantRecipe, array $applicantRecipeIds, array $prevSelectedRecipeIds)
    {
        if (in_array($applicantRecipe->getId(), $prevSelectedRecipeIds) &&
            !in_array($applicantRecipe->getId(), $applicantRecipeIds)
        ) {
            $applicantRecipe->setSelected(false);
            $applicantRecipe->getApplicant()->setStatus(Applicant::STATUS_REJECTED);
        }
    }

    /**
     * @param ApplicantRecipe $applicantRecipe
     * @param array $applicantRecipeIds
     * @param array $prevSelectedRecipeIds
     */
    protected function select(ApplicantRecipe $applicantRecipe, $applicantRecipeIds, $prevSelectedRecipeIds)
    {
        if (!in_array($applicantRecipe->getId(), $prevSelectedRecipeIds) &&
            in_array($applicantRecipe->getId(), $applicantRecipeIds)
        ) {
            $applicantRecipe->setSelected(true);
            $applicantRecipe->getApplicant()->setStatus(Applicant::STATUS_ACCEPTED);
            $this->dispatcher->dispatch(
                ApplicantChosenEvent::EVENT_NAME,
                new ApplicantChosenEvent($applicantRecipe->getApplicant())
            );
        }
    }
}
