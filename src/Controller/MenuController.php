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
        // �޴� �����͸� �𵨿��� ������
        $menuData = $this->menuModel->getMenuData();
        // �޴� �����͸� Ʈ�� ������ ��ȯ
        $menuTree = MenuHelper::generateMenuTree($menuData);

        return $menuTree;
    }
}