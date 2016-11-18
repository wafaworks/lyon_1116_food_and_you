<?php

namespace AppBundle\Event\Listener;

use AppBundle\Entity\Member;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MemberListener implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @ORM\PrePersist
     * @param Member $member
     * @param LifecycleEventArgs $event
     */
    public function prePersistHandler(Member $member, LifecycleEventArgs $event)
    {
        if (empty($member->getTableCode())) {
            $this->container->get('app.manager.table_token')->regenerateMemberToken($member);
        }
    }
}
