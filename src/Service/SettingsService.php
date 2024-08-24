<?php
//파일위치 src/Service/SettingService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Model\SettingsModel;

class SettingsService
{
    protected $settingsModel;

    public function __construct(SettingsModel $settingsModel)
    {
        $this->settingsModel = $settingsModel;
    }

    /**
     * 특정 cf_id에 해당하는 일반 설정을 가져옵니다.
     *
     * @param int $cf_id
     * @return array 설정 데이터
     */
    public function getGeneralSettings($cf_id)
    {
        return $this->settingsModel->getConfigByCfId($cf_id);
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
        return $this->settingsModel->updateConfigByCfId($cf_id, $data);
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

        // 메뉴 코드 생성
        $me_code = $this->generateMenuCode($cf_id, $me_code, $me_depth);

        // 메뉴 순서 생성
        $me_order = $this->settingsModel->setMenuOrder($cf_id);

        // 삽입할 메뉴 데이터 배열 생성
        $menuData = [
            'cf_id' => ['i', $cf_id],
            'me_code' => ['s', $me_code],
            'me_order' => ['i', $me_order],
            // 추가 데이터 필드가 필요하면 여기에 추가
        ];

        // 실제로 메뉴 데이터를 데이터베이스에 삽입하는 로직
        return $this->settingsModel->insertMenu($menuData);
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
            $code = $this->settingsModel->getMaxMenuCode($cf_id, 1);
            $me_code = $code ? (int)substr($code, 0, WZ_CATE_LENGTH) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
        } else {
            // 하위 메뉴 코드 생성
            $tmp = $this->settingsModel->getMenuByCode($cf_id, $me_code);
            $depth = (int)$tmp['me_depth'] + 1;
            $cut = (int)($tmp['me_depth'] * WZ_CATE_LENGTH);

            $code = $this->settingsModel->getMaxSubMenuCode($cf_id, $tmp['me_code'], $depth);
            $me_code = $code ? (int)substr($code, $cut, WZ_CATE_LENGTH) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
            $me_code = $tmp['me_code'] . $me_code;
        }

        return $me_code;
    }
}