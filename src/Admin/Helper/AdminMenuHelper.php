<?php
// 파일 위치: /src/Admin/Helper/AdminMenuHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Model\AdminSettingsModel;
use Web\Admin\Model\AdminBoardsModel;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

class AdminMenuHelper
{
    private $container;
    private $db;
    private $cacheHelper;
    private $adminSettingsModel;
    private $adminBoardsModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
        $this->cacheHelper = $this->container->get('CacheHelper');
        $this->adminBoardsModel = $this->container->get('AdminBoardsModel');

        $this->adminSettingsModel = new AdminSettingsModel($this->container);
    }

    public function getAdminMenu()
    {
        $cacheKey = 'admin_menu_cache';
    
        // 캐시된 메뉴가 있는지 확인
        $cachedMenu = $this->cacheHelper->getCache($cacheKey);
        if ($cachedMenu !== null) {
            return $cachedMenu;
        }

        $adminMenu = [
            'dashboard' => [
                'label' => '대쉬보드',
                'url' => '/admin',
                'icon' => 'bi-speedometer2',
            ],
            'settings' => [
                'label' => '환경설정',
                'url' => '/admin/config/configDomain',
                'icon' => 'bi-gear',
                'submenu' => [
                    'general' => [
                        'label' => '기본 환경설정',
                        'url' => '/admin/config/configDomain',
                    ],
                    'menus' => [
                        'label' => '메뉴 설정',
                        'url' => '/admin/settings/menus',
                    ],
                    'clause' => [
                        'label' => '이용약관 관리',
                        'url' => '/admin/settings/clauseList',
                    ],
                ],
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
                        'label' => '회원 등록',
                        'url' => '/admin/members/add',
                    ],
                    'level' => [
                        'label' => '회원 등급관리',
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
            'design' => [
                'label' => '디자인 관리',
                'url' => '/admin/template/templateList',
                'icon' => 'bi-people',
                'submenu' => [
                    'template' => [
                        'label' => '템플릿 관리',
                        'url' => '/admin/template/templateList',
                    ],
                    'page' => [
                        'label' => '페이지 생성/관리',
                        'url' => '/admin/template/pageGroup',
                    ],
                    'banner' => [
                        'label' => '배너 관리',
                        'url' => '/admin/banner/bannerList',
                    ],
                    'widget' => [
                        'label' => '위젯 관리',
                        'url' => '/admin/widget/widgetList',
                    ],
                ],
            ],
            'reports' => [
                'label' => 'Reports',
                'url' => '/admin/reports',
                'icon' => 'bi-bar-chart',  // Bootstrap Icons 리포트 아이콘
            ],
        ];

        // 플러그인 디렉토리를 스캔하여, adminMenu를 추가하고, adminMenu를 캐쉬화 함
        // 플러그인 디렉토리 스캔
        $pluginsDir = WZ_SRC_PATH . '/Plugins';
        if (is_dir($pluginsDir)) {
            $plugins = scandir($pluginsDir);

            foreach ($plugins as $plugin) {
                if ($plugin === '.' || $plugin === '..') {
                    continue;
                }

                $pluginMenuFile = $pluginsDir . '/' . $plugin . '/adminMenu.php';
                if (file_exists($pluginMenuFile)) {
                    $pluginMenu = include $pluginMenuFile;
                    if (is_array($pluginMenu)) {
                        $adminMenu = array_merge($adminMenu, $pluginMenu);
                    }
                }
            }
        }

        // 메뉴를 캐시에 저장
        $this->cacheHelper->setCache($cacheKey, $adminMenu, 3600 * 24); // 1시간 동안 캐시

        return $adminMenu;
    }

    public function setMenuCategory()
    {
        /*
         * 게시판 메뉴 생성
         */
        $boards = [];
        $boardData = $this->adminBoardsModel->getBoardsConfig(null);
        if (!empty($boardData)) {
            foreach($boardData as $key=>$val) {
                $boards[$key]['me_cate2'] = $val['board_id'];
                $boards[$key]['me_name'] = $val['board_name'];
                $boards[$key]['me_title'] = $val['board_name'];
                $boards[$key]['me_link'] = '/board/'.$val['board_id'].'/list';
            }
        }

        $menuCategory = [
            'boards' => ['title' => '게시판', 'children' => $boards],
            'page' => ['title' => '페이지', 'children' => []],
            'direct' => ['title' => '직접입력', 'children' => []],
        ];

        return $menuCategory;
    }
}