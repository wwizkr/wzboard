<?php
//파일위치 src/Service/SettingService.php

namespace Web\PublicHtml\Service;

use  Web\PublicHtml\Model\BoardsModel;

class BoardsService
{
    protected $boardsModel;

    public function __construct(BoardsModel $boardsModel)
    {
        $this->boardsModel = $boardsModel;
    }
    
    // ---------------------------
    // 그룹 관리
    // ---------------------------
    public function getBoardsGroup($group_id='')
    {
        return $this->boardsModel->getBoardsGroup($group_id);
    }

    public function insertBoardsGroup($data)
    {
        return $this->boardsModel->insertBoardsGroup($data);
    }

    public function updateBoardsGroup($board_no, $data)
    {
        return $this->boardsModel->insertBoardsGroup($board_no, $data);
    }
    
    // ---------------------------
    // 카테고리 관리
    // ---------------------------
    public function getBoardsCategory()
    {
        return $this->boardsModel->getBoardsCategory();
    }
}