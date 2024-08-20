<?php
//파일위치 src/Service/SettingService.php

namespace Web\PublicHtml\Service;

use  Web\PublicHtml\Model\SettingsModel;

class SettingsService
{
    protected $settingsModel;

    public function __construct(SettingsModel $settingsModel)
    {
        $this->settingsModel = $settingsModel;
    }

    public function getGeneralSettings($cf_id)
    {
        return $this->settingsModel->getConfigByCfId($cf_id);
    }

    public function updateGeneralSettings($cf_id, array $data)
    {
        /*
         * Data 업데이트 후 캐시 갱신
         */
        return $this->settingsModel->updateConfigByCfId($cf_id, $data);
    }
}