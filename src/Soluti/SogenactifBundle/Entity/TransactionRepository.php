<?php

namespace Soluti\SogenactifBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class TransactionRepository extends EntityRepository
{
    /**
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction)
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $mode (iterate or all)
     * @param \DateTime|null $olderThen
     *
     * @return Transaction[]|\Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getTransactionsWithNoResponse(\DateTime $olderThen = null, $mode = 'iterate')
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.created < :date')
            ->andWhere('t.response_code IS NULL')
            ->setParameter('date', $olderThen)
            ;

        if ($mode === 'iterate') {
            return $qb->getQuery()->iterate();
        }

        return $qb->getQuery()->getResult();
    }
}
