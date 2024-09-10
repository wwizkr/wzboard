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
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'no DESC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        error_log("add Where::".print_r($addWhere, true));

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
        return $result[0]['totalCount'] ?? 0;
    }


    /*
     * 게시글 작성, 수정
     *
     */
    public function writeBoardsUpdate($article_no, $board_id, $data)
    {
        $param = $data;
        $where = [];
        
        if ($article_no) {
            unset($param);
            $param['category_no'] = $data['category_no'];
            $param['title'] = $data['title'];
            $param['slug'] = $data['slug'];
            $param['content'] = $data['content'];
            $where['no'] = ['i', $article_no];
            $result = $this->db->sqlBindQuery('update','board_articles',$param,$where);
            if($result['result'] === 'success') {
                return ['result' => 'success', 'message' => '게시글을 수정하였습니다.'];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
        } else {
            $result = $this->db->sqlBindQuery('insert','board_articles',$param,$where);
            if($result['ins_id']) {
                return ['result' => 'success', 'message' => '게시글을 등록하였습니다.'];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
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



    /*
     * 댓글 작성, 수정
     *
     */
    public function commentWriteUpdate($comment_no, $board_id, $data)
    {
        $param = $data;
        $where = [];
        
        if ($comment_no) {
            unset($param);
            $param['content'] = $data['content'];
            $where['no'] = ['i', $comment_no];
            $result = $this->db->sqlBindQuery('update', 'board_comments',$param,$where);
            if($result['result'] === 'success') {
                return ['result' => 'success', 'message' => '댓글을 수정하였습니다.', 'action' => 'modify'];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
        } else {
            $result = $this->db->sqlBindQuery('insert', 'board_comments',$param,$where);
            if($result['ins_id']) {
                $insert_path = $param['path'][1];
                $new_path = !$insert_path ? (string)$result['ins_id'] : $insert_path.'/'.$result['ins_id'];
                $action = !$insert_path ? 'insert' : 'reply';
                
                //path update
                $update_param['path'] = ['s', $new_path];
                $update_where['no'] = ['i', $result['ins_id']];
                $update = $this->db->sqlBindQuery('update', 'board_comments', $update_param, $update_where);
                
                return ['result' => 'success', 'message' => '댓글을 등록하였습니다.', 'action' => $action];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
        }
    }

    /**
     * 댓글 데이터를 가져오는 메서드
     * @param int $board_no 게시판 번호
     * @param int|null $article_no 게시글 번호 (없을 경우 전체 댓글 가져오기)
     * @param int|null $comment_no 특정 댓글 번호 (특정 댓글만 가져오기)
     * @param int $offset 페이징을 위한 시작 위치
     * @param int $perPage 페이지당 댓글 수
     * @return array 댓글 목록 또는 개별 댓글 데이터
     */
    public function getComments(?int $board_no = null, ?int $article_no = null, ?int $comment_no = null, int $offset = 0, int $perPage = 10): array
    {
        // WHERE 조건 설정
        $where = [];

        if ($board_no !== null) {
            $where['board_no'] = ['i', $board_no, 'AND'];
        }

        if ($article_no !== null) {
            $where['article_no'] = ['i', $article_no, 'AND'];
        }

        if ($comment_no !== null) {
            $where['no'] = ['i', $comment_no, 'AND'];
            $limit = '1';
        } else {
            $limit = "$offset, $perPage";
        }

        // SQL 쿼리 실행
        $result = $this->db->sqlBindQuery(
            'select',                 // 쿼리 모드
            'board_comments',         // 테이블 이름
            [],                       // 파라미터 없음
            $where,                   // WHERE 조건
            [                         // 옵션
                'field' => '*',
                'order' => 'path ASC, created_at DESC',
                'limit' => $limit
            ]
        );

        // 쿼리 결과 처리
        if (is_array($result)) {
            // 쿼리가 성공적으로 실행된 경우
            return ['result' => 'success', 'data' => $result];
        } else {
            // 쿼리가 실패한 경우
            return ['result' => 'failure', 'message' => '댓글 데이터를 가져오는 데 실패하였습니다.'];
        }
    }
}