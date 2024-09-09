<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\CryptoHelper;

class HomeController
{
    public function index()
    {
        // 뷰 경로와 데이터를 반환
        $skin = 'basic';
        $viewData = [];

        return [
            "viewPath" => "Home/{$skin}/index",
            "viewData" => $viewData
        ];
    }
}