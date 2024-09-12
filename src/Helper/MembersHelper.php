<?php
// 파일 위치: /src/Helper/MembersHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Model\MembersModel;

class MembersHelper
{
    protected $container;
    protected $membersModel; // 회원 관련 모델 인스턴스
    protected $sessionManager; // 세션 관리자 인스턴스

    /**
     * 생성자: MembersHelper 인스턴스를 생성합니다.
     *
     * @param MembersModel $membersModel 회원 모델 인스턴스
     * @param SessionManager $sessionManager 세션 관리자 인스턴스
     */
    public function __construct(DependencyContainer $container, MembersModel $membersModel)
    {
        $this->container = $container;
        $this->membersModel = $membersModel;
        $this->sessionManager = $this->container->get('session_manager');
    }

    /**
     * 회원 정보를 가져옵니다.
     *
     * @param int $mb_no 회원 번호
     * @return array 회원 정보
     */
    public function getMemberDataByNo($mb_no = null)
    {
        if (!$mb_no) {
            $mb = $this->sessionManager->get('auth');
            $mb_no = $mb['mb_no'] ?? null;
        }
        
        if (!$mb_no) {
            return null;
        }

        return $this->membersModel->getMemberDataByNo($mb_no);
    }

    public function getMemberDataById($email=null)
    {
        return $this->membersModel->getMemberDataById($email);
    }

    /**
     * 회원 레벨 데이터를 가져옵니다.
     *
     * @return array 회원 레벨 목록
     */
    public function getLevelData()
    {
        return $this->membersModel->getMemberLevelData();
    }

    public function getMemberLevelData($level=null)
    {
        return $this->membersModel->getMemberLevelData($level);
    }
}