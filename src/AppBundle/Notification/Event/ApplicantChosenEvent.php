<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Applicant;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class ApplicantChosenEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.applicant_chosen';

    /**
     * @var Applicant
     */
    private $applicant;

    /**
     * @param Applicant $applicant
     */
    public function __construct(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * @return Applicant
     */
    public function getApplicant()
    {
        return $this->applicant;
    }
}
