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
        return $this->boardsModel->updateBoardsGroup($board_no, $data);
    }
    
    // ---------------------------
    // 카테고리 관리
    // ---------------------------
    public function getBoardsCategory($category_no)
    {
        return $this->boardsModel->getBoardsCategory($category_no);
    }

    public function insertBoardsCategory($data)
    {
        $result = $this->boardsModel->checkBoardsCategoryName($data['category_name']);
        if($result === false) {
            $jsonData = [
                'result' => 'failer',
                'message' => '카테고리명이 중복되었습니다.'
            ];
            return $jsonData;
        }

        return $this->boardsModel->insertBoardsCategory($data);
    }

    public function updateBoardsCategory($category_no, $data, $categoryData)
    {
        return $this->boardsModel->updateBoardsGroup($board_no, $data);
    }

    // ---------------------------
    // 게시판 관리
    // ---------------------------
    public function getBoardsList($board_id='')
    {
        return $this->boardsModel->getBoardsList($board_id);
    }
}