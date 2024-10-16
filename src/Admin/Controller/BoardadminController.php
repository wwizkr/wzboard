<?php
namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\CommonHelper;
//use Web\PublicHtml\Middleware\FormDataMiddleware;
//use Web\Admin\Helper\AdminBoardsHelper;
//use Web\PublicHtml\Service\MembersHelper;

class BoardadminController
{
    protected DependencyContainer $container;
    protected $sessionManager;
    protected $adminBoardsHelper;
    protected $adminBoardsService;
    protected $membersService;
    protected $config_domain;
    protected $formDataMiddleware;
    protected $componentsViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;

        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->adminBoardsHelper = $this->container->get('AdminBoardsHelper');
        $this->membersService = $this->container->get('MembersService');
        $this->adminBoardsService = $this->container->get('AdminBoardsService');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    public function group(): array
    {
        $viewData = [
            'title' => '게시판 그룹 관리',
            'content' => '',
            'config_domain' => $this->config_domain,
            'groupData' => $this->adminBoardsService->getBoardsGroup(),
            'levelData' => $this->membersService->getLevelData(),
        ];

        return [
            'viewPath' => 'AdminBoards/group',
            'viewData' => $viewData,
        ];
    }

    public function groupUpdate(): array
    {
        $action = $_POST['action'] ?? null;
        $group_no = (int)($_POST['group_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.',
            ]);
        }

        $numericFields = ['allow_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            unset($data['group_id']);
            $this->adminBoardsService->updateBoardsGroup($group_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsGroup($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.',
        ]);
    }

    public function category(): array
    {
        $level = $this->membersService->getLevelData();
        $levelData = $this->membersService->formatLevelDataArray($level);
        $levelSelect = $this->adminBoardsHelper->getLevelSelectBox('listData');

        $categoryList = $this->adminBoardsService->getCategoryData(null, $levelData);

        $viewData = [
            'title' => '게시판 카테고리 관리',
            'config_domain' => $this->config_domain,
            'categoryList' => $categoryList,
            'levelSelect' => $levelSelect,
        ];

        return [
            'viewPath' => 'AdminBoards/category',
            'viewData' => $viewData,
        ];
    }

    public function categoryUpdate(): array
    {
        $action = $_POST['action'] ?? null;
        $category_no = (int)($_POST['category_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.',
            ]);
        }

        $numericFields = ['list_level', 'read_level', 'write_level', 'comment_level', 'download_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            $this->adminBoardsService->updateBoardsCategory($category_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsCategory($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.',
        ]);
    }

    public function boards(): array
    {
        $boardData = $this->adminBoardsService->getBoardsList();

        $params = $boardData['params'];

        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $boardData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);
        
        $searchSelectBox = [];

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].$queryString;

        $viewData = [
            'title' => '게시판 관리',
            'totalItems' => $boardData['totalItems'],
            'boardList' => $boardData['boardList'],
            'searchSelectBox' => $searchSelectBox,
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => 'AdminBoards/boards',
            'viewData' => $viewData,
        ];
    }

    public function boardform(array $vars): array
    {
        $board_id = $vars['param'] ?? '';
        $boardConfig = $board_id ? $this->adminBoardsService->getBoardsConfig($board_id) : [];
        $levelSelect = $this->adminBoardsHelper->getLevelSelectBox('formData');
        
        $viewData = [
            'title' => !empty($boardConfig) ? $boardConfig['board_name'].' 수정' : '게시판 생성',
            'content' => '',
            'config_domain' => $this->config_domain,
            'groupData' => $this->adminBoardsService->getBoardsGroup(),
            'categoryData' => $this->adminBoardsService->getCategoryData(),
            'boardCategory' => !empty($boardConfig) ? $this->adminBoardsService->getBoardsCategoryMapping($boardConfig['no']) : [],
            'levelData' => $this->membersService->getLevelData(),
            'skinData' => $this->adminBoardsHelper->getBoardSkinDir(),
            'boardConfig' => $boardConfig,
            'levelSelect' => $levelSelect,
        ];

        return [
            'viewPath' => 'AdminBoards/boardForm',
            'viewData' => $viewData,
        ];
    }

    public function boardUpdate(): array
    {
        $board_no = (int)($_POST['board_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다.',
            ]);
        }

        if ($board_no) {
            $board_id = $_POST['board_id'] ?? '';
            $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);

            if(empty($boardConfig)) {
                return CommonHelper::jsonResponse([
                    'result' => 'failure',
                    'message' => '게시판 정보가 없습니다.',
                ]);
            }
        }

        $numericFields = [
            'group_no', 'list_level', 'read_level', 'write_level', 'download_level', 'comment_level',
            'read_point', 'write_point', 'download_point', 'comment_point',
            'is_use_comment', 'board_list_type',
            'is_use_file', 'file_size_limit', 'use_separate_table'
        ];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($board_no && !empty($boardConfig)) {
            $this->adminBoardsService->updateBoardsConfig($board_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsConfig($data);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '처리하였습니다.',
        ]);
    }
}