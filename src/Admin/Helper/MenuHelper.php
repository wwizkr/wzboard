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
                'icon' => 'bi-speedometer2',  // Bootstrap Icons 대쉬보드 아이콘
            ],
            'members' => [
                'label' => '회원관리',
                'url' => '/admin/members',
                'icon' => 'bi-people',  // Bootstrap Icons 사용자 아이콘
                'submenu' => [
                    'all_users' => [
                        'label' => '전체 회원',
                        'url' => '/admin/members/all',
                    ],
                    'add_user' => [
                        'label' => '회원등록',
                        'url' => '/admin/members/add',
                    ],
                ],
            ],
            'settings' => [
                'label' => '환경설정',
                'url' => '/admin/settings',
                'icon' => 'bi-gear',  // Bootstrap Icons 설정 아이콘
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