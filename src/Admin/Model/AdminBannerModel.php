<?php
// 파일 위치: /src/Model/AdminBannerModel.php

namespace Web\Admin\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;

class AdminBannerModel
{
    use DatabaseHelperTrait;

    private $db;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
    }

    public function getBannerList(int $cf_id, string $position = null, int $use = null): array
    {
        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        if ($position) {
            $where['ba_position'] = ['s', $position];
        }

        if ($use !== null) {
            $where['ba_use'] = ['i', $use];
        }
        
        $options = [
            'order' => 'ba_order ASC',
        ];

        $result = $this->db->sqlBindQuery('select', 'banner_table', $param, $where, $options);

        return $result;
    }

    public function getBannerDataById(int $baId = null, int $cf_id = 1): array
    {
        if (!$baId) {
            $result = $this->db->getTableFieldsWithNull('banner_table');
            return $result;
        }

        $param = [];
        $where['ba_id'] = ['i', $baId];
        $where['cf_id'] = ['i', $cf_id];
        $result = $this->db->sqlBindQuery('select', 'banner_table', $param, $where);

        if (isset($result[0]) && !empty($result[0])) {
            return $result[0];
        }

        $result = $this->db->getTableFieldsWithNull('banner_table');
        return $result;
    }

    public function updateBanner(int $cf_id, int $baId = null, array $param): array
    {
        if ($baId > 0) {
            $where['ba_id'] = ['i', $baId];
            $where['cf_id'] = ['i', $cf_id];
            $result = $this->db->sqlBindQuery('update', 'banner_table', $param, $where);
            $result['ins_id'] = $baId;
        } else {
            $param['cf_id'] = ['i', $cf_id];
            $result = $this->db->sqlBindQuery('insert', 'banner_table', $param);
        }

        return $result;
    }
}