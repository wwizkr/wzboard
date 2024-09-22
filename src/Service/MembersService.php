<?php
//파일위치 src/Service/MembersService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;

class MembersService
{
    protected $container;
    protected $config_domain;
    protected $sessionManager;
    protected $cookieManager;
    protected $membersModel;
    protected $membersHelper;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->membersModel = $container->get('MembersModel');
        $this->membersHelper = $container->get('MembersHelper');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->cookieManager = $this->container->get('CookieManager');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
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
            $ss_mb = $this->sessionManager->get('auth');
            $mb_no = $ss_mb['mb_no'] ?? null;
        }
        
        if (!$mb_no) {
            return null;
        }
        
        // 결과값에 is_super, is_admin 추가
        $result = $this->membersModel->getMemberDataByNo($mb_no);
        $result['is_admin'] = $ss_mb['is_admin'];
        $result['is_super'] = $ss_mb['is_super'];
        return $result;
    }

    public function getMemberLevelData($level=null)
    {
        return $this->membersModel->getMemberLevelData($level);
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

    public function getMemberListData($currentPage, $page_rows, $searchQuery, $filters, $sort)
    {
        return $this->membersModel->getMemberListData($currentPage, $page_rows, $searchQuery, $filters, $sort);
    }

    public function getTotalMemberCount($searchQuery, $filters)
    {
        return $this->membersModel->getTotalMemberCount($searchQuery, $filters);
    }
    
    public function insertMemberData(array $memberData = [])
    {
        if (empty($memberData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        $isSocial = $memberData['is_social_login'] ?? 0;

        // social이 아닐 때, profile_picture 체크 [전체 URL 반영]
        
        $formData = $memberData;
        $formData['cf_id'] = $this->config_domain['cf_id'];
        $formData['member_level'] = 1;
        $formData['user_ip'] = CommonHelper::getUserIp();

        // 회원가입 시 적립금 등. 환경설정에서 가져온 입력값 추가

        $numericFields = ['cf_id', 'age', 'member_level', 'is_social_login'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        $result = $this->membersModel->insertMemberData($isSocial, $data);
        
        if (isset($result['result']) && $result['result'] === 'failure') {
            return $result;
        }
        
        // social login 일 경우 바로 로그인 상태로 변경.
        if ($isSocial) {
            $level = $this->membersHelper->getMemberLevelData($result['member_level']) ?? [];
            $authService = $this->container->get('AuthService');
            $authService->login($result, $level);
        }
        
        // social login 이 아닌 경우에만
        return true;
    }
}