<?php
// 파일 위치: /src/Model/SettingsModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;

class SettingsModel
{
    use DatabaseHelperTrait;

    private $db;
    private $config;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->config = $container->get('config');
    }

    /*
     * 특정 cf_id에 해당하는 환경설정을 가져옴
     * @param int $cf_id
     * @return array
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
        $result = $this->db->sqlBindQuery('select', $this->getTableName('config_domain'), $param, $where, $options);

        return $result[0];
    }

    /*
     * 모든 환경설정 데이터를 가져옴
     * @return array
     */
    public function getAllConfigs()
    {
        $param = [];
        $options = [
            'order' => 'cf_id ASC',
        ];
        $result = $this->db->sqlBindQuery('select', $this->getTableName('config_domain'), $param, [], $options);

        return $result;
    }

    /*
     * 환경 설정 데이터를 업데이트 함
     * @return boolean
     */
    public function updateConfigByCfId($cf_id, array $data)
    {
        $columns = [];
        $params = [];

        /*
         * $data = $param ===> $field_name = ['s',$val];
         */

        $param = $data;
        $where['cf_id'] = ['i',$cf_id];
        $options = [];
        $columns = implode(', ', $columns);
        $params[':cf_id'] = $cf_id;

        $result = $this->db->sqlBindQuery('update', $this->getTableName('config_domain'), $param, [], $options);

        return $result;
    }
}