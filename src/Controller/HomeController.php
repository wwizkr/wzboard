<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\CryptoHelper;

class HomeController
{
    public function index()
    {
        // �� ��ο� �����͸� ��ȯ
        $skin = 'basic';
        $viewData = [];

        return [
            "viewPath" => "Home/{$skin}/index",
            "viewData" => $viewData
        ];
    }
}