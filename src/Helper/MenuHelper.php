<?php
// src/Helper/MenuHelper.php

namespace Web\PublicHtml\Helper;

class MenuHelper
{
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
}

/*
1. 트리 구조화 로직 설명
Step 1: me_code를 기준으로 메뉴 항목을 인덱스화하고, 각 메뉴 항목에 children 배열을 추가합니다. 이 배열은 하위 메뉴를 담기 위해 사용됩니다.
Step 2: 모든 메뉴 항목에 대해 부모(me_parent)가 있는지를 확인합니다. 부모가 있는 경우 해당 부모 항목의 children 배열에 현재 메뉴 항목을 추가합니다. 부모가 없는 경우, 즉 최상위 메뉴는 최종 트리 구조의 루트에 추가됩니다.

$menuData = [
    ['me_code' => '1', 'me_parent' => 0, 'me_depth' => 1, 'me_name' => 'Home'],
    ['me_code' => '2', 'me_parent' => 0, 'me_depth' => 1, 'me_name' => 'About'],
    ['me_code' => '3', 'me_parent' => 1, 'me_depth' => 2, 'me_name' => 'Company Info'],
    ['me_code' => '4', 'me_parent' => 1, 'me_depth' => 2, 'me_name' => 'Contact'],
    ['me_code' => '5', 'me_parent' => 3, 'me_depth' => 3, 'me_name' => 'Team'],
];

$menuTree = [
    [
        'me_code' => '1',
        'me_parent' => 0,
        'me_depth' => 1,
        'me_name' => 'Home',
        'children' => [
            [
                'me_code' => '3',
                'me_parent' => 1,
                'me_depth' => 2,
                'me_name' => 'Company Info',
                'children' => [
                    [
                        'me_code' => '5',
                        'me_parent' => 3,
                        'me_depth' => 3,
                        'me_name' => 'Team',
                        'children' => []
                    ]
                ]
            ],
            [
                'me_code' => '4',
                'me_parent' => 1,
                'me_depth' => 2,
                'me_name' => 'Contact',
                'children' => []
            ]
        ]
    ],
    [
        'me_code' => '2',
        'me_parent' => 0,
        'me_depth' => 1,
        'me_name' => 'About',
        'children' => []
    ]
];
*/