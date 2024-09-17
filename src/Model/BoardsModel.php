<?php
// 파일 위치: /src/Model/BoardModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class BoardsModel
{
    use DatabaseHelperTrait;

    protected $db;
    protected array $config_domain;

    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->config_domain = $container->get('ConfigHelper')->getConfig('config_domain');
    }
    
    public function getArticleListData(int $board_no, int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {
        $offset = ($currentPage - 1) * $page_rows;
        
        $where = ['cf_id' => ['i', $this->config_domain['cf_id']]];
        if ($board_no) {
            $where['board_no'] = ['i', $board_no];
        }

        [$addWhere, $bindValues] = $this->buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);
        
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'no DESC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
    }
    
    public function getTotalArticleCount(int $board_no, ?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        $where = [
            'cf_id' => ['i', $this->config_domain['cf_id']],
            'board_no' => ['i', $board_no]
        ];

        [$addWhere, $bindValues] = $this->buildSearchConditions($searchQuery, $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
        return (int)($result[0]['totalCount'] ?? 0);
    }

    public function writeBoardsUpdate(?int $article_no, string $board_id, array $data): array
    {
        if ($article_no) {
            $param = [
                'category_no' => $data['category_no'],
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content']
            ];
            $where = ['no' => ['i', $article_no]];
            $result = $this->db->sqlBindQuery('update', 'board_articles', $param, $where);
            return $result['result'] === 'success' 
                ? ['result' => 'success', 'message' => '게시글을 수정하였습니다.']
                : ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
        } else {
            $result = $this->db->sqlBindQuery('insert', 'board_articles', $data, []);
            return $result['ins_id']
                ? ['result' => 'success', 'message' => '게시글을 등록하였습니다.']
                : ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
        }
    }

    public function getArticleDataByNo(int $board_no, int $article_no): ?array
    {
        $where = [
            'board_no' => ['i', $board_no],
            'no' => ['i', $article_no],
        ];
        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, []);
        return $result[0] ?? null;
    }

    public function articleViewCountUpdate(array $articleData): void
    {
        $articleNo = (int)$articleData['no'];
        $viewCount = (int)$articleData['view_count'] + 1;

        if ($articleNo <= 0) {
            return;
        }

        $tableName = $this->getTableName('board_articles');
        $sql = "UPDATE $tableName SET view_count = :viewCount WHERE no = :articleNo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':viewCount' => $viewCount, ':articleNo' => $articleNo]);
    }

    public function commentWriteUpdate(?int $comment_no, string $board_id, array $data): array
    {
        if ($comment_no) {
            $param = ['content' => $data['content']];
            $where = ['no' => ['i', $comment_no]];
            $result = $this->db->sqlBindQuery('update', 'board_comments', $param, $where);
            return $result['result'] === 'success'
                ? ['result' => 'success', 'message' => '댓글을 수정하였습니다.', 'action' => 'modify']
                : ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
        } else {
            $result = $this->db->sqlBindQuery('insert', 'board_comments', $data, []);
            if ($result['ins_id']) {
                $new_path = !empty($data['path']) ? $data['path'] . '/' . $result['ins_id'] : $result['ins_id'];
                $this->updateCommentPath($result['ins_id'], $new_path);
                return ['result' => 'success', 'message' => '댓글을 등록하였습니다.', 'action' => empty($data['path']) ? 'insert' : 'reply'];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
        }
    }

    public function getComments(?int $board_no = null, ?int $article_no = null, ?int $comment_no = null, int $offset = 0, int $perPage = 10): array
    {
        $where = [];
        if ($board_no !== null) $where['board_no'] = ['i', $board_no, 'AND'];
        if ($article_no !== null) $where['article_no'] = ['i', $article_no, 'AND'];
        if ($comment_no !== null) $where['no'] = ['i', $comment_no, 'AND'];

        $options = [
            'field' => '*',
            'order' => 'path ASC, created_at DESC',
            'limit' => $comment_no !== null ? '1' : "$offset, $perPage"
        ];

        $result = $this->db->sqlBindQuery('select', 'board_comments', [], $where, $options);

        return is_array($result)
            ? ['result' => 'success', 'data' => $result]
            : ['result' => 'failure', 'message' => '댓글 데이터를 가져오는 데 실패하였습니다.'];
    }

    private function buildSearchConditions(?string $searchQuery, array $filters): array
    {
        $addWhere = [];
        $bindValues = [];
        if (!empty($searchQuery) && !empty($filters)) {
            $searchConditions = [];
            foreach ($filters as $field) {
                $searchConditions[] = "$field LIKE ?";
                $bindValues[] = "%$searchQuery%";
            }
            $addWhere[] = '(' . implode(' OR ', $searchConditions) . ')';
        }
        return [$addWhere, $bindValues];
    }

    private function updateCommentPath(int $commentId, string $newPath): void
    {
        $this->db->sqlBindQuery('update', 'board_comments', ['path' => ['s', $newPath]], ['no' => ['i', $commentId]]);
    }
}