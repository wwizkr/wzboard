<?php
// 파일 위치: /src/Helper/MembersHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Service\MembersService;

class MembersHelper
{
    protected $membersService; // 회원 관련 서비스 인스턴스

    /**
     * 생성자: MemberHelper 인스턴스를 생성합니다.
     *
     * @param MembersService $membersService 회원 서비스 인스턴스
     */
    public function __construct(MembersService $membersService)
    {
        $this->membersService = $membersService;
    }

    /**
     * 회원 정보를 가져옵니다.
     *
     * @return array 회원 정보
     */
    function getMemberData($mb_no)
    {
        return $this->memberService->getMemberData($mb_no);
    }

    /**
     * 회원 레벨 데이터를 가져옵니다.
     *
     * @return array 회원 레벨 목록
     */
    public function getLevelData()
    {
        return $this->membersService->getMemberLevelData();
    }
}