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
                'url' => '/adversting/admin/configs',
                'icon' => 'bi-people',
                'submenu' => [
                    'config' => [
                        'label' => '광고 관리 설정',
                        'url' => '/adversting/admin/config',
                    ],
                    'navershop' => [
                        'label' => '네이버 쇼핑 상품 관리',
                        'url' => '/adversting/admin/nshopList',
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