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

        // 임시로 캐시 강제 삭제
        $this->cacheHelper->clearCache($cacheKey);

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
        
        // 플러그인 메뉴 로드
        $adminMenu = $this->loadPluginMenus($adminMenu);

        // 메뉴를 캐시에 저장
        $this->cacheHelper->setCache($cacheKey, $adminMenu, 3600 * 24); // 1시간 동안 캐시

        return $adminMenu;
    }

    private function loadPluginMenus($adminMenu)
    {
        $pluginsDir = WZ_SRC_PATH . '/Plugins';
        $pluginOrder = require WZ_PROJECT_ROOT . '/config/pluginOrder.php';

        foreach ($pluginOrder as $plugin) {
            $pluginMenuFile = $pluginsDir . '/' . $plugin . '/adminMenu.php';
            if (file_exists($pluginMenuFile)) {
                require_once $pluginMenuFile;
                $className = "Plugins\\{$plugin}\\AdminMenu";
                if (class_exists($className)) {
                    $pluginMenu = new $className($this->container);
                    $adminMenu = array_merge($adminMenu, $pluginMenu->getMenu());
                }
            }
        }

        // 설정에 없는 추가 플러그인들을 로드
        if (is_dir($pluginsDir)) {
            $plugins = scandir($pluginsDir);
            foreach ($plugins as $plugin) {
                if ($plugin === '.' || $plugin === '..' || in_array($plugin, $pluginOrder)) {
                    continue;
                }
                $pluginMenuFile = $pluginsDir . '/' . $plugin . '/adminMenu.php';
                if (file_exists($pluginMenuFile)) {
                    require_once $pluginMenuFile;
                    $className = "Plugins\\{$plugin}\\AdminMenu";
                    if (class_exists($className)) {
                        $pluginMenu = new $className($this->container);
                        $adminMenu = array_merge($adminMenu, $pluginMenu->getMenu());
                    }
                }
            }
        }

        return $adminMenu;
    }

    public function setMenuCategory()
    {
        $menuCategory = [
            'boards' => ['title' => '게시판', 'children' => $this->getBoardMenus()],
            'page' => ['title' => '페이지', 'children' => []],
            'direct' => ['title' => '직접입력', 'children' => []],
        ];

        // 플러그인 메뉴 카테고리 로드
        $menuCategory = $this->loadPluginMenuCategories($menuCategory);

        return $menuCategory;
    }

    private function getBoardMenus()
    {
        $boards = [];
        $boardData = $this->adminBoardsModel->getBoardsConfig(null);
        if (!empty($boardData)) {
            foreach($boardData as $key => $val) {
                $boards[$key] = [
                    'me_cate2' => $val['board_id'],
                    'me_name' => $val['board_name'],
                    'me_title' => $val['board_name'],
                    'me_link' => '/board/'.$val['board_id'].'/list',
                ];
            }
        }
        return $boards;
    }

    private function loadPluginMenuCategories($menuCategory)
    {
        $pluginsDir = WZ_SRC_PATH . '/Plugins';
        if (is_dir($pluginsDir)) {
            $plugins = scandir($pluginsDir);
            foreach ($plugins as $plugin) {
                if ($plugin === '.' || $plugin === '..') {
                    continue;
                }
                $pluginMenuFile = $pluginsDir . '/' . $plugin . '/adminMenu.php';
                if (file_exists($pluginMenuFile)) {
                    require_once $pluginMenuFile;
                    $className = "Plugins\\{$plugin}\\AdminMenu";
                    if (class_exists($className)) {
                        $pluginMenu = new $className($this->container);
                        if (method_exists($pluginMenu, 'getMenuCategory')) {
                            $menuCategory = array_merge($menuCategory, $pluginMenu->getMenuCategory());
                        }
                    }
                }
            }
        }
        return $menuCategory;
    }
}