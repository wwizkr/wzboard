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
     * 게시판 그룹 목록 또는 특정 그룹을 가져옴
     * @param int $cf_id
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
    public function getBoardsCategory()
    {
        $param = [];
        $where = [];
        $options = [
            'order' => 'no DESC',
        ];
        $result = $this->db->sqlBindQuery('select', $this->getTableName('board_categories'), $param, $where, $options);

        return $result;
    }
}