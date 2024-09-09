<?php
//파일위치 src/Service/SettingService.php

namespace Web\Admin\Service;

use Web\Admin\Model\AdminSettingsModel;

class AdminSettingsService
{
    protected $adminSettingsModel;

    public function __construct(AdminSettingsModel $adminSettingsModel)
    {
        $this->adminSettingsModel = $adminSettingsModel;
    }

    /**
     * 특정 cf_id에 해당하는 일반 설정을 가져옵니다.
     *
     * @param int $cf_id
     * @return array 설정 데이터
     */
    public function getGeneralSettings($cf_id)
    {
        return $this->adminSettingsModel->getConfigByCfId($cf_id);
    }

    /**
     * 특정 cf_id에 해당하는 일반 설정을 업데이트합니다.
     *
     * @param int $cf_id
     * @param array $data 업데이트할 데이터
     * @return bool 업데이트 성공 여부
     */
    public function updateGeneralSettings($cf_id, array $data)
    {
        /*
         * 데이터 업데이트 후 캐시 갱신
         */
        $result = $this->adminSettingsModel->updateConfigByCfId($cf_id, $data);

        return $this->adminSettingsModel->getConfigByCfId($cf_id);
    }

    /**
     * 새로운 메뉴 데이터를 삽입합니다.
     *
     * @param string $type 메뉴 유형
     * @param array $data 삽입할 메뉴 데이터
     * @return bool 삽입 성공 여부
     */
    public function insertMenuData($type, array $data)
    {
        $cf_id = $data['cf_id'][1];
        $me_code = $data['me_code'][1];
        $me_depth = $data['me_depth'][1];
        $me_name = $data['me_name'][1];

        // 메뉴 코드 생성
        $result = $this->generateMenuCode($cf_id, $me_code, $me_depth);
        // 메뉴 순서 생성
        $me_order = $this->adminSettingsModel->setMenuOrder($cf_id);

        // 삽입할 메뉴 데이터 배열 생성
        $menuData = [
            'cf_id' => ['i', $cf_id],
            'me_code' => ['s', $result['me_code']],
            'me_parent' => ['i', $result['me_parent']],
            'me_depth' => ['i', $result['me_depth']],
            'me_code' => ['s', $result['me_code']],
            'me_order' => ['i', $me_order],
            'me_name' => ['s', $me_name],
        ];

        // 실제로 메뉴 데이터를 데이터베이스에 삽입하는 로직
        $insert = $this->adminSettingsModel->insertMenu($menuData);

        return $this->adminSettingsModel->getMenuByCode($cf_id, $result['me_code']);
    }

    /**
     * 메뉴 코드를 생성합니다.
     *
     * @param int $cf_id 설정 ID
     * @param string $me_code 기존 메뉴 코드 (없을 수도 있음)
     * @param int $me_depth 메뉴 깊이 (1단계 또는 하위 메뉴)
     * @return string 생성된 메뉴 코드
     */
    private function generateMenuCode($cf_id, $me_code = '', $me_depth = 1)
    {
        if ($me_depth == 1) {
            // 1단계 메뉴 코드 생성
            $code = $this->adminSettingsModel->getMaxMenuCode($cf_id, 1);
            $me_code = $code ? (int)substr($code, 0, 3) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
            $depth = $me_depth;
            $parent = 0;
        } else {
            // 하위 메뉴 코드 생성
            $tmp = $this->adminSettingsModel->getMenuByCode($cf_id, $me_code);
            $depth = (int)$tmp['me_depth'] + 1;
            $cut = (int)($tmp['me_depth'] * 3);
            $parent = (int)$tmp['no'];

            $code = $this->adminSettingsModel->getMaxSubMenuCode($cf_id, $tmp['me_code'], $depth);
            $me_code = $code ? (int)substr($code, $cut, 3) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
            $me_code = $tmp['me_code'] . $me_code;
        }

        $responsData = [
            'me_code' => $me_code,
            'me_depth' => $depth,
            'me_parent' => $parent,
        ];

        return $responsData;
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
        $result = $this->adminSettingsModel->updateMenuData($cf_id, $no, $me_code, $data);

        return $this->adminSettingsModel->getMenuByCode($cf_id, $me_code);
    }
}