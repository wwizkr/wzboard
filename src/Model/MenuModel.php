<?php
// src/Model/MenuModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Core\DependencyContainer;

class MenuModel
{
    protected $db;
    protected $config_domain;

    public function __construct()
    {
        $container = DependencyContainer::getInstance();
        $this->db = $container->get('db');
        $this->config_domain = $container->get('ConfigHelper')->getConfig('config_domain');
    }

    public function getMenuData($useCache = true)
    {
        $cacheKey = 'menu_cache_' . $this->config_domain['cf_domain'];
        $menuData = $useCache ? CacheHelper::getCache($cacheKey) : null;

        if ($menuData === null) {
            // DB에서 메뉴 데이터를 가져옴
            $query = "SELECT * FROM " . (new class {
                use DatabaseHelperTrait;
            })->getTableName('menus') . " WHERE cf_id = :cf_id";
            $stmt = $this->db->query($query, ['cf_id' => $this->config_domain['cf_id']]);
            $menuData = $this->db->fetchAll($stmt);

            if ($menuData) {
                // 데이터를 암호화하여 캐시에 저장
                $encryptedData = CryptoHelper::encryptJson($menuData);
                CacheHelper::setCache($cacheKey, $encryptedData);
            } else {
                $menuData = [];
            }
        } else {
            // 캐시에서 복호화된 데이터를 가져옴
            $menuData = CryptoHelper::decryptJson($menuData);
        }

        return $menuData;
    }

    public function clearCache()
    {
        $cacheKey = 'menu_cache_' . $this->config_domain['cf_domain'];
        CacheHelper::clearCache($cacheKey);
    }
}