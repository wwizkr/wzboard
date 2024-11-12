<?php
//파일위치 src/Service/MembersService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Service\AdminLevelService;

class MembersService
{
    private $container;
    private $config_domain;
    private $sessionManager;
    private $membersModel;
    private $formDataMiddleware;
    private $authMiddleware;
    private $cryptoHelper;
    private $adminLevelService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->membersModel = $this->container->get('MembersModel');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->authMiddleware = $this->container->get('AuthMiddleware');
        $this->cryptoHelper = $this->container->get('CryptoHelper');

        $this->adminLevelService = new AdminLevelService($this->container);
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
    
    /**
     * 회원 레벨 데이터를 가져옵니다.
     *
     * @return array 회원 레벨 목록
     */
    public function getMemberLevelData($level = false)
    {
        return $this->adminLevelService->getMemberLevelData($level);
    }

    

    public function getMemberDataById($value=null)
    {
        return $this->membersModel->getMemberDataById($value);
    }

    /**
     * 회원 레벨 데이터를 가져옵니다.
     *
     * @return array 회원 레벨 목록
     
    public function getLevelData()
    {
        return $this->membersModel->getMemberLevelData();
    }
    */


    /**
     * 회원 레벨데이터를 level_id => level_name 배열로 가공
     * AdminLevelService 로 이전, 차후 삭제
     */
    public function formatLevelDataArray(array $level): array
    {
        $authUser = $this->authMiddleware->getAuthUser();

        $levelData = [];
        foreach ($level as $val) {
            if ($authUser['mb_level'] < $val['level_id']) {
                continue;
            }
            $levelData[$val['level_id']] = $val['level_name'];
        }
        return $levelData;
    }

    public function getMemberList()
    {
        // 기본 설정 로드
        $config = [];

        $level = $this->adminLevelService->getMemberLevelData();
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
    
    /**
     * 사용자 페이지 /src/Controller/Members
     */
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
        $formData['cf_class'] = $this->config_domain['cf_class'];
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
            $level = $this->getMemberLevelData($result['member_level']) ?? [];
            $authService = $this->container->get('AuthService');
            $authService->login($result, $level);
        }
        
        // social login 이 아닌 경우에만
        return true;
    }

    /**
     * 관리자 페이지 /Admin/Controller/Members
     */
    public function memberUpdate(int $no)
    {
        if ($no) {
            $memberData = $this->membersModel->getMemberDataByNo($no, false);

            if (empty($memberData)) {
                return [
                    'result' => 'failure',
                    'message' => '회원 정보를 찾을 수 없습니다.',
                ];
            }
        }

        // 회원 레벨 정보
        $memberLevel = isset($_POST['formData']['member_level']) ? CommonHelper::pickNumber($_POST['formData']['member_level']) : 0;
        $levelData = $this->getMemberLevelData($memberLevel) ?? [];
        
        // 회원 비밀번호
        $password = isset($_POST['formData']['password']) ? CommonHelper::validateParam('password', 'string', '', $_POST['formData']['password'], null) : '';
        $password = $password ? $this->cryptoHelper->hashPassword($password) : '';
        unset($_POST['formData']['password']);

        // 폼데이터 정리
        $formData = isset($_POST['formData']) ? $_POST['formData'] : [];
        $numericFields = ['cf_id', 'age', 'member_level', 'is_social_login'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);
        $data['user_ip'] = ['s', CommonHelper::getUserIp()];

        // 비밀번호 정리
        if ($password ) {
            $data['password'] = ['s', $password];
        }
        
        return $result = $this->membersModel->memberUpdate($no, $data, $levelData);
    }

    public function validate(string $field, string $value)
    {
        switch($field) {
            case 'userid':
                $result = $this->validateUserId($value);
                break;
            case 'email':
                $result = $this->validateEmail($value);
                break;
            default:
                $result = [
                    'result' => 'failure',
                    'message' => '잘못된 검증 필드입니다.',
                ];
                break;
        }

        return $result;
    }

    public function validateUserId($userId)
    {
        $result = [
            'result' => 'success',
            'message' => '사용 가능한 아이디 입니다.',
        ];

        $data = $this->membersModel->getMemberDataById($userId);
        if (!empty($data) && $userId === $data['mb_id']) {
            $result = [
                'result' => 'failure',
                'message' => '이미 사용 중인 아이디입니다.',
            ];
        }

        return $result;
    }

    public function validateEmail($email)
    {
        $result = [
            'result' => 'success',
            'message' => '사용 가능한 이메일 입니다.',
        ];

        $data = $this->membersModel->getMemberDataById($email);
        if (!empty($data) && $email === $data['email']) {
            $result = [
                'result' => 'failure',
                'message' => '이미 사용 중인 이메일입니다.',
            ];
        }

        return $result;
    }
}