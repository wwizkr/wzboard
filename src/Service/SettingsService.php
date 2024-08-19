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
}