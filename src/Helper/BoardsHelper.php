<?php
// 파일 위치: /src/Helper/BoardsHelper.php
namespace Web\PublicHtml\Helper;

use Web\Admin\Service\AdminBoardsService;
use Web\Admin\Helper\AdminBoardsHelper;
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

    public function getCategoryData($category_no = null)
    {
        return $this->adminBoardsService->getCategoryData($category_no);
    }

    public function getBoardsConfig($boardId = null)
    {
        return $this->adminBoardsService->getBoardsConfig($boardId);
    }

    public function getSkinData()
    {
        return AdminBoardsHelper::getBoardSkinDir();
    }

    public function getBoardsCategoryMapping($board_no)
    {
        return $this->adminBoardsService->getBoardsCategoryMapping($board_no);
    }
}