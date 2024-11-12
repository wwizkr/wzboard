<?php
// 파일 위치: /src/Model/AdminLevelModel.php

namespace Web\Admin\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminLevelModel
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
     * 회원의 개별 레벨 정보 또는 전체 정보를 가져옴
     * @param level 
     */
    public function getMemberLevelData($level = false, $level_use = 1, $sort='ASC')
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        if ($level_use === 1) {
            $where['level_use'] = ['i', 1];
        }

        if ($level) {
            $where['level_id'] = ['i', $level];
        }
        $options = [
            'order' => 'level_id '. $sort,
        ];
        $result = $this->db->sqlBindQuery('select','members_level',$param,$where,$options);
        
        /*
        if($level) {
            $levelData = $result[0];
        } else {
            $levelData = $result;
        }
        */

        return $result;
    }

    public function memberLevelModify(int $level_id=0, array $data = [])
    {
        $param = $data;
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['level_id'] = ['i', $level_id];

        return $this->db->sqlBindQuery('update', 'members_level', $param, $where);
    }

    public function getAdminAuthData()
    {
        $param = [];
        $where['cf_id'] = ['i', 1];
        $result = $this->db->sqlBindQuery('select', 'members_auth', $param, $where);

        return $result;
    }

    public function getAdminAuthDataByNo(int $no)
    {
        $param = [];
        $where['cf_id'] = ['i', 1];
        $where['no'] = ['i', $no];
        $result = $this->db->sqlBindQuery('select', 'members_auth', $param, $where);

        return $result[0] ?? [];
    }

    public function memberAuthUpdate(int $level_id=0, string $menuCate, string $menuCode, string $menuAuth)
    {
        $param = [];
        $where = [
            'cf_id' => ['i', 1],
            'level_id' => ['i', $level_id],
            'menuCate' => ['s', $menuCate],
            'menuCode' => ['s', $menuCode],
        ];

        $result = $this->db->sqlBindQuery('select', 'members_auth', $param, $where);

        if (empty($result)) {
            $param = [
                'cf_id' => ['i', 1],
                'level_id' => ['i', $level_id],
                'menuCate' => ['s', $menuCate],
                'menuCode' => ['s', $menuCode],
                'menuAuth' => ['s', $menuAuth],
            ];

            $result = $this->db->sqlBindQuery('insert', 'members_auth', $param);
        } else {
            $param = [
                'menuAuth' => ['s', $menuAuth],
            ];

            $result = $this->db->sqlBindQuery('update', 'members_auth', $param, $where);
        }

        return $result;
    }

    public function memberAuthDelete(int $no=0, string $menuCode)
    {
        $param = [];
        $where = [
            'cf_id' => ['i', 1],
            'no' => ['i', $no],
            'menuCode' => ['s', $menuCode],
        ];

        return $this->db->sqlBindQuery('delete', 'members_auth', $param, $where);
    }

    public function getAdminActionAuthData(int $level, string $activeCode)
    {
        $param = [];
        $where['cf_id'] = ['i', 1];
        $where['level_id'] = ['i', $level];
        $where['menuCode'] = ['s', $activeCode];
        $result = $this->db->sqlBindQuery('select', 'members_auth', $param, $where);

        return $result[0] ?? [];
    }
}