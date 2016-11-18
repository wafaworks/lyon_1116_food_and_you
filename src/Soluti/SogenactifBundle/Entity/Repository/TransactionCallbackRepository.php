<?php

namespace Soluti\SogenactifBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Soluti\SogenactifBundle\Entity\TransactionCallback;

class TransactionCallbackRepository extends EntityRepository
{
    public function save($callback)
    {
        $transactionCallback = new TransactionCallback();
        $transactionCallback->setCallback($callback);

        $this->_em->persist($transactionCallback);
        $this->_em->flush();
    }
}