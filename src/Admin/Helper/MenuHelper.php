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
                        'url' => '/admin/members/list',
                    ],
                    'add' => [
                        'label' => '회원등록',
                        'url' => '/admin/members/add',
                    ],
                ],
            ],
            'boards' => [
                'label' => '게시판관리',
                'url' => '/admin/boardadmin/boards',
                'icon' => 'bi-people',
                'submenu' => [
                    'group' => [
                        'label' => '게시판 그룹관리',
                        'url' => '/admin/boardadmin/group',
                    ],
                    'categories' => [
                        'label' => '게시판 카테고리',
                        'url' => '/admin/boardadmin/category',
                    ],
                    'boards' => [
                        'label' => '게시판 관리',
                        'url' => '/admin/boardadmin/boards',
                    ],
                ],
            ],
            'trial' => [
                'label' => '문제 관리',
                'url' => '/admin/trialadmin/configs',
                'icon' => 'bi-people',
                'submenu' => [
                    'prompt' => [
                        'label' => '문제 프롬포트',
                        'url' => '/admin/trialadmin/prompt',
                    ],
                    'subject' => [
                        'label' => '문제 과목 관리',
                        'url' => '/admin/trialadmin/subject',
                    ],
                    'categories' => [
                        'label' => '카테고리 관리',
                        'url' => '/admin/trialadmin/category',
                    ],
                    'list' => [
                        'label' => '문제 관리',
                        'url' => '/admin/trialadmin/list',
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
                    'menus' => [
                        'label' => '메뉴 설정',
                        'url' => '/admin/settings/menus',
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