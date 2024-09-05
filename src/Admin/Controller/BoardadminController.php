<?php
// 파일 위치: /src/Admin/Controller/BoardadminController.php
// 게시판 그룹관리, 카테고리 관리, 게시판 설정 관리 등 관리 컨트롤러
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;
use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class BoardadminController
{
    protected $container;
    protected $boardsHelper;
    protected $membersHelper;
    protected $adminBoardsModel;
    protected $adminBoardsService;
    protected $boardsModel;
    protected $boardsService;
    protected $membersModel;
    protected $membersService;
    protected $configDomain;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->adminBoardsModel = new AdminBoardsModel($container);
        $this->adminBoardsService = new AdminBoardsService($this->adminBoardsModel);
        $this->boardsHelper = new BoardsHelper($this->adminBoardsService);
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->membersHelper = new MembersHelper($this->membersService);
        $this->configDomain = $container->get('config_domain');

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    // ---------------------------
    // 그룹 관리 메서드
    // ---------------------------

    public function group()
    {
        $viewData = [
            'title' => '게시판 그룹 관리',
            'content' => '',
            'config_domain' => $this->configDomain,
            'groupData' => $this->boardsHelper->getGroupData(),
            'levelData' => $this->membersHelper->getLevelData(),
        ];

        return ['AdminBoards/group', $viewData];
    }

    public function groupUpdate()
    {
        $action = $_POST['action'] ?? null;
        $group_no = CommonHelper::pickNumber($_POST['group_no'], 0) ?? 0;
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.'
            ]);
        }

        $numericFields = ['allow_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            unset($data['group_id']); // update 시 아이디는 변경 불가
            $this->adminBoardsService->updateBoardsGroup($group_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsGroup($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.'
        ]);
    }

    // 카테고리 관리 메서드

    public function category()
    {
        $viewData = [
            'title' => '게시판 카테고리 관리',
            'content' => '',
            'config_domain' => $this->configDomain,
            'categoryData' => $this->boardsHelper->getCategoryData(),
            'levelData' => $this->membersHelper->getLevelData(),
        ];

        return ['AdminBoards/category', $viewData];
    }

    public function categoryUpdate()
    {
        $action = $_POST['action'] ?? null;
        $category_no = CommonHelper::pickNumber($_POST['category_no'], 0) ?? 0;
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.'
            ]);
        }

        $numericFields = ['allow_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            //$categoryData = $this->adminBoardsService->getBoardsCategory($category_no);
            $this->adminBoardsService->updateBoardsCategory($category_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsCategory($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.'
        ]);
    }

    // 게시판 관리 메서드 목록, 생성, 수정, 삭제

    public function boards()
    {
        $viewData = [
            'title' => '게시판 관리',
            'content' => '',
            'config_domain' => $this->configDomain,
            'boardsConfig' => $this->boardsHelper->getBoardsConfig(),
            'levelData' => $this->membersHelper->getLevelData(),
        ];

        return ['AdminBoards/boards', $viewData];
    }

    public function boardform($vars)
    {
        $action = $vars['param'] ?? 'create';
        $selectBoard = []; //update일때.
        $viewData = [
            'title' => '게시판 생성',
            'content' => '',
            'config_domain' => $this->configDomain,
            'groupData' => $this->boardsHelper->getGroupData(),
            'categoryData' => $this->boardsHelper->getCategoryData(),
            'levelData' => $this->membersHelper->getLevelData(),
            'skinData' => $this->boardsHelper->getSkinData(),
            'selectBoard' => $selectBoard,
            'action' => $action,
        ];

        return ['AdminBoards/boardForm', $viewData];
    }

    public function boardUpdate()
    {
        $action = $_POST['action'] ?? null;
        $board_no = CommonHelper::pickNumber($_POST['board_no'], 0) ?? 0;
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.'
            ]);
        }

        $numericFields = [
            'group_no', 'read_level', 'write_level', 'download_level',
            'is_use_file', 'file_size_limit', 'use_separate_table'
        ];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            $this->adminBoardsService->updateBoardsConfig($board_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsConfig($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.'
        ]);
    }
}