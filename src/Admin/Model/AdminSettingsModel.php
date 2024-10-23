<?php
// 파일 위치: /src/Model/SettingsModel.php

namespace Web\Admin\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminSettingsModel
{
    use DatabaseHelperTrait;

    private $db;
    private $config_domain;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->config_domain = $container->get('ConfigHelper')->getConfig('config_domain');
    }

    /**
     * 특정 cf_id에 해당하는 환경설정을 가져옵니다.
     *
     * @param int $cf_id 설정 ID
     * @return array 환경 설정 데이터
     */
    public function getConfigByCfId($cf_id)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $cf_id];
        $options = [
            'order' => 'cf_id DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select', 'config_domain', $param, $where, $options);

        return $result[0];
    }

    /**
     * 모든 환경설정 데이터를 가져옵니다.
     *
     * @return array 환경 설정 데이터 배열
     */
    public function getAllConfigs()
    {
        $param = [];
        $options = [
            'order' => 'cf_id ASC',
        ];
        $result = $this->db->sqlBindQuery('select', 'config_domain', $param, [], $options);

        return $result;
    }

    /**
     * 특정 cf_id에 해당하는 환경 설정 데이터를 업데이트합니다.
     *
     * @param int $cf_id 설정 ID
     * @param array $data 업데이트할 데이터
     * @return bool 업데이트 성공 여부
     */
    public function updateConfigByCfId($cf_id, array $data)
    {
        // FormDataMiddleware에서 이미 $data가 변환되어 들어옴
        $where['cf_id'] = ['i', $cf_id];
        $options = [];

        $result = $this->db->sqlBindQuery('update', 'config_domain', $data, $where, $options);

        return $result;
    }

    /**
     * 메뉴 등록을 위한 order 값을 생성합니다.
     *
     * @param int $cf_id 설정 ID
     * @return int 생성된 order 값
     */
    public function setMenuOrder($cf_id)
    {
        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        $options = ['field' => 'max(me_order) as max'];
        $result = $this->db->sqlBindQuery('select', 'menus', $param, $where, $options);
        $maxValue = isset($result[0]['max']) ? $result[0]['max'] + 1 : 1;

        return $maxValue;
    }

    /**
     * 특정 cf_id와 me_depth에 해당하는 최대 메뉴 코드를 가져옵니다.
     *
     * @param int $cf_id 설정 ID
     * @param int $me_depth 메뉴 깊이
     * @return string|null 최대 메뉴 코드 (없을 경우 null)
     */
    public function getMaxMenuCode($cf_id, $me_depth)
    {
        $param = [];
        $where = [
            'cf_id' => ['i', $cf_id],
            'me_depth' => ['i', $me_depth]
        ];
        $options = [
            'field' => 'me_code as code',
            'order' => 'me_code DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select', 'menus', $param, $where, $options);

        return $result[0]['code'] ?? null;
    }

    /**
     * 특정 메뉴 코드에 해당하는 메뉴 데이터를 가져옵니다.
     *
     * @param int $cf_id 설정 ID
     * @param string $me_code 메뉴 코드
     * @return array 메뉴 데이터
     */
    public function getMenuByCode($cf_id, $me_code)
    {
        $param = [];
        $where = [
            'cf_id' => ['i', $cf_id],
            'me_code' => ['s', $me_code],
        ];
        $options = [
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select', 'menus', $param, $where, $options);

        //error_log("Result: ". print_r($result));

        return $result[0] ?? null;
    }

    /**
     * 특정 cf_id와 me_code에 해당하는 최대 하위 메뉴 코드를 가져옵니다.
     *
     * @param int $cf_id 설정 ID
     * @param string $me_code 상위 메뉴 코드
     * @param int $me_depth 메뉴 깊이
     * @return string|null 최대 하위 메뉴 코드 (없을 경우 null)
     */
    public function getMaxSubMenuCode($cf_id, $me_code, $me_depth)
    {
        $param = [];
        $where = [
            'cf_id' => ['i', $cf_id],
            'me_code' => ['s', $me_code, 'AND', 'like_right'],
            'me_depth' => ['i', $me_depth],
        ];
        $options = [
            'field' => 'me_code as code',
            'order' => 'me_code DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select', 'menus', $param, $where, $options);

        return $result[0]['code'] ?? null;
    }

    /**
     * 새로운 메뉴 데이터를 삽입합니다.
     *
     * @param array $menuData 삽입할 메뉴 데이터
     * @return bool 삽입 성공 여부
     */
    public function insertMenu(array $menuData)
    {
        return $this->db->sqlBindQuery('insert', 'menus', $menuData);
    }

    /**
     * 메뉴 정보를 업데이트 합니다.
     *
     * @param int $cf_id
     * @param int $no
     * @param string $me_code
     * @return boolean
     */
    public function updateMenuData($cf_id, $no, $me_code, $data)
    {
        $param = $data;
        $where = [];
        $where['cf_id'] = ['i', $cf_id];
        $where['no'] = ['i', $no];
        $where['me_code'] = ['s', $me_code];
        $options = [];

        return $this->db->sqlBindQuery('update', 'menus', $param, $where, $options);
    }

    /**
     * 메뉴 삭제
     */
    public function menuDelete($whereData)
    {
        $param = [];
        return $this->db->sqlBindQuery('delete', 'menus', $param, $whereData);
    }

    /**
     * 메뉴 순서 업데이트
     */
    public function updateMenuOrder($menuData)
    {
        if (empty($menuData)) {
            return false;
        }

        $order = 0;
        foreach($menuData as $key => $val) {
            if (!$val['no']) {
                continue;
            }
            
            $param = [
                'me_order' => ['i', $order],
            ];
            $where = [
                'cf_id' => ['i', $this->config_domain['cf_id']],
                'no' => ['i', $val['no']],
            ];
            
            $this->db->sqlBindQuery('update', 'menus', $param, $where);
            $order++;
        }

        return true;
    }

    public function getTotalClauseCount(?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        // WHERE 조건 생성
        $where = [
            'cf_id' => ['i', $this->config_domain['cf_id']],
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);

        // 추가 검색 쿼리를 생성
        $searchType = [
            'ct_page_type' => 'LIKE',
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'clause_table', [], $where, $options);
        return (int)($result[0]['totalCount'] ?? 0);
    }

    /*
     * 이용약관 목록을 가져옴.
     * 
     */
    public function getClauseListData(int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {
        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [
            'cf_id' => ['i', $this->config_domain['cf_id']],
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);

        // 추가 검색 쿼리를 생성
        $searchType = [
            'ct_page_type' => 'LIKE',
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);
        
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'ct_id DESC',
            'limit' => "$offset, $page_rows",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'clause_table', [], $where, $options);
    }

    public function getClauseDataById(int $ctId = null, int $cf_id = 1): array
    {
        $tablename = 'clause_table';

        if (!$ctId) {
            $result = $this->db->getTableFieldsWithNull($tablename);
            return $result;
        }

        $param = [];
        $where['ct_id'] = ['i', $ctId];
        $where['cf_id'] = ['i', $cf_id];
        $result = $this->db->sqlBindQuery('select', $tablename, $param, $where);

        if (isset($result[0]) && !empty($result[0])) {
            return $result[0];
        }

        $result = $this->db->getTableFieldsWithNull($tablename);
        return $result;
    }

    public function clauseItemUpdate(?int $cf_id, int $ctId, array $data): array
    {
        if ($ctId) {
            $param = $data;
            $where = ['ct_id' => ['i', $ctId]];
            $result = $this->db->sqlBindQuery('update', 'clause_table', $param, $where);
            return $result['result'] === 'success' 
                ? [
                    'result' => 'success',
                    'message' => '이용약관을 수정하였습니다.',
                    'view' => '/admin/settings/clauseForm/'.$ctId,
                    'data' => ['ctId' => $ctId]
                  ]
                : [
                    'result' => 'failure',
                    'message' => '오류가 발생하였습니다.'
                  ];
        } else {
            $param = $data;
            $param['cf_id'] = ['i', $cf_id];
            $result = $this->db->sqlBindQuery('insert', 'clause_table', $data, []);
            return $result['ins_id']
                ? [
                    'result' => 'success',
                    'message' => '이용약관 등록하였습니다.',
                    'view' => '/admin/settings/clauseForm/'.$result['ins_id'],
                    'data' => ['ctId' => $result['ins_id']]
                  ]
                : [
                    'result' => 'failure',
                    'message' => '오류가 발생하였습니다.'
                  ];
        }
    }

    public function clauseItemDelete(?int $cf_id, int $ctId): array
    {
        $where = [
            'cf_id' => ['i', $cf_id],
            'ct_id' => ['i', $ctId],
        ];
        
        $result = $this->db->sqlBindQuery('delete', 'clause_table', [], $where);

        return [
            'result' => 'success',
            'message' => '이용약관을 삭제하였습니다.'
        ];
    }
}