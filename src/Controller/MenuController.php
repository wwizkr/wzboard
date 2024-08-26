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
        // MenuModel�� ���� ĳ�õ� �޴� �����͸� ������
        $menuData = $this->menuModel->getMenuData();

        // �޴� �����͸� Ʈ�� ������ ��ȯ
        $menuTree = MenuHelper::generateMenuTree($menuData);

        return $menuTree;
    }

    public function clearMenuCache()
    {
        // MenuModel���� ĳ�ø� �����ϰ� �����Ƿ�, ���⼭ ���� ���� ĳ�ø� ���� �� ����
        $this->menuModel->clearCache();
    }
}