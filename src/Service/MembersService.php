<?php
//파일위치 src/Service/MembersService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminCommonHelper;

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
    public function getMemberDataByNo($mb_no = null, $null = false)
    {
        if (!$mb_no && $null === false) {
            $ss_mb = $this->sessionManager->get('auth');
            $mb_no = $ss_mb['mb_no'] ?? null;

            if (!$mb_no) {
                return null;
            }
        }
        
        // 결과값에 is_super, is_admin 추가
        $result = $this->membersModel->getMemberDataByNo($mb_no, $null);
        $result['is_admin'] = $ss_mb['is_admin'] ?? 0;
        $result['is_super'] = $ss_mb['is_super'] ?? 0;
        
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
    
    /**
     * 회원 레벨데이터를 level_id => level_name 배열로 가공
     *
     */
    public function formatLevelDataArray(array $level): array
    {
        $levelData = [];
        foreach ($level as $val) {
            $levelData[$val['level_id']] = $val['level_name'];
        }
        return $levelData;
    }

    public function getMemberList()
    {
        // 기본 설정 로드
        $config = [
            'cf_page_rows' => $this->config_domain['cf_page_rows'],
            'cf_page_nums' => $this->config_domain['cf_page_nums']
        ];

        $level = $this->getLevelData();
        $levelData = $this->formatLevelDataArray($level);

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['nickName', 'phone', 'email'];
        $allowedSortFields = ['mb_no', 'signup_date'];
        
        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                if ($key === 'member_level') {
                    $allowed = !empty($levelData) ? array_keys($levelData) : [];
                }

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총회원수
        $totalItems = $this->getTotalMemberCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 회원 목록 데이터 조회
        $memberData = $this->membersModel->getMemberListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );
        
        $memberList = [];
        foreach ($memberData as $key => $member) {
            if (isset($member['password'])) {
                unset($memberData[$key]['password']);
            }
            $memberList[$key] = $member;
            $memberList[$key]['levelSelect'] = CommonHelper::makeSelectBox(
                'listData[member_level]['.$key.']',
                $levelData ?? [],
                $member['member_level'] ?? '',
                'member_level_'.$key,
                'frm_input frm_full',
                '선택'
            );
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'memberList' => $memberList,
            'levelData' => $levelData,
        ];
    }

    public function getTotalMemberCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->membersModel->getTotalMemberCount($searchQuery, $filters, $additionalQueries);
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