<?php
// 파일위치 : src/Model/MembersModel.php

namespace Web\PublicHtml\Model;

use PDO;
use PDOException;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;

class MembersModel
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
    
    /*
     * 회원의 개별 정보를 가져옴
     * @param email [email 또는 mb_id]
     */
    public function getMemberDataById($email)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['mb_id'] = ['s', $email];
        $where['email'] = ['s', $email, 'or'];
        $options = [
            'order' => 'mb_no DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select','members',$param,$where,$options);

        return $result[0];
    }

    /*
     * 회원의 개별 정보를 가져옴
     * @param mb_no [mb_no]
     */
    public function getMemberDataByNo($mb_no)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['mb_no'] = ['s', $mb_no];
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
    public function getMemberListData($currentPage, $page_rows, $searchQuery, $filters = [], $sort = [])
    {
        $offset = ($currentPage - 1) * $page_rows;

        // WHERE 조건 생성
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];

        if (!empty($searchQuery)) {
            $where['nickName'] = ['like', $searchQuery, 'AND'];
            $where['email'] = ['like', $searchQuery, 'OR'];
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
            $order = 'signup_date DESC'; // 기본 정렬
        }

        // LIMIT 조건 생성
        $limit = "$offset, $page_rows";

        // SQL 실행
        $options = [
            'order' => $order,
            'limit' => $limit
        ];

        return $this->db->sqlBindQuery('select', 'members', [], $where, $options);
    }

    public function getTotalMemberCount($searchQuery, $filters = [])
    {
        // WHERE 조건 생성
        $where = [];

        if (!empty($searchQuery)) {
            $where['name'] = ['like', $searchQuery, 'AND'];
            $where['email'] = ['like', $searchQuery, 'OR'];
        }

        foreach ($filters as $key => $value) {
            $where[$key] = ['=', $value, 'AND'];
        }

        // SQL 실행
        $options = [
            'field' => 'COUNT(*) AS totalCount'
        ];

        $result = $this->db->sqlBindQuery('select', 'members', [], $where, $options);
        return $result[0]['totalCount'] ?? 0;
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
}
