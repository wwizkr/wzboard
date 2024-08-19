<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Model\SettingsModel;
use Web\PublicHtml\Service\SettingsService;
use Web\PublicHtml\Helper\DependencyContainer;

class SettingsController
{
    protected $container;
    protected $settingsModel;
    protected $settingsService;

    // 생성자에서 DependencyContainer와 SettingsService를 받아옴
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $settingsModel = new SettingsModel($container); // SettingsModel 인스턴스 생성
        $this->settingsService = new SettingsService($settingsModel); // SettingsService에 SettingsModel 주입
    }

    public function index()
    {
        $viewData = [
            'title' => 'Index',
            'content' => 'This is the user list.'
        ];

        return ['Settings/index', $viewData];
    }

    public function general()
    {
        // 컨테이너에서 cf_id를 가져옴
        $cf_id = $this->container->get('cf_id');

        // 환경설정을 가져옴
        $config_domain = $this->settingsService->getGeneralSettings($cf_id);

        $viewData = [
            'title' => '기본환경 설정',
            'content' => 'This is the general settings.',
            'config_domain' => $config_domain // 환경설정 데이터를 viewData에 포함
        ];

        return ['Settings/general', $viewData];
    }

    public function view($params)
    {
        // 특정 유저를 보는 코드
        $userId = $params['id'];
        $viewData = [
            'title' => "Viewing User #{$userId}",
            'content' => "This is the detail for user #{$userId}."
        ];

        return ['User/view', $viewData];
    }
}