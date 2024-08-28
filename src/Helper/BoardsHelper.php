<?php
// 파일 위치: /src/Helper/BoardsHelper.php
namespace Web\PublicHtml\Helper;

use Web\Admin\Service\AdminBoardsService;
use Web\Admin\Helper\BoardsHelper as AdminBoardsHelper;
use Web\PublicHtml\Service\BoardsService;

class BoardsHelper
{
    protected $adminBoardsService;
    protected $boardsService;

    public function __construct(AdminBoardsService $adminBoardsService)
    {
        $this->adminBoardsService = $adminBoardsService;
    }

    public function setBoardsService(BoardsService $boardsService)
    {
        $this->boardsService = $boardsService;
    }

    public function getGroupData()
    {
        return $this->adminBoardsService->getBoardsGroup(null);
    }

    public function getCategoryData()
    {
        return $this->adminBoardsService->getBoardsCategory(null);
    }

    public function getBoardsConfig($boardId = null)
    {
        return $this->adminBoardsService->getBoardsConfig($boardId);
    }

    public function getSkinData()
    {
        return AdminBoardsHelper::getBoardSkinDir();
    }
}