<?php

namespace AppBundle\Service;

use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\MemberRepository;
use AppBundle\Notification\Event\LevelUpEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class LevelChecker
{
    const LEVEL_DEFINITION = array(
        0 => ['rating' => 0, 'participations' => 0],
        1 => ['rating' => 2.5, 'participations' => 1],
        2 => ['rating' => 3, 'participations' => 2],
        3 => ['rating' => 3.5, 'participations' => 4],
        4 => ['rating' => 4, 'participations' => 6],
        5 => ['rating' => 4.5, 'participations' => 8],
    );

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * LevelChecker constructor.
     * @param MemberRepository $memberRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(MemberRepository $memberRepository, EventDispatcherInterface $dispatcher)
    {
        $this->memberRepository = $memberRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Member $member
     */
    public function updateLevelForMember(Member $member)
    {
        $newLevel = $this->computeLevel($member);

        if ($newLevel > $member->getLevel()) {
            $member->setLevel($newLevel);
            $this->dispatcher->dispatch(
                LevelUpEvent::EVENT_NAME,
                new LevelUpEvent($member)
            );
        }

        if ($newLevel != $member->getLevel()) {
            $member->setLevel($newLevel);
        }
    }

    /**
     * @param Member $member
     * @return int
     */
    private function computeLevel(Member $member)
    {
        for ($i = count(self::LEVEL_DEFINITION) - 1; $i >= 0; $i--) {
            if ($this->checkLevelConditions($member, self::LEVEL_DEFINITION[$i])) {
                return $i;
            };
        }

        return 0;
    }

    /**
     * @param Member $member
     * @param array $levelStats
     * @return bool
     */
    private function checkLevelConditions(Member $member, array $levelStats)
    {
        return (round($member->getRating()*2)/2) >= $levelStats['rating'] &&
        $member->getParticipations() >= $levelStats['participations'];
    }
}
