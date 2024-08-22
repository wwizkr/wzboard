<?php
// 파일 위치: /src/Admin/Helper/MenuHelper.php

namespace Web\Admin\Helper;

class MenuHelper
{
    public static function getAdminMenu()
    {
        return [
            'dashboard' => [
                'label' => '대쉬보드',
                'url' => '/admin',
                'icon' => 'bi-speedometer2',
            ],
            'members' => [
                'label' => '회원관리',
                'url' => '/admin/members/all',
                'icon' => 'bi-people',
                'submenu' => [
                    'all' => [
                        'label' => '전체 회원',
                        'url' => '/admin/members/all',
                    ],
                    'add' => [
                        'label' => '회원등록',
                        'url' => '/admin/members/add',
                    ],
                ],
            ],
            'boards' => [
                'label' => '게시판관리',
                'url' => '/admin/boards/configs',
                'icon' => 'bi-people',
                'submenu' => [
                    'group' => [
                        'label' => '게시판 그룹관리',
                        'url' => '/admin/boards/group',
                    ],
                    'categories' => [
                        'label' => '게시판 카테고리',
                        'url' => '/admin/boards/category',
                    ],
                    'config' => [
                        'label' => '게시판 관리',
                        'url' => '/admin/boards/configs',
                    ],
                ],
            ],
            'settings' => [
                'label' => '환경설정',
                'url' => '/admin/settings/general',
                'icon' => 'bi-gear',
                'submenu' => [
                    'general' => [
                        'label' => '기본 환경설정',
                        'url' => '/admin/settings/general',
                    ],
                    'security' => [
                        'label' => 'Security',
                        'url' => '/admin/settings/security',
                    ],
                ],
            ],
            'reports' => [
                'label' => 'Reports',
                'url' => '/admin/reports',
                'icon' => 'bi-bar-chart',  // Bootstrap Icons 리포트 아이콘
            ],
        ];
    }
}