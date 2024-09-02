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
}