<?php
// 파일 위치: /src/Admin/Controller/MembersController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Service\AdminLevelService;
use Web\Admin\Helper\AdminMenuHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminCommonHelper;

class MembersController
{
    protected $container;
    protected $membersService;
    protected $config_domain;
    protected $componentsViewHelper;
    protected $adminLevelService;
    protected $formDataMiddleware;
    protected $authMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->membersService = $this->container->get('MembersService');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->authMiddleware = $this->container->get('AuthMiddleware');

        $this->adminLevelService = new AdminLevelService($this->container);
    }

    public function list($vars)
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('r');

        $memberData = $this->membersService->getMemberList();

        $levelData = $memberData['levelData'];
        
        $searchSelectBox = [
            'pagenum' => CommonHelper::makeSelectBox(
                'pagenum',
                CommonHelper::pagingOption(),
                (string)(CommonHelper::pickNumber($_GET['pagenum'] ?? 0)),
                'pagenum',
                'frm_input list-search-item'
            ),
            'member_level' => CommonHelper::makeSelectBox(
                'searchData[member_level]',
                $levelData ?? [],
                $_GET['searchData']['member_level'] ?? '',
                'member_level',
                'frm_input list-search-item',
                '회원등급'
            )
        ];
        
        $params = $memberData['params'];
        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $memberData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].$queryString;

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 관리',
            'content' => '',
            'totalItems' => $memberData['totalItems'],
            'searchSelectBox' => $searchSelectBox,
            'memberList' => $memberData['memberList'],
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => 'Members/memberList',
            'viewData' => $viewData,
        ];
    }

    public function memberForm($vars)
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('r');

        $mbNo = isset($vars['param']) ? CommonHelper::pickNumber($vars['param']) : 0;

        $memberData = $this->membersService->getMemberDataByNo($mbNo, true);
        unset($memberData['password']);

        $level = $this->adminLevelService->getMemberLevelData();
        $levelData = $this->adminLevelService->formatLevelDataArray($level);

        $levelSelect = CommonHelper::makeSelectBox(
            'formData[member_level]',
            $levelData ?? [],
            (string)$memberData['member_level'] ?? '',
            'member_level',
            'frm_input frm_full',
            '회원등급'
        );

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 관리',
            'levelSelect' => $levelSelect,
            'memberData' => $memberData,
        ];

        return [
            'viewPath' => 'Members/memberForm',
            'viewData' => $viewData,
        ];
    }

    function memberUpdate($vars)
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('w');

        $no = isset($_POST['memberNo']) ? CommonHelper::pickNumber($_POST['memberNo']) : 0;

        $result = $this->membersService->memberUpdate($no);

        if ($result['result'] === 'success') {
            $msg = $no ? '수정' : '등록';
            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '회원정보를 '.$msg.' 하였습니다.',
                'data' => [
                    'memberNo' => $result['memberNo'],
                ],
            ]);
        }

        return CommonHelper::jsonResponse([
            'result' => 'failure',
            'message' => '오류가 발생 하였습니다.',
            'data' => [],
        ]);
    }

    public function memberListModify()
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('r');

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }

    public function memberListDelete()
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('d');

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }

    public function memberLevel()
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('r');

        $level = $this->adminLevelService->getMemberLevelData(false, false, 'DESC');

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 등급관리',
            'levelData' => $level,
        ];

        return [
            'viewPath' => 'Members/memberLevel',
            'viewData' => $viewData,
        ];
    }

    public function memberLevelModify()
    {
        // 권한 체크
        $this->authMiddleware->checkAdminAuth('w');

        $result = $this->adminLevelService->memberLevelModify();

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '회원 레벨정보를 수정하였습니다.',
            'data' => [],
        ]);
    }

    public function memberAuth()
    {
        $this->authMiddleware->checkAdminAuth('r');

        $level = $this->adminLevelService->getMemberLevelData();
        
        $adminMenuHelper = new AdminMenuHelper($this->container);
        $menuDatas = $adminMenuHelper->getAdminMenu();

        $authData = $this->adminLevelService->getAdminAuthData();

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '등급별 권한 관리',
            'levelData' => $level,
            'menuData' => $menuDatas,
            'authData' => $authData,
        ];

        return [
            'viewPath' => 'Members/memberAuth',
            'viewData' => $viewData,
        ];
    }

    public function memberAuthUpdate()
    {
        $this->authMiddleware->checkAdminAuth('w');
        
        $result = $this->adminLevelService->memberAuthUpdate();

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '관리자 권한 정보를 수정하였습니다.',
            'data' => [],
        ]);
    }

    public function memberAuthDelete($vars)
    {
        $this->authMiddleware->checkAdminAuth('d');

        $no = $vars['param'] ?? 0;

        $result = $this->adminLevelService->memberAuthDelete($no);
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '관리자 권한 정보를 삭제하였습니다.',
            'data' => ['result' => $result, 'post' => $vars],
        ]);
    }

    public function memberAuthListModify()
    {
        $this->authMiddleware->checkAdminAuth('w');
        
        $result = $this->adminLevelService->memberAuthListUpdate('update');

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '관리자 권한 정보를 수정하였습니다.',
            'data' => [],
        ]);
    }

    public function memberAuthListDelete()
    {
        $this->authMiddleware->checkAdminAuth('d');
        $data = $_POST;
        $result = $this->adminLevelService->memberAuthListUpdate('delete');

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '관리자 권한 정보를 삭제하였습니다.',
            'data' => [],
        ]);
    }
    
    // 아이디, 이메일 등 중복 체크
    public function validate($vars)
    {
        $data = CommonHelper::getJsonInput();

        $field = $vars['param'] ?? '';
        $value = CommonHelper::validateParam('value', 'string', '', $data['value'], null);

        if (!$field || !$value) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.',
                'data' => [],
            ]);
        };

        $result = $this->membersService->validate($field, $value);

        return CommonHelper::jsonResponse([
            'result' => $result['result'],
            'message' => $result['message'],
            'data' => [],
        ]);
    }
}
