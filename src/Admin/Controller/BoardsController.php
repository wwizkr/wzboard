<?php
// 파일 위치: /src/Admin/Controller/BoardsController.php
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\Admin\Helper\BoardsHelper;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class BoardsController
{
    protected $container;
    protected $boardsModel;
    protected $boardsService;
    protected $membersModel;
    protected $membersService;
    protected $configDomain;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->boardsModel = new BoardsModel($container);
        $this->boardsService = new BoardsService($this->boardsModel);
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->configDomain = $container->get('config_domain');

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    protected function getGroupData()
    {
        return $this->boardsService->getBoardsGroup(null);
    }

    protected function getCategoryData()
    {
        return $this->boardsService->getBoardsCategory(null);
    }

    protected function getBoardData()
    {
        return $this->boardsService->getBoardsList(null);
    }

    protected function getLevelData()
    {
        return $this->membersService->getMemberLevelData();
    }

    protected function getSkinData()
    {
        return BoardsHelper::getBoardSkinDir();
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
            'groupData' => $this->getGroupData(),
            'levelData' => $this->getLevelData(),
        ];

        return ['Boards/group', $viewData];
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
            $this->boardsService->updateBoardsGroup($group_no, $data);
        } else {
            $this->boardsService->insertBoardsGroup($data);
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
            'categoryData' => $this->getCategoryData(),
            'levelData' => $this->getLevelData(),
        ];

        return ['Boards/category', $viewData];
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
            $categoryData = $this->boardsService->getBoardsCategory($category_no);
            $this->boardsService->updateBoardsCategory($category_no, $data, $categoryData);
        } else {
            $this->boardsService->insertBoardsCategory($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.'
        ]);
    }

    // 게시판 관리 메서드 목록, 생성, 수정, 삭제

    public function configs()
    {
        $viewData = [
            'title' => '게시판 관리',
            'content' => '',
            'config_domain' => $this->configDomain,
            'boardData' => $this->getBoardData(),
            'levelData' => $this->getLevelData(),
        ];

        return ['Boards/configs', $viewData];
    }

    public function boardform($vars)
    {
        $action = $vars['param'] ?? 'create';
        $selectBoard = []; //update일때.
        $viewData = [
            'title' => '게시판 생성',
            'content' => '',
            'config_domain' => $this->configDomain,
            'groupData' => $this->getGroupData(),
            'categoryData' => $this->getCategoryData(),
            'levelData' => $this->getLevelData(),
            'skinData' => $this->getSkinData(),
            'selectBoard' => $selectBoard,
            'action' => $action,
        ];

        return ['Boards/boardForm', $viewData];
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
            $this->boardsService->updateBoardsConfig($board_no, $data);
        } else {
            $this->boardsService->insertBoardsConfig($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.'
        ]);
    }
}
