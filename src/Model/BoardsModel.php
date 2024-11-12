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

        //[$addWhere, $bindValues] = $this->buildSearchConditions($searchQuery ?? '', $filters);
        [$addWhere, $bindValues] = CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
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

        //[$addWhere, $bindValues] = $this->buildSearchConditions($searchQuery, $filters);
        [$addWhere, $bindValues] = CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', [], $where, $options);
        return (int)($result[0]['totalCount'] ?? 0);
    }

    /*
     * 이전글, 다음글
     */
    public function getAdjacentData(int $board_no, int $article_no, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {
        $where = ['cf_id' => ['i', $this->config_domain['cf_id']]];
        if ($board_no) {
            $where['board_no'] = ['i', $board_no];
        }

        // 이전글
        $prevWhere = $where;
        $prevWhere['no'] = ['i', $article_no, 'and', '<'];

        // 다음글
        $nextWhere = $where;
        $nextWhere['no'] = ['i', $article_no, 'and', '>'];

        [$addWhere, $bindValues] = CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);

        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $baseOrder = isset($params['sort']['order']) && strtolower($params['sort']['order']) == 'asc' ? "DESC" : "ASC";
        $prevOrder = $baseOrder == "ASC" ? "DESC" : "ASC";
        $nextOrder = $baseOrder;

        $prevOptions = [
            'order' => !empty($sort) ? "{$sort['field']} {$prevOrder}" : "no {$prevOrder}",
            'limit' => "1",
            'addWhere' => !empty($addWhere) ? implode(' AND ', $addWhere) : '',
            'values' => $bindValues
        ];

        $nextOptions = [
            'order' => !empty($sort) ? "{$sort['field']} {$nextOrder}" : "no {$nextOrder}",
            'limit' => "1",
            'addWhere' => !empty($addWhere) ? implode(' AND ', $addWhere) : '',
            'values' => $bindValues
        ];

        $prevData = $this->db->sqlBindQuery('select', 'board_articles', [], $prevWhere, $prevOptions);
        $nextData = $this->db->sqlBindQuery('select', 'board_articles', [], $nextWhere, $nextOptions);

        $prev = isset($prevData[0]) && $prevData[0] ? $prevData[0] : null;
        $next = isset($nextData[0]) && $nextData[0] ? $nextData[0] : null;

        return [
            'prevData' => $prev,
            'nextData' => $next
        ];
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
                ? [
                    'result' => 'success',
                    'message' => '게시글을 수정하였습니다.',
                    'view' => '/board/'.$board_id.'/view/'.$article_no.'/'.$data['slug'][1],
                    'data' => ['articleNo' => $article_no, 'slug' => $data['slug'][1]]
                  ]
                : [
                    'result' => 'failure',
                    'message' => '오류가 발생하였습니다.'
                  ];
        } else {
            $result = $this->db->sqlBindQuery('insert', 'board_articles', $data, []);
            return $result['ins_id']
                ? [
                    'result' => 'success',
                    'message' => '게시글을 등록하였습니다.',
                    'view' => '/board/'.$board_id.'/view/'.$result['ins_id'].'/'.$data['slug'][1],
                    'data' => ['articleNo' => $result['ins_id'], 'slug' => $data['slug'][1]]
                  ]
                : [
                    'result' => 'failure',
                    'message' => '오류가 발생하였습니다.'
                  ];
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

    public function getArticleFileData($board_no, $article_no)
    {
        $where = [
            'board_no' => ['i', $board_no],
            'article_no' => ['i', $article_no],
        ];
        $result = $this->db->sqlBindQuery('select', 'board_attachments', [], $where, []);

        return $result;
    }

    public function getArticleFileDataByNo($board_no, $article_no, $file_no)
    {
        $where = [
            'board_no' => ['i', $board_no],
            'article_no' => ['i', $article_no],
            'no' => ['i', $file_no],
        ];
        $result = $this->db->sqlBindQuery('select', 'board_attachments', [], $where, []);

        return $result[0] ?? [];
    }

    public function getArticleFileDataDeleteByNo($board_no, $article_no, $file_no)
    {
        $where = [
            'board_no' => ['i', $board_no],
            'article_no' => ['i', $article_no],
            'no' => ['i', $file_no],
        ];
        $result = $this->db->sqlBindQuery('delete', 'board_attachments', [], $where, []);

        return $result[0] ?? [];
    }

    public function insertArticleFileData($param)
    {
        return $this->db->sqlBindQuery('insert', 'board_attachments', $param);
    }

    public function getArticleLinkData($board_no, $article_no)
    {
        $where = [
            'board_no' => ['i', $board_no],
            'article_no' => ['i', $article_no],
        ];
        $result = $this->db->sqlBindQuery('select', 'board_links', [], $where, []);

        return $result;
    }

    public function updateArticleLinkData($board_no, $article_no, $link)
    {
        $url = $link['url'];
        $no = $link['no'];
        
        if ($no) {
            $param['link'] = ['s', $url];
            $where = [
                'board_no' => ['i', $board_no],
                'article_no' => ['i', $article_no],
                'no' => ['i', $no],
            ];
            
            return $this->db->sqlBindQuery('update', 'board_links', $param, $where);
        } else {
            $param = [
                'board_no' => ['i', $board_no],
                'article_no' => ['i', $article_no],
                'link' => ['s', $url],
            ];

            return $this->db->sqlBindQuery('insert', 'board_links', $param);
        }
    }

    public function articleViewCountUpdate(array $articleData): void
    {
        $article_no = (int)$articleData['no'];
        $viewCount = (int)$articleData['view_count'] + 1;

        if ($article_no <= 0) {
            return;
        }

        $tableName = $this->getTableName('board_articles');
        $sql = "UPDATE $tableName SET view_count = :viewCount WHERE no = :articleNo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':viewCount' => $viewCount, ':articleNo' => $article_no]);
    }

    public function commentWriteUpdate(?int $comment_no, string $board_id, array $data): array
    {
        if ($comment_no) {
            $param = ['content' => $data['content']];
            $where = ['no' => ['i', $comment_no]];
            $result = $this->db->sqlBindQuery('update', 'board_comments', $param, $where);

            if ($result['result'] === 'success') {
                $commentData = $this->getCommentData($comment_no);
                return [
                    'result' => 'success', 
                    'message' => '댓글을 수정하였습니다.', 
                    'action' => 'modify',
                    'comment' => $commentData
                ];
            } else {
                return ['result' => 'failure', 'message' => '오류가 발생하였습니다.'];
            }
        } else {
            $result = $this->db->sqlBindQuery('insert', 'board_comments', $data, []);
            
            if ($result['ins_id']) {
                $new_path = !empty($data['path']) && $data['path'][1] ? $data['path'][1] . '/' . $result['ins_id'] : $result['ins_id'];
                $this->updateCommentPath($result['ins_id'], $new_path);
                
                // 게시글 댓글 수 업데이트
                $article_no = is_array($data['article_no']) ? $data['article_no'][1] : $data['article_no'];
                $tableName = $this->getTableName('board_articles');
                $sql = "UPDATE $tableName SET comment_count = comment_count + 1 WHERE no = :articleNo";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':articleNo' => $article_no]);

                $commentData = $this->getCommentData($result['ins_id']);
                return [
                    'result' => 'success', 
                    'message' => '댓글을 등록하였습니다.', 
                    'action' => empty($data['path'][1]) ? 'insert' : 'reply',
                    'comment' => $commentData
                ];
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

        return is_array($result) && !empty($result)
            ? ['result' => 'success', 'data' => $result]
            : ['result' => 'failure', 'message' => '댓글 데이터를 가져오는 데 실패하였습니다.'];
    }

    public function getCommentData($commentNo): array
    {
        $param = [];
        $where['no'] = ['i', $commentNo];
        $result = $this->db->sqlBindQuery('select', 'board_comments', $param, $where);

        $comment = $result[0] ?? [];

        return $comment;
    }

    private function updateCommentPath(int $commentId, string $newPath): void
    {
        $this->db->sqlBindQuery('update', 'board_comments', ['path' => ['s', $newPath]], ['no' => ['i', $commentId]]);
    }

    public function checkArticleMemberPoint(array $data)
    {
        $param = [];
        $where = $data;
        $options = [
            'field' => 'COUNT(*) AS cnt',
        ];

        $result = $this->db->sqlBindQuery('select', 'points', $param, $where, $options);

        return $result[0]['cnt'] > 0 ? true : false;
    }

    public function updateArticleMemberPoint($mb_id, $mb_point, $point, array $data)
    {
        if (!$mb_id) {
            return false;
        }

        $param = $data;
        $where = [];
        $options = [];
        $result = $this->db->sqlBindQuery('insert', 'points', $param, $where, $options);

        if ($result['ins_id'] > 0) {
            $update_point = $mb_point + $point;
            $mb_param['point'] = ['i', $update_point];
            $mb_where['mb_id'] = ['s', $mb_id];
            $updated = $this->db->sqlBindQuery('update', 'members', $mb_param, $mb_where);
            return true;
        } else {
            return false;
        }
    }

    public function processedLikeAction($mb_id, $table, $action, $no, $articleNo, $boardNo)
    {
        $data = [];

        /*
         * 기본 액션은 like, dislike
         * 기본 액션이 아닐 경우 테이블에 $action.'_count' 필드를 추가해 줍니다.
         */
        if ($action !== 'like' && $action !== 'dislike') {
            $field = $action.'_count';
            $this->db->checkedDbField($field, 'board_articles', 'INT NOT NULL DEFAULT 0', 'INDEX');
        }

        $reaction_table = 'board_'.$table.'_reactions';
        $update_table = 'board_'.$table;
        $reaction_field = $table === 'articles' ? 'article_no' : 'comment_no';
        $update_field = $action.'_count';
        $tableName = $this->getTableName($update_table);
        
        $param = [];
        $where['mb_id'] = ['s', $mb_id];
        $where['board_no'] = ['i', $boardNo];
        $where[$reaction_field] = ['i', $no];
        if ($table === 'articles') {
            $where['article_no'] = ['i', $articleNo];
        }
        
        $options = [
        ];

        $reaction = $this->db->sqlBindQuery('select', $reaction_table, $param, $where, $options);
        
        $mode = 'insert';
        if (isset($reaction[0])) {
            if ($reaction['0']['reaction_type'] !== $action) {
                $data = [
                    'result' => 'failure',
                    'message' => '',
                    'data' => [],
                ];
                return $data;
            }

            $mode = 'delete';
        }

        if ($mode == 'insert') {
            $insert_param = [
                'board_no' => ['i', $boardNo],
                $reaction_field => ['i', $no],
                'mb_id' => ['s', $mb_id],
                'reaction_type' => ['s', $action],
            ];
            
            if ($table === 'articles') {
                $insert_param['article_no'] = ['i', $articleNo];
            }

            $result = $this->db->sqlBindQuery('insert', $reaction_table, $insert_param);

            if ($result['ins_id']) {
                $sql = "UPDATE $tableName SET $update_field = $update_field + 1 WHERE no = :No";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':No' => $no]);

                $data = [
                    'result' => 'success',
                    'message' => 'insert',
                    'data' => [
                                'reaction' => $action,
                                'mode' => $mode,
                              ],
                ];
            } else {
                $data = [
                    'result' => 'failure',
                    'message' => '',
                    'data' => null,
                ];
            }
        }

        if ($mode == 'delete') {
            $delete_where = [
                $reaction_field => ['i', $no],
                'mb_id' => ['s', $mb_id],
            ];

            $result = $this->db->sqlBindQuery('delete', $reaction_table, [], $delete_where);
            $sql = "UPDATE $tableName SET $update_field = $update_field - 1 WHERE no = :No";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':No' => $no]);

            $data = [
                'result' => 'success',
                'message' => 'delete',
                'data' => [
                    'reaction' => $action,
                    'mode' => $mode,
                ],
            ];
        }

        return $data;
    }

    /**
     * 게시판 최신글을 가져오는 메소드
     * @pram int $cf_id 사이트 고유번호
     * @param int $board_no 게시판 아이디
     * @param int $limit 갯수
     * return array 목록
     */
    public function getLatestArticleList(int $cf_id, int $board_no, int $limit)
    {
        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        $where['board_no'] = ['i', $board_no];
        $options = [
            'order' => 'created_at DESC',
            'limit' => '0, ' . $limit,
        ];

        $result = $this->db->sqlBindQuery('select', 'board_articles', $param, $where, $options);

        return $result;
    }
    
    /*
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
    */
}