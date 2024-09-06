<?php
// 파일 위치: /src/Model/BoardModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

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
    
    /**
     * 게시글 목록을 조회하는 메소드
     * 
     * @param int $board_no 게시판 번호
     * @param int $currentPage 현재 페이지 번호
     * @param int $page_rows 페이지당 표시할 게시글 수
     * @param string $searchQuery 검색어
     * @param array $filters 검색할 필드 목록
     * @param array $sort 정렬 기준 (필드명과 정렬 방향)
     * @param array $additionalQueries 추가 검색 조건
     * @return array 조회된 게시글 목록
     */
    public function getArticleListData($board_no, $currentPage, $page_rows, $searchQuery, $filters = [], $sort = [], $additionalQueries = [])
    {
        $offset = ($currentPage - 1) * $page_rows;
        
        $where = [
            'cf_id' => ['i', $this->configDomain['cf_id']],
        ];
        if ($board_no) {
            $where['board_no'] = ['i', $board_no];
        }

        $addWhere = [];
        $bindValues = [];

        // 검색 쿼리와 필터 처리 ## 수정필요
        if (!empty($searchQuery) && !empty($filters)) {
            $searchConditions = [];
            foreach ($filters as $index => $field) {
                $searchConditions[] = "$field LIKE ?";
                $bindValues[] = "%$searchQuery%";
            }
            $addWhere[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        // additionalQueries 처리
        //error_log("Model additionalQueries:" . print_r($additionalQueries, true));
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'no DESC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];
        
        //error_log("Add Where:" . print_r($options, true));

        return $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
    }
    
    /**
     * 전체 게시글 수를 조회하는 메소드
     * 
     * @param int $board_no 게시판 번호
     * @param string $searchQuery 검색어
     * @param array $filters 검색할 필드 목록
     * @param array $additionalQueries 추가 검색 조건
     * @return int 전체 게시글 수
     */
    public function getTotalArticleCount($board_no, $searchQuery, $filters = [], $additionalQueries = [])
    {
        $where = [
            'cf_id' => ['i', $this->configDomain['cf_id']],
            'board_no' => ['i', $board_no]
        ];

        $addWhere = [];
        $bindValues = [];

        // 검색 쿼리와 필터 처리
        if (!empty($searchQuery) && !empty($filters)) {
            $searchConditions = [];
            foreach ($filters as $index => $field) {
                $searchConditions[] = "$field LIKE ?";
                $bindValues[] = "%$searchQuery%";
            }
            $addWhere[] = '(' . implode(' OR ', $searchConditions) . ')';
        }
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
        //error_log("Result:".print_r($result,true));
        return $result[0]['totalCount'] ?? 0;
    }


    /*
     * 게시글 작성, 수정
     *
     */
    public function writeBoardsUpdate($article_no, $boardId, $data)
    {
        $param = $data;
        $where = [];
        
        if ($article_no) {
            $where['no'] = ['i', $article_no];
            return $result = $this->db->sqlBindQuery('update','board_articles',$param,$where);
        } else {
            return $result = $this->db->sqlBindQuery('insert','board_articles',$param,$where);
        }
    }

    /*
     * 게시글 읽기
     *
     * @param string $group_no
     * @param int $article_no
     */
    public function getArticleDataByNo($board_no, $article_no)
    {
        $param = [];
        $where = [
            'board_no' => ['i', $board_no],
            'no' => ['i', $article_no],
        ];
        $options = [];

        $result = $this->db->sqlBindQuery('select', 'board_articles', $param, $where, $options);

        return $result[0];
    }
}