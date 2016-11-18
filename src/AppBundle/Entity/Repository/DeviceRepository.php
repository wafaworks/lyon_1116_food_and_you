<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Device;
use AppBundle\Entity\Event;
use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class DeviceRepository extends EntityRepository
{
    /**
     * Find existing or create a new Device
     *
     * @param array|null $deviceInfo
     * @return Device
     */
    public function create(array $deviceInfo = [])
    {
        if (!$deviceInfo || !array_key_exists('token', $deviceInfo)) {
            return new Device();
        }

        $device =  $this->findOneBy(array('token' => $deviceInfo['token']));

        return $device ?: new Device();
    }

    /**
     * @param Device $device
     */
    public function save(Device $device)
    {
        $this->getEntityManager()->persist($device);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Event $event
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getDevicesForEvent(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->select('d')
            ->innerJoin('d.member', 'm')
            ->innerJoin('m.reservations', 'r')
            ->innerJoin('r.event', 'e')
            ->where('e = :event')
            ->andWhere('r.status = :reservationStatus')
            ->setParameter('event', $event)
            ->setParameter('reservationStatus', Reservation::STATUS_CONFIRMED)
            ;

        return $qb->getQuery()->getResult();
    }
}
