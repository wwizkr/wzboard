<?php
// 파일 위치: /src/Helper/BoardsHelper.php

namespace Web\PublicHtml\Helper;

use Web\Admin\Service\AdminBoardsService;
use Web\Admin\Helper\BoardsHelper as AdminBoardsHelper;

class BoardsHelper
{
    protected $boardsService;
    protected $membersService;

    public function __construct(AdminBoardsService $boardsService)
    {
        $this->boardsService = $boardsService;
    }

    public function getGroupData()
    {
        return $this->boardsService->getBoardsGroup(null);
    }

    public function getCategoryData()
    {
        return $this->boardsService->getBoardsCategory(null);
    }

    public function getBoardsConfig($boardId = null)
    {
        return $this->boardsService->getBoardsConfig($boardId);
    }

    public function getSkinData()
    {
        return AdminBoardsHelper::getBoardSkinDir();
    }
}