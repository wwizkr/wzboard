<?php
// 파일위치 : src/Model/MembersModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class MembersModel
{
    use DatabaseHelperTrait;
    
    protected $container;
    protected $db;
    protected $config_domain;
    protected $authMiddleware;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->authMiddleware = $this->container->get('AuthMiddleware');
    }
    
    /*
     * 회원의 개별 정보를 가져옴
     * @param value [email 또는 mb_id]
     */
    public function getMemberDataById($value)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['mb_id'] = ['s', $value];
        $where['email'] = ['s', $value, 'or'];
        $options = [
            'order' => 'mb_no DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select','members',$param,$where,$options);

        return $result[0] ?? [];
    }

    /*
     * 회원의 개별 정보를 가져옴
     * @param mb_no [mb_no]
     */
    public function getMemberDataByNo($mb_no, $null)
    {
        if (!$mb_no && $null === true) {
            $result = $this->db->getTableFieldsWithNull('members');
            return $result;
        }


        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['mb_no'] = ['i', $mb_no];
        $options = [
            'order' => 'mb_no DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select','members',$param,$where,$options);

        return $result[0];
    }

    /*
     * 회원의 개별 레벨 정보 또는 전체 정보를 가져옴
     * @param level
     * /AdminLevelService 로 이전된 메소드. 차후 삭제
     */
    public function getMemberLevelData($level=0, $sort='ASC')
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        if($level) {
            $where['level_id'] = ['i', $level];
        }
        $options = [
            'order' => 'level_id '. $sort,
        ];
        $result = $this->db->sqlBindQuery('select','members_level',$param,$where,$options);

        if($level) {
            $levelData = $result[0];
        } else {
            $levelData = $result;
        }

        return $levelData;
    }

    /*
     * 회원 목록을 가져옴.
     * @param level 
     */
    public function getMemberListData(int $currentPage, int $page_rows, ?string $searchQuery = null, array $filters = [], array $sort = [], array $additionalQueries = []): array
    {
        $authUser = $this->authMiddleware->getAuthUser();

        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [
            'cf_id' => ['i', $this->config_domain['cf_id']],
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        
        // 추가 검색 쿼리를 생성
        $searchType = [
            'member_level' => '=',
        ];
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues, $searchType);
        
        $options = [
            'order' => !empty($sort) ? "{$sort['field']} {$sort['order']}" : 'mb_no DESC',
            'limit' => "$offset, $page_rows",
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        return $this->db->sqlBindQuery('select', 'members', [], $where, $options);
    }

    public function getTotalMemberCount(?string $searchQuery = null, array $filters = [], array $additionalQueries = []): int
    {
        $authUser = $this->authMiddleware->getAuthUser();

        // WHERE 조건 생성
        $where = [
            'cf_id' => ['i', $this->config_domain['cf_id']],
        ];

        [$addWhere, $bindValues] =  CommonHelper::buildSearchConditions($searchQuery ?? '', $filters);
        $processedQueries = CommonHelper::additionalModelQueries($additionalQueries, $addWhere, $bindValues);

        $options = [
            'field' => 'COUNT(*) AS totalCount',
            'rawWhere' => "CONCAT('/', cf_class, '/') LIKE '%/{$authUser['cf_class']}/%'",
            'addWhere' => implode(' AND ', $addWhere),
            'values' => $bindValues
        ];

        $result = $this->db->sqlBindQuery('select', 'members', [], $where, $options);
        return (int)($result[0]['totalCount'] ?? 0);
    }

    public function findBySocialId($providerName, $identifier)
    {
        $where = [
            'social_provider' => ['s', $providerName],
            'social_id' => ['s', $identifier],
        ];

        $options = [
        ];

        $result = $this->db->sqlBindQuery('select', 'members', [], $where, $options);
        return $result[0] ?? [];
    }

    public function insertMemberData($isSocial, $data)
    {
        $result = $this->db->sqlBindQuery('insert', 'members', $data, []);

        if (!$result['ins_id']) {
            return [
                'result' => 'failure',
                'message' => '오류가 발생하였습니다.'
            ];
        }
        
        // social 가입일 경우 아이디를 생성해 줌.
        if ($result['ins_id'] && $isSocial) {
            $provider = $data['social_provider'][1];
            $formattedNumber = sprintf("%08d", $result['ins_id']);
            $mb_id = $provider.'_'.$formattedNumber;
            $param['mb_id'] = ['s', $mb_id];
            $where['mb_no'] = ['i', $result['ins_id']];
            $updated = $this->db->sqlBindQuery('update', 'members', $param, $where);
            unset($param);
            unset($where);
        }
        
        $param = [];
        $where['mb_no'] = ['i', $result['ins_id']];
        $memberData = $this->db->sqlBindQuery('select', 'members', $param, $where);
        
        return $memberData[0] ?? null;
    }

    public function memberUpdate(int $no, array $param, array $levelData)
    {
        if ($no) {
            $where['mb_no'] = ['i', $no];

            $result = $this->db->sqlBindQuery('update', 'members', $param, $where);

            return [
                'result' => $result['result'],
                'memberNo' => $no,
            ];
        } else {
            $result = $this->db->sqlBindQuery('insert', 'members', $param);

            if ($result['ins_id']) {
                $authUser = $this->authMiddleware->getAuthUser();
                $cf_id = $authUser['member_data']['cf_id'];
                $cf_class = isset($levelData['is_admin']) && $levelData['is_admin'] ? $authUser['member_data']['cf_class'].'/'.$result['ins_id'] : $authUser['member_data']['cf_class'];
                
                $update_param = [
                    'cf_id' => ['i', $cf_id],
                    'cf_class' => ['s', $cf_class],
                ];
                $update_where['mb_no'] = ['i', $result['ins_id']];
                $this->db->sqlBindQuery('update', 'members', $update_param, $update_where);
            }

            return [
                'result' => $result['result'],
                'memberNo' => $result['ins_id'] ?? 0,
            ];
        }
    }
}
