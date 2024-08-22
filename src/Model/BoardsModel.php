<?php
// 파일 위치: /src/Model/BoardModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;

class BoardsModel
{
    use DatabaseHelperTrait;

    protected $db;
    protected $configDomain;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->configDomain = $container->get('config_domain');
    }

    /*
     * 게시판 그룹 목록 또는 특정 그룹을 가져옴
     * @param int $cf_id
     * @return array
     */
    public function getBoardsGroup($group_id='')
    {
        $param = [];
        $where = [];
        if ($group_id) {
            $where['group_id'] = ['s', $group_id];
        }
        $options = [
            'order' => 'no DESC',
        ];
        $result = $this->db->sqlBindQuery('select', $this->getTableName('board_groups'), $param, $where, $options);

        if($group_id) {
            $groupData = $result[0];
        } else {
            $groupData = $result;
        }

        return $groupData;
    }
    
    /*
     * 게시판 그룹 목록 또는 특정 그룹을 가져옴
     * @param int $cf_id
     * @return array
     */
    public function insertBoardsGroup($data)
    {
        $param = $data;
        $where = [];
        $options = [];
        $result = $this->db->sqlBindQuery('insert', $this->getTableName('board_groups'), $param, $where, $options);

        return $result;
    }
    
    /*
     * 게시판 그룹 정보 업데이트
     * @param int $group_no, $data
     * @return array
     */
    public function updateBoardsGroup($group_no, $data)
    {
        $param = $data;
        $where['no'] = ['i',$group_no];
        $options = [];
        $result = $this->db->sqlBindQuery('update', $this->getTableName('board_groups'), $param, $where, $options);

        return $result;
    }

    /*
     * 게시판 카테고리 목록을 가져옴
     * @return array
     */
    public function getBoardsCategory($category_no=null)
    {
        $param = [];
        $where = [];
        if ($category_no) {
            $where['no'] = ['i', $category_no];
        }
        $options = [
            'order' => 'no DESC',
        ];

        $result = $this->db->sqlBindQuery('select', $this->getTableName('board_categories'), $param, $where, $options);

        if($category_no) {
            $categoryData = $result[0] ?? null;
        } else {
            $categoryData = $result;
        }

        return $categoryData;
    }

    /*
     * 게시판 카테고리 등록
     * @param array $data
     * @return boolean
     */
    public function checkBoardsCategoryName($category_name)
    {
        // 카테고리명 중복확인
        $param = [];
        $where['category_name'] = ['s',$category_name];
        $options = ['field'=>'count(*) as cnt'];
        $result = $this->db->sqlBindQuery('select', $this->getTableName('board_categories'), $param, $where, $options);
        if($result[0]['cnt'] > 0) {
            return false;
        }
        return true;
    }

    public function insertBoardsCategory($data)
    {
        $param = $data;
        $where = [];
        $options = [];
        $result = $this->db->sqlBindQuery('insert', $this->getTableName('board_categories'), $param, $where, $options);

        return $result;
    }
    
    /*
     * 게시판 카테고리 정보 업데이트
     * @param int $category_no, array $data
     * @return array
     */
    public function updateBoardsCategory($category_no, $data)
    {
        $param = $data;
        $where['no'] = ['i',$category_no];
        $options = [];
        $result = $this->db->sqlBindQuery('update', $this->getTableName('board_categories'), $param, $where, $options);

    }

    /*
     * 생성된 게시판 목록을 가져옴
     * @return array
     */
    public function getBoardsList($board_id='')
    {
        $param = [];
        $where = [];
        if ($board_id) {
            $where[$this->getTableName('board_configs') . '.board_id'] = ['s', $board_id];
        }
        
        $options = [
            'field' => $this->getTableName('board_configs') . '.*, ' . 
                       $this->getTableName('board_groups') . '.group_name, ' . 
                       $this->getTableName('board_groups') . '.group_id',
            'joins' => [
                [
                    'type' => 'LEFT',
                    'table' => $this->getTableName('board_groups'),
                    'on' => $this->getTableName('board_configs') . '.group_no = ' . $this->getTableName('board_groups') . '.no'
                ]
            ],
            'order' => $this->getTableName('board_groups') . '.order_num ASC, ' . 
                       $this->getTableName('board_configs') . '.board_name ASC'
        ];

        $result = $this->db->sqlBindQuery('select', $this->getTableName('board_configs'), $param, $where, $options);

        if($board_id) {
            $boardData = $result[0] ?? null;
        } else {
            $boardData = $result;
        }

        return $boardData;
    }

    /*
     * 게시판 설정 등록, 게시판 생성
     * @param array $data
     * @return boolean
     */
    public function insertBoardsConfig($data)
    {
        $param = $data;
        $where = [];
        $options = [];
        $result = $this->db->sqlBindQuery('insert', $this->getTableName('board_configs'), $param, $where, $options);

        return $result;
    }
    
    /*
     * 게시판 그룹 정보 업데이트
     * @param int $group_no, $data
     * @return boolean
     */
    public function updateBoardsConfig($board_no, $data)
    {
        $param = $data;
        $where['no'] = ['i',$board_no];
        $options = [];
        $result = $this->db->sqlBindQuery('update', $this->getTableName('board_configs'), $param, $where, $options);

        return $result;
    }
}