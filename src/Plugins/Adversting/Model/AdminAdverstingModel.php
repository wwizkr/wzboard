<?php
// 파일 위치: /Plugins/Adversting/Model/AdminAdverstingModel.php

namespace Plugins\Adversting\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminAdverstingModel
{
    use DatabaseHelperTrait;
    
    protected $container;
    protected $db;
    protected $config_domain;
    protected $configHelper;
    protected $formDataMiddleware;
    protected $authMiddleware;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->config_domain = $container->get('ConfigHelper')->getConfig('config_domain');
        $this->formDataMiddleware = $container->get('FormDataMiddleware');
        $this->authMiddleware = $container->get('AuthMiddleware');
    }
    
    /*
     * 프로그램 총 갯수를 가져옴.
     */
    public function getTotalProgramCount(?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        
        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'adversting_company', [], $where, $options);

        return (int)($result[0]['totalCount'] ?? 0);
    }

    /*
     * 프로그램 목록을 가져옴.
     */
    public function getProgramListData(int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {
        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        
        // 추가 검색 쿼리를 생성
        $searchType = [
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);
        
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'status ASC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'adversting_company', [], $where, $options);
    }

    public function getProgramListAll(int $status = 0): array
    {
        // WHERE 조건 생성
        $where = [
            'status' => ['i', $status],
        ];

        $options = [
            'order' => 'status ASC',
        ];

        return $this->db->sqlBindQuery('select', 'adversting_company', [], $where, $options);
    }

    public function getProgramDataByNo($no)
    {
        $where['no'] = ['i', $no];

        return $this->db->sqlBindQuery('select', 'adversting_company', [], $where);
    }

    public function programUpdate(int $no, array $data)
    {
        if ($no) {
            $param = $data;
            $where['no'] = ['i', $no];

            $result = $this->db->sqlBindQuery('update', 'adversting_company', $param, $where);

            return [
                'result' => $result['result'],
                'programNo' => $no,
            ];
        } else {
            $param = $data;
            $result = $this->db->sqlBindQuery('insert', 'adversting_company', $param);

            return [
                'result' => $result['result'],
                'programNo' => $result['ins_id'] ?? 0,
            ];
        }
    }

    public function programDelete(int $no)
    {
        $where['no'] = ['i', $no];
        return $this->db->sqlBindQuery('delete', 'adversting_company', [], $where);
    }

    //---- Program -------- END-----------//

    //---- Item -------- START-----------//
    
    /**
     * 종료일이 지난 상품 종료 처리
     */
    public function clearItemListStaus()
    {
        $tableName = $this->getTableName('adversting_items');
        $sql = "UPDATE $tableName SET status = '2' WHERE close_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }

    /**
     * 총 갯수를 가져옴.
     */
    public function getTotalItemCount(?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'adversting_items', [], $where, $options);

        return (int)($result[0]['totalCount'] ?? 0);
    }

    /**
     * 목록을 가져옴.
     */
    public function getItemListData(int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {   
        $authUser = $this->authMiddleware->getAuthUser();

        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        
        // 추가 검색 쿼리를 생성
        $searchType = [
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);
        
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'no DESC',
            'limit' => "$offset, $page_rows",
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'adversting_items', [], $where, $options);
    }

    public function getItemDataByNo($no)
    {
        $where['no'] = ['i', $no];

        return $this->db->sqlBindQuery('select', 'adversting_items', [], $where);
    }

    public function checkSellerId(string $sellerId, int $sellerLevel)
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
            'mb_id' => ['s', $sellerId],
            'member_level' => ['i', $sellerLevel],
        ];

        $options = [
            'limit' => "1",
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
        ];

        return $this->db->sqlBindQuery('select', 'members', [], $where, $options);
    }

    public function itemUpdate(int $no, array $data)
    {
        if ($no) {
            $param = $data;
            $where['no'] = ['i', $no];

            $result = $this->db->sqlBindQuery('update', 'adversting_items', $param, $where);

            return [
                'result' => $result['result'],
                'itemNo' => $no,
            ];
        } else {
            $param = $data;
            $result = $this->db->sqlBindQuery('insert', 'adversting_items', $param);

            return [
                'result' => $result['result'],
                'itemNo' => $result['ins_id'] ?? 0,
            ];
        }
    }

    public function insertItemHistoryData(array $params)
    {
        return $this->db->sqlBindQuery('insert', 'adversting_items_history', $params);
    }

    public function insertItemRankingHistoryData(array $params)
    {
        return $this->db->sqlBindQuery('insert', 'adversting_items_ranking_history', $params);
    }

    public function viewPeriodHistory(int $programNo, int $itemNo)
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
            'programNo' => ['i', $programNo],
            'itemNo' => ['i', $itemNo],
        ];

        $options = [
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
        ];

        return $this->db->sqlBindQuery('select', 'adversting_items_history', [], $where, $options);
    }

    public function viewRankingHistory(int $programNo, int $itemNo)
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
            'programNo' => ['i', $programNo],
            'itemNo' => ['i', $itemNo],
        ];

        $options = [
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'order' => ' no DESC',
        ];

        return $this->db->sqlBindQuery('select', 'adversting_items_ranking_history', [], $where, $options);
    }

    public function itemDelete(int $no)
    {
        $where['no'] = ['i', $no];
        return $this->db->sqlBindQuery('delete', 'adversting_items', [], $where);
    }

    //---- Item -------- END-----------//

    //---- Period -------- START-----------//

    public function getTotalPeriodCount(?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'joins' => [
                [
                    'type' => 'LEFT',
                    'table' => $this->getTableName('adversting_items'),
                    'on' => $this->getTableName('adversting_items_history') . '.itemNo = ' . $this->getTableName('adversting_items') . '.no'
                ]
            ],
            'rawWhere' => "CONCAT('/', ".$this->getTableName('adversting_items_history').".cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'adversting_items_history', [], $where, $options);

        return (int)($result[0]['totalCount'] ?? 0);
    }

    /*
     * 목록을 가져옴.
     */
    public function getPeriodListData(int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {   
        $authUser = $this->authMiddleware->getAuthUser();

        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        
        // 추가 검색 쿼리를 생성
        $searchType = [
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);
        
        $options = [
            'field' => $this->getTableName('adversting_items') . '.*, ' . 
                       $this->getTableName('adversting_items_history') . '.managerId, ' . 
                       $this->getTableName('adversting_items_history') . '.sellerId, ' .
                       $this->getTableName('adversting_items_history') . '.slotCount AS slotCnt, ' .
                       $this->getTableName('adversting_items_history') . '.period, ' .
                       $this->getTableName('adversting_items_history') . '.orderType, ' .
                       $this->getTableName('adversting_items_history') . '.created_at AS period_at',
            'joins' => [
                [
                    'type' => 'LEFT',
                    'table' => $this->getTableName('adversting_items'),
                    'on' => $this->getTableName('adversting_items_history') . '.itemNo = ' . $this->getTableName('adversting_items') . '.no'
                ]
            ],
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : $this->getTableName('adversting_items_history').'.no DESC',
            'limit' => "$offset, $page_rows",
            'rawWhere' => "CONCAT('/', ".$this->getTableName('adversting_items_history').".cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'adversting_items_history', [], $where, $options);
    }

    //---- Period -------- END-----------//
}