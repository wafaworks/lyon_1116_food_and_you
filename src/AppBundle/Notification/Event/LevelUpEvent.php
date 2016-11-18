<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Member;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class LevelUpEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.level_up';

    /**
     * @var Member
     */
    private $member;

    /**
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }
}
