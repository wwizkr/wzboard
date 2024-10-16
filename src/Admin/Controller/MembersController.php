<?php
// 파일 위치: /src/Admin/Controller/MemberController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminCommonHelper;

class MembersController
{
    protected $container;
    protected $membersService;
    protected $config_domain;
    protected $cf_id;
    protected $formDataMiddleware;
    protected $componentsViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->cf_id = $this->config_domain['cf_id'];
        $this->membersService = $this->container->get('MembersService');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    protected function getLevelData()
    {
        return $this->membersService->getMemberLevelData();
    }

    public function list($vars)
    {
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
        $mbNo = isset($vars['param']) ? CommonHelper::pickNumber($vars['param']) : 0;

        $memberData = $this->membersService->getMemberDataByNo($mbNo, true);

        $level = $this->getLevelData();
        $levelData = $this->membersService->formatLevelDataArray($level);

        $levelSelect = CommonHelper::makeSelectBox(
            'searchData[member_level]',
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

    public function memberListModify()
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }

    public function memberListDelete()
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }
}
