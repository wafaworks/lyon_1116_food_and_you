<?php

namespace AppBundle\Service;

use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\MemberRepository;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class TableTokenManager
{
    const TABLE_HASH_LENGTH = 8;

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var HashGenerator
     */
    private $hashGenerator;

    /**
     * @param MemberRepository $memberRepository
     * @param HashGenerator $hashGenerator
     */
    public function __construct(MemberRepository $memberRepository, HashGenerator $hashGenerator)
    {
        $this->memberRepository = $memberRepository;
        $this->hashGenerator = $hashGenerator;
    }

    /**
     * @param $token
     * @return Member|null
     */
    public function findMemberByToken($token)
    {
        return $this
            ->memberRepository
            ->getMemberByTableCode($token);
    }

    /**
     * @param Member $member
     */
    public function regenerateMemberToken(Member $member)
    {
        $hash = $this->getNewHash();
        while ($this->memberRepository->tableCodeInUse($hash)) {
            $hash = $this->getNewHash();
        }

        $member->setTableCode($hash);
    }

    /**
     * @return string
     */
    protected function getNewHash()
    {
        return 'FY' . $this->hashGenerator->generate(self::TABLE_HASH_LENGTH);
    }
}
