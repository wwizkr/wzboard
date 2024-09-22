<?php

namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Model\AdminBoardsModel;

class AdminBoardsService
{
    protected $container;
    protected $adminBoardsModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->adminBoardsModel = $this->container->get('AdminBoardsModel');
    }
    
    // ---------------------------
    // 그룹 관리
    // ---------------------------
    public function getBoardsGroup($group_id='')
    {
        return $this->adminBoardsModel->getBoardsGroup($group_id);
    }

    public function insertBoardsGroup($data)
    {
        return $this->adminBoardsModel->insertBoardsGroup($data);
    }

    public function updateBoardsGroup($group_no, $data)
    {
        return $this->adminBoardsModel->updateBoardsGroup($group_no, $data);
    }
    
    // ---------------------------
    // 전체 카테고리 OR 개별 카테고리 정보
    // ---------------------------
    public function getCategoryData($category_no='')
    {
        return $this->adminBoardsModel->getCategoryData($category_no);
    }

    public function insertBoardsCategory($data)
    {
        $result = $this->adminBoardsModel->checkBoardsCategoryName($data['category_name']);
        if($result === false) {
            $jsonData = [
                'result' => 'failer',
                'message' => '카테고리명이 중복되었습니다.'
            ];
            return $jsonData;
        }

        return $this->adminBoardsModel->insertBoardsCategory($data);
    }

    public function updateBoardsCategory($category_no, $data)
    {
        return $this->adminBoardsModel->updateBoardsCategory($category_no, $data);
    }

    // ---------------------------
    // 게시판 관리
    // ---------------------------

    // 생성된 게시판 목록
    public function getBoardsConfig($board_id='')
    {
        return $this->adminBoardsModel->getBoardsConfig($board_id);
    }

    public function getBoardsConfigByNo(int $board_no)
    {
        return $this->adminBoardsModel->getBoardsConfigByNo($board_no);
    }

    public function insertBoardsConfig($data)
    {
        $category = explode("-",$data['categories'][1]) ?? null;
        unset($data['categories']); //$data에서 categories 제거

        $insert = $this->adminBoardsModel->insertBoardsConfig($data);
        if($insert['ins_id']) {
            if(!empty($category)) {
                $this->adminBoardsModel->updateBoardsCategoryMapping($insert['ins_id'], $data['board_id'][1], $category);
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

        $update = $this->adminBoardsModel->updateBoardsConfig($board_no, $data);

        if(!empty($category)) {
            $this->adminBoardsModel->updateBoardsCategoryMapping($board_no, $data['board_id'][1], $category);
        }

        return $update;
    }

    public function getBoardsCategoryMapping($board_no)
    {
        return $this->adminBoardsModel->getBoardsCategoryMapping($board_no);
    }
}