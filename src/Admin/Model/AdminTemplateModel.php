<?php
// 파일 위치: /src/Model/SettingsModel.php

namespace Web\Admin\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;

class AdminTemplateModel
{
    use DatabaseHelperTrait;

    private $db;
    //private $config;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
    }

    public function getTemplateList(string $table, int $cf_id, string $position = null, int $use = null): array
    {
        $tablename = ($table === 'page') ? 'custom_page_lists' : 'custom_template_lists';

        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        if ($position) {
            $where['ct_position'] = ['s', $position];
        }
        if ($use !== null) {
            $where['ct_use'] = ['i', 0];
        }
        
        $options = [
            'order' => 'ct_order ASC',
        ];

        $result = $this->db->sqlBindQuery('select', $tablename, $param, $where, $options);

        return $result;
    }

    public function getTemplateDataById(string $table, int $ctId = null, int $cf_id): array
    {
        $tablename = ($table === 'page') ? 'custom_page_lists' : 'custom_template_lists';

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

    public function getTemplateDataBySectionId(string $table, string $section_id, int $cf_id): array
    {
        $tablename = ($table === 'page') ? 'custom_page_lists' : 'custom_template_lists';

        $param = [];
        $where['ct_section_id'] = ['s', $section_id];
        $where['cf_id'] = ['i', $cf_id];
        $result = $this->db->sqlBindQuery('select', $tablename, $param, $where);

        return $result[0] ?? [];
    }

    public function templateUpdate(string $table, int $ctId, array $param): array
    {
        if ($table === 'page') {
            $tablename = 'custom_page_lists';
        } else {
            $tablename = 'custom_template_lists';
        }

        if ($ctId > 0) {
            $param['ct_order'] = ['i', $_POST['ct_order'] ?? 0];
            $where['ct_id'] = ['i', $ctId];
            $where['cf_id'] = $param['cf_id'];

            unset($param['ct_section_id']);
            unset($param['cf_id']);

            $result = $this->db->sqlBindQuery('update', $tablename, $param, $where);
            $result['ins_id'] = $ctId;
        } else {
            $ct_position = $param['ct_position'][1];
            $order_sql = "SELECT MAX(ct_order) AS count FROM ".$this->getTableName($tablename)." WHERE ct_position = :ct_position";
            $stmt = $this->db->prepare($order_sql);
            $this->db->execute($stmt, [':ct_position' => $ct_position]);
            $order_res = $this->db->fetch($stmt);
            $order = ($order_res['count'] ?? 0) + 1;
            $param['ct_order'] = ['i', $order];
            $where = [];

            $result = $this->db->sqlBindQuery('insert', $tablename, $param, $where);
        }

        return $result;
    }

    public function getTemplateCiBoxItem(string $table, int $ct_id, int $box_id, int $cf_id, string $type = null): array
    {
        $tablename = ($table === 'page') ? 'custom_page_items' : 'custom_template_items';

        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        $where['ct_id'] = ['i', $ct_id];
        $where['ci_box_id'] = ['i', $box_id];
        if ($type) {
            $where['ci_type'] = ['s', $type];
        }

        $result = $this->db->sqlBindQuery('select', $tablename, $param, $where);

        return $result;
    }

    public function deleteAllTemplateCiBoxItem(string $table, int $ct_id, int $box_id, int $cf_id): void
    {
        $tablename = ($table === 'page') ? 'custom_page_items' : 'custom_template_items';

        $param = [];
        $where['cf_id'] = ['i', $cf_id];
        $where['ct_id'] = ['i', $ct_id];
        $where['ci_box_id'] = ['i', $box_id];

        $this->db->sqlBindQuery('delete', $tablename, $param, $where);

        return;
    }

    public function insertTemplateCiBoxItem(string $table, array $param): void
    {
        $tablename = ($table === 'page') ? 'custom_page_items' : 'custom_template_items';

        $where = [];

        $this->db->sqlBindQuery('insert', $tablename, $param, $where);

        return;
    }
}