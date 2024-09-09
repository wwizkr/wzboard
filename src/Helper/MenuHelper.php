<?php
// src/Helper/MenuHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Model\MenuModel;

class MenuHelper
{
    public static function getMenuTree()
    {
        // MenuModel을 통해 캐시된 메뉴 데이터를 가져옴
        $menuModel = new MenuModel();
        $menuData = $menuModel->getMenuData();

        return self::generateMenuTree($menuData);
    }

    public static function generateMenuTree(array $menuData)
    {
        $menuTree = [];
        $indexedMenu = [];

        // 1. 각 메뉴 항목을 인덱스로 정리 (no 또는 me_code를 기준으로)
        foreach ($menuData as &$menuItem) {
            $menuItem['children'] = []; // 자식 메뉴를 담을 배열 추가
            $indexedMenu[$menuItem['no']] = &$menuItem;
        }

        // 2. 부모-자식 관계를 설정하여 트리 구조 생성
        foreach ($indexedMenu as &$menuItem) {
            if ($menuItem['me_parent'] != 0) {
                // 부모가 있는 경우, 해당 부모의 'children' 배열에 추가
                if (isset($indexedMenu[$menuItem['me_parent']])) {
                    $indexedMenu[$menuItem['me_parent']]['children'][] = &$menuItem;
                }
            } else {
                // 최상위 메뉴(부모가 없는 경우)는 트리의 루트에 추가
                $menuTree[] = &$menuItem;
            }
        }

        return $menuTree;
    }

    public static function clearMenuCache()
    {
        // MenuModel에서 캐시를 관리하고 있으므로, 여기서 직접 모델의 캐시를 지울 수 있음
        $menuModel = new MenuModel();
        $menuModel->clearCache();
    }
}