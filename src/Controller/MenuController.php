<?php
// src/Controller/MenuController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Model\MenuModel;
use Web\PublicHtml\Helper\MenuHelper;

class MenuController
{
    protected $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function getMenuData()
    {
        // 메뉴 데이터를 모델에서 가져옴
        $menuData = $this->menuModel->getMenuData();
        // 메뉴 데이터를 트리 구조로 변환
        $menuTree = MenuHelper::generateMenuTree($menuData);

        return $menuTree;
    }
}