<?php
// /src/Plugins/Adversting/adminMenu.php
namespace Plugins\Adversting;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

class AdminMenu
{
    private $container;
    private $db;
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
    }

    // 필수
    public function getMenu()
    {
        return [
            'adversting' => [
                'label' => '광고상품 관리',
                'url' => '/adversting/admin/itemList',
                'icon' => 'bi-people',
                'code' => '901',
                'open' => true,
                'submenu' => [
                    'config' => [
                        'label' => '광고 프로그램 관리',
                        'url' => '/adversting/admin/programList',
                        'code' => '901101',
                        'open' => true,
                    ],
                    'itemlist' => [
                        'label' => '광고 상품 관리',
                        'url' => '/adversting/admin/itemList',
                        'code' => '901102',
                        'open' => true,
                    ],
                    'periodlist' => [
                        'label' => '광고 상품 집행목록',
                        'url' => '/adversting/admin/periodList',
                        'code' => '901103',
                        'open' => true,
                    ],
                ],
            ],
        ];
    }

    // 필수
    public function getMenuCategory()
    {
        return [
        ];
    }
}