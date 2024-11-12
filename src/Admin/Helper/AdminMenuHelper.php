<?php
// 파일 위치: /src/Admin/Helper/AdminMenuHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\Admin\Model\AdminSettingsModel;
use Web\Admin\Service\AdminLevelService;

class AdminMenuHelper
{
    private $container;
    private $db;
    private $cacheHelper;
    private $adminSettingsModel;
    private $adminBoardsModel;
    private $adminLevelService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
        $this->cacheHelper = $this->container->get('CacheHelper');
        $this->adminBoardsModel = $this->container->get('AdminBoardsModel');

        $this->adminSettingsModel = new AdminSettingsModel($this->container);
        $this->adminLevelService = new AdminLevelService($this->container);
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
                'code' => '000',
                'open' => true,
            ],
            'settings' => [
                'label' => '환경설정',
                'url' => '/admin/config/configDomain',
                'icon' => 'bi-gear',
                'code' => '001',
                'open' => true,
                'submenu' => [
                    'general' => [
                        'label' => '기본 환경설정',
                        'url' => '/admin/config/configDomain',
                        'code' => '001001',
                        'open' => true,
                    ],
                    'menus' => [
                        'label' => '메뉴 설정',
                        'url' => '/admin/settings/menus',
                        'code' => '001002',
                        'open' => true,
                    ],
                    'clause' => [
                        'label' => '이용약관 관리',
                        'url' => '/admin/settings/clauseList',
                        'code' => '001003',
                        'open' => true,
                    ],
                ],
            ],
            'members' => [
                'label' => '회원관리',
                'url' => '/admin/members/all',
                'icon' => 'bi-people',
                'code' => '002',
                'open' => true,
                'submenu' => [
                    'all' => [
                        'label' => '회원 목록',
                        'url' => '/admin/members/list',
                        'code' => '002001',
                        'open' => true,
                    ],
                    'add' => [
                        'label' => '회원 등록',
                        'url' => '/admin/members/memberForm',
                        'code' => '002002',
                        'open' => true,
                    ],
                    'level' => [
                        'label' => '회원 등급관리',
                        'url' => '/admin/members/memberLevel',
                        'code' => '002003',
                        'open' => true,
                    ],
                    'auth' => [
                        'label' => '등급별 권한관리',
                        'url' => '/admin/members/memberAuth',
                        'code' => '002004',
                        'open' => true,
                    ],
                ],
            ],
            'boards' => [
                'label' => '게시판관리',
                'url' => '/admin/boardadmin/boards',
                'icon' => 'bi-people',
                'code' => '003',
                'open' => true,
                'submenu' => [
                    'group' => [
                        'label' => '게시판 그룹관리',
                        'url' => '/admin/boardadmin/group',
                        'code' => '003001',
                        'open' => true,
                    ],
                    'categories' => [
                        'label' => '게시판 카테고리',
                        'url' => '/admin/boardadmin/category',
                        'code' => '003002',
                        'open' => true,
                    ],
                    'boards' => [
                        'label' => '게시판 관리',
                        'url' => '/admin/boardadmin/boards',
                        'code' => '003003',
                        'open' => true,
                    ],
                ],
            ],
            'design' => [
                'label' => '디자인 관리',
                'url' => '/admin/template/templateList',
                'icon' => 'bi-people',
                'code' => '004',
                'open' => true,
                'submenu' => [
                    'template' => [
                        'label' => '템플릿 관리',
                        'url' => '/admin/template/templateList',
                        'code' => '004001',
                        'open' => true,
                    ],
                    'page' => [
                        'label' => '페이지 생성/관리',
                        'url' => '/admin/template/pageGroup',
                        'code' => '004002',
                        'open' => true,
                    ],
                    'banner' => [
                        'label' => '배너 관리',
                        'url' => '/admin/banner/bannerList',
                        'code' => '004003',
                        'open' => true,
                    ],
                    'widget' => [
                        'label' => '위젯 관리',
                        'url' => '/admin/widget/widgetList',
                        'code' => '004004',
                        'open' => true,
                    ],
                ],
            ],
            'reports' => [
                'label' => 'Reports',
                'url' => '/admin/reports',
                'icon' => 'bi-bar-chart',
                'code' => '005',
                'open' => false,
            ],
        ];
        
        // 플러그인 메뉴 로드
        $adminMenu = $this->loadPluginMenus($adminMenu);

        $adminMenuArray = [];
        foreach($adminMenu as $key => $val) {
            if (isset($val['open']) && $val['open'] === false) {
                continue;
            }
            $adminMenuArray[$key] = $val;
            $adminMenuArray[$key]['submenu'] = [];
            if (isset($val['submenu']) && !empty($val['submenu'])) {
                foreach($val['submenu'] as $subkey => $subval) {
                    if (isset($subval['open']) && $subval['open'] === false) {
                        continue;
                    }
                    $adminMenuArray[$key]['submenu'][$subkey] = $subval;
                }
            }
        }
        // 메뉴를 캐시에 저장
        $this->cacheHelper->setCache($cacheKey, $adminMenuArray, 3600 * 24); // 1시간 동안 캐시

        return $adminMenuArray;
    }

    private function loadPluginMenus($adminMenu)
    {
        $pluginsDir = WZ_SRC_PATH . '/Plugins';
        $pluginOrder = require WZ_PROJECT_ROOT . '/config/pluginOrder.php';

        // 관리자 레벨별 허용가능 메뉴
        $authMiddleware = $this->container->get('AuthMiddleware');
        $authUser = $authMiddleware->getAuthUser();

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

        if ($authUser['is_super']) {
            return $adminMenu;
        }

        $adminAuthMenu = $this->adminLevelService->getAdminAuthData();
        $authMenu = isset($adminAuthMenu[$authUser['member_data']['member_level']]) ? $adminAuthMenu[$authUser['member_data']['member_level']] : [];

        // 관리자이지만 허용 가능한 메뉴가 없다면 -> 홈페이지로 보내야함.
        if (empty($authMenu)) {
            header('Location: /');
            return [];
        }

        // 권한이 있는 메뉴 코드를 카테고리별로 그룹화
        $authMenuArray = array_reduce($authMenu, function($result, $item) {
            $result[$item['menuCate']][] = $item['menuCode'];
            return $result;
        }, []);

        // 권한이 있는 메뉴만 필터링
        $menu = array_filter($adminMenu, function($menuItems, $category) use ($authMenuArray) {
            return isset($authMenuArray[$category]) && !empty($authMenuArray[$category]);
        }, ARRAY_FILTER_USE_BOTH);

        // 서브메뉴 필터링
        array_walk($menu, function(&$menuItem, $category) use ($authMenuArray) {
            if (empty($menuItem['submenu'])) {
                return;
            }
            
            $menuItem['submenu'] = array_filter($menuItem['submenu'], function($submenu) use ($authMenuArray, $category) {
                return in_array($submenu['code'], $authMenuArray[$category]);
            });
        });

        return $menu;
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