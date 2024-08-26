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
        // MenuModel을 통해 캐시된 메뉴 데이터를 가져옴
        $menuData = $this->menuModel->getMenuData();

        // 메뉴 데이터를 트리 구조로 변환
        $menuTree = MenuHelper::generateMenuTree($menuData);

        return $menuTree;
    }

    public function clearMenuCache()
    {
        // MenuModel에서 캐시를 관리하고 있으므로, 여기서 직접 모델의 캐시를 지울 수 있음
        $this->menuModel->clearCache();
    }
}