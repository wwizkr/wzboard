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
    private function processAdditionalQueries($additionalQueries, &$addWhere, &$bindValues)
    {
        foreach ($additionalQueries as $index => $query) {
            $field = $query[0];
            $value = $query[1];
            
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $paramName = "additional_{$index}_{$i}";
                    $placeholders[] = ":{$paramName}";
                    $bindValues[$paramName] = $v;
                }
                $addWhere[] = "$field IN (" . implode(',', $placeholders) . ")";
            } else {
                $paramName = "additional_{$index}";
                $addWhere[] = "$field = :{$paramName}";
                $bindValues[$paramName] = $value;
            }
        }
    }
    */

    private function processAdditionalQueries($additionalQueries, &$addWhere, &$bindValues)
    {
        foreach ($additionalQueries as $index => $query) {
            $field = $query[0];
            $value = $query[1];
            
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $placeholders[] = "?";
                    $bindValues[] = $v;
                }
                $addWhere[] = "$field IN (" . implode(',', $placeholders) . ")";
            } else {
                $addWhere[] = "$field = ?";
                $bindValues[] = $value;
            }
        }
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

        // 검색 쿼리와 필터 처리
        if (!empty($searchQuery) && !empty($filters)) {
            $searchConditions = [];
            foreach ($filters as $index => $field) {
                $paramName = "search_$index";
                $searchConditions[] = "$field LIKE :$paramName";
                $bindValues[$paramName] = "%$searchQuery%";
            }
            $addWhere[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        // additionalQueries 처리
        $this->processAdditionalQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'no DESC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

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
                $paramName = "search_$index";
                $searchConditions[] = "$field LIKE :$paramName";
                $bindValues[$paramName] = "%$searchQuery%";
            }
            $addWhere[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        // additionalQueries 처리
        $this->processAdditionalQueries($additionalQueries, $addWhere, $bindValues);

        error_log("Bind Values:".print_r($bindValues,true));

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
        error_log("Result:".print_r($result,true));
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