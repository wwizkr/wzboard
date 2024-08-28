<?php
//파일위치 src/Admin/Service/AdminBoardsService.php

namespace Web\Admin\Service;

use  Web\Admin\Model\AdminBoardsModel;

class AdminBoardsService
{
    protected $boardsModel;

    public function __construct(AdminBoardsModel $boardsModel)
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

    public function updateBoardsGroup($group_no, $data)
    {
        return $this->boardsModel->updateBoardsGroup($group_no, $data);
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

    // 생성된 게시판 목록
    public function getBoardsConfig($board_id='')
    {
        return $this->boardsModel->getBoardsConfig($board_id);
    }

    public function insertBoardsConfig($data)
    {
        $category = explode("-",$data['categories'][1]) ?? null;
        unset($data['categories']); //$data에서 categories 제거

        $insert = $this->boardsModel->insertBoardsConfig($data);
        if($insert['ins_id']) {
            if(!empty($category)) {
                $this->boardsModel->updateBoardsCategoryMapping($insert['ins_id'], $data['board_id'][1], $category);
            }
            return $insert['ins_id'];
        } else {
            return false;
        }
    }

    public function updateBoardsConfig($board_no, $data)
    {
        $category = explode("-",$data['categories'][1]) ?? null;
        unset($data['categories']); //$data에서 categories 제거

        $update = $this->boardsModel->updateBoardsConfig($board_no, $data);

        if(!empty($category)) {
            $this->boardsModel->updateBoardsCategoryMapping($board_no, $data['board_id'][1], $category);
        }

        return $update;
    }

    public function getBoardsCategoryMapping($board_no)
    {
        return $this->boardsModel->getBoardsCategoryMapping($board_no);
    }
}