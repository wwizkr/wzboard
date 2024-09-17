<?php
namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;

class BoardadminController
{
    protected DependencyContainer $container;
    protected $sessionManager;
    protected $boardsHelper;
    protected $membersHelper;
    protected $adminBoardsService;
    protected $configDomain;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $this->container->get('SessionManager');
        $this->boardsHelper = $this->container->get('BoardsHelper');
        $this->membersHelper = $this->container->get('MembersHelper');
        $this->adminBoardsService = $this->container->get('AdminBoardsService');
        $this->configDomain = $this->container->get('config_domain');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
    }

    public function group(): array
    {
        return [
            'AdminBoards/group',
            [
                'title' => '게시판 그룹 관리',
                'content' => '',
                'config_domain' => $this->configDomain,
                'groupData' => $this->boardsHelper->getGroupData(),
                'levelData' => $this->membersHelper->getLevelData(),
            ]
        ];
    }

    public function groupUpdate(): array
    {
        $action = $_POST['action'] ?? null;
        $group_no = (int)($_POST['group_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return $this->jsonFailureResponse('입력정보가 비어 있습니다.');
        }

        $numericFields = ['allow_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            unset($data['group_id']);
            $this->adminBoardsService->updateBoardsGroup($group_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsGroup($data);
        }

        return $this->jsonSuccessResponse('처리하였습니다.');
    }

    public function category(): array
    {
        return [
            'AdminBoards/category',
            [
                'title' => '게시판 카테고리 관리',
                'content' => '',
                'config_domain' => $this->configDomain,
                'categoryData' => $this->boardsHelper->getCategoryData(),
                'levelData' => $this->membersHelper->getLevelData(),
            ]
        ];
    }

    public function categoryUpdate(): array
    {
        $action = $_POST['action'] ?? null;
        $category_no = (int)($_POST['category_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return $this->jsonFailureResponse('입력정보가 비어 있습니다.');
        }

        $numericFields = ['allow_level', 'order_num'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($action === 'update') {
            $this->adminBoardsService->updateBoardsCategory($category_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsCategory($data);
        }

        return $this->jsonSuccessResponse('처리하였습니다.');
    }

    public function boards(): array
    {
        return [
            'AdminBoards/boards',
            [
                'title' => '게시판 관리',
                'content' => '',
                'config_domain' => $this->configDomain,
                'boardsConfig' => $this->boardsHelper->getBoardsConfig(),
                'levelData' => $this->membersHelper->getLevelData(),
            ]
        ];
    }

    public function boardform(array $vars): array
    {
        $board_id = $vars['param'] ?? '';
        $boardConfig = $board_id ? $this->boardsHelper->getBoardsConfig($board_id) : [];

        return [
            'AdminBoards/boardForm',
            [
                'title' => !empty($boardConfig) ? $boardConfig['board_name'].' 수정' : '게시판 생성',
                'content' => '',
                'config_domain' => $this->configDomain,
                'groupData' => $this->boardsHelper->getGroupData(),
                'categoryData' => $this->boardsHelper->getCategoryData(),
                'boardCategory' => !empty($boardConfig) ? $this->boardsHelper->getBoardsCategoryMapping($boardConfig['no']) : [],
                'levelData' => $this->membersHelper->getLevelData(),
                'skinData' => $this->boardsHelper->getSkinData(),
                'boardConfig' => $boardConfig,
            ]
        ];
    }

    public function boardUpdate(): array
    {
        $board_no = (int)($_POST['board_no'] ?? 0);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return $this->jsonFailureResponse('입력정보가 비어 있습니다.');
        }

        if ($board_no) {
            $board_id = $_POST['board_id'] ?? '';
            $boardConfig = $this->boardsHelper->getBoardsConfig($board_id);

            if(empty($boardConfig)) {
                return $this->jsonFailureResponse('게시판 정보가 없습니다.');
            }
        }

        $numericFields = [
            'group_no', 'read_level', 'write_level', 'download_level',
            'is_use_file', 'file_size_limit', 'use_separate_table'
        ];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        if ($board_no && !empty($boardConfig)) {
            $this->adminBoardsService->updateBoardsConfig($board_no, $data);
        } else {
            $this->adminBoardsService->insertBoardsConfig($data);
        }

        return $this->jsonSuccessResponse('처리하였습니다.');
    }

    private function jsonSuccessResponse(string $message): array
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => $message
        ]);
    }

    private function jsonFailureResponse(string $message): array
    {
        return CommonHelper::jsonResponse([
            'result' => 'failure',
            'message' => $message
        ]);
    }
}