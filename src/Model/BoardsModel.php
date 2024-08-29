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
     * 전체 게시글 수
     */
    public function getArticleListData($currentPage, $page_rows, $searchQuery, $filters = [], $sort = [])
    {
        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [];
        $where['cf_id'] = ['i', $this->configDomain['cf_id']];

        if (!empty($searchQuery)) {
            //$where 추가
        }

        foreach ($filters as $key => $value) {
            $where[$key] = ['=', $value, 'AND'];
        }

        // ORDER BY 조건 생성
        $order = '';
        if (!empty($sort)) {
            $order = implode(', ', array_map(function ($key, $value) {
                return "{$key} {$value}";
            }, array_keys($sort), $sort));
        } else {
            $order = 'no DESC'; // 기본 정렬
        }

        // LIMIT 조건 생성
        $limit = "$offset, $page_rows";

        // SQL 실행
        $options = [
            'order' => $order,
            'limit' => $limit
        ];

        return $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
    }
    
    /*
     * 전체 게시글 카운트
     */
    public function getTotalArticleCount($searchQuery, $filters = [])
    {
        // WHERE 조건 생성
        $where = [];
        $where['cf_id'] = ['i', $this->configDomain['cf_id']];

        if (!empty($searchQuery)) {
        }

        foreach ($filters as $key => $value) {
            $where[$key] = ['=', $value, 'AND'];
        }

        // SQL 실행
        $options = [
            'field' => 'COUNT(*) AS totalCount'
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);

        return $result[0]['totalCount'] ?? 0;
    }


    /*
     * 게시글 작성, 수정
     *
     */
    public function writeBoardsUpdate($boardId, $data)
    {
        $param = $data;
        $where = [];

        return $result = $this->db->sqlBindQuery('insert','board_articles',$param,$where);
    }
}