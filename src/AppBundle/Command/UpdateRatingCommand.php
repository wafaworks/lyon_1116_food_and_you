<?php

namespace AppBundle\Command;

use AppBundle\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateRatingCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-rating')
            ->setDescription('Updates the application recipe rating');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->updateApplicantRecipeRatings($output);
        $this->updateMemberRating($output);
        $this->countVotes($output);
        $this->updateEventRating($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function updateApplicantRecipeRatings(OutputInterface $output)
    {
        $output->writeln('Update applicant recipe rating');

        $recipeRepository = $this->getContainer()->get('app.repository.applicant_recipe');
        $ratingRepository = $this->getContainer()->get('app.repository.applicant_recipe_rating');

        $applicantRecipeIds = $recipeRepository->getUnaccountedRecipeIds();

        foreach ($applicantRecipeIds as $recipeId) {
            $ratingRepository->updateRating($recipeId);
        }

        $output->writeln('Updated ' . count($applicantRecipeIds) . ' applicant recipe rating');
    }

    /**
     * @param OutputInterface $output
     */
    protected function updateMemberRating(OutputInterface $output)
    {
        $output->writeln('Update member rating');

        $memberRepository = $this->getContainer()->get('app.repository.member');
        $applicantRepository = $this->getContainer()->get('app.repository.applicant');
        $levelChecker = $this->getContainer()->get('app.level.checker');
        $memberRates = $applicantRepository->getAllAverageRates();

        foreach ($memberRates as $memberRate) {
            /** @var Member $member */
            $member = $memberRepository->find($memberRate['id']);
            $member->setRating($memberRate['rating']);
            $member->setParticipations($memberRate['participations']);
            $levelChecker->updateLevelForMember($member);
            $memberRepository->save($member);
        }
        $output->writeln('Updated ' . count($memberRates) . ' member ratings');
    }

    /**
     * @param OutputInterface $output
     */
    private function countVotes(OutputInterface $output)
    {
        $output->writeln('Update applicant vote count');

        $applicantVoteRepository = $this->getContainer()->get('app.repository.applicant_vote');

        $applicantIds = $applicantVoteRepository->getUnaccountedVoteApplicantIds();

        foreach ($applicantIds as $applicantId) {
            $applicantVoteRepository->updateVoteCount($applicantId);
        }

        $output->writeln('Updated ' . count($applicantIds) . ' applicant vote counts');
    }

    /**
     * @param OutputInterface $output
     */
    private function updateEventRating(OutputInterface $output)
    {
        $output->writeln('Updating event ratings');

        $eventRepository = $this->getContainer()->get('app.repository.event');
        $eventRatingsRepository = $this->getContainer()->get('app.repository.member.event.ratings');

        $eventIds = $eventRepository->getUnaccountedEventIds();

        foreach ($eventIds as $eventId) {
            $eventRatingsRepository->updateRating($eventId);
        }

        $output->writeln('Updated ' . count($eventIds) . ' event rating');
    }
}
