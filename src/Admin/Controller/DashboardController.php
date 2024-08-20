<?php

namespace Web\Admin\Controller;

use Web\PublicHtml\Helper\DependencyContainer;

class DashboardController
{
    protected $container;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

    public function index($vars = [])
    {
        // 여기에 대시보드 관련 로직 추가
        $viewData = [
            'title' => 'Admin Dashboard',
            'content' => 'Welcome to the Admin Dashboard',
        ];

        return ["Dashboard/index", $viewData]; // Dashboard 스킨에서 index 뷰 사용
    }
}