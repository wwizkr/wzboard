<?php
// /src/Helper/CacheHelper.php

namespace Web\PublicHtml\Helper;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheHelper
{
    protected static $cache;

    public static function initialize($subDirectory = '', $namespace = '', $lifetime = 3600)
    {
        // 동적으로 도메인에 따라 캐시 디렉토리 생성
        $cacheDirectory = __DIR__ . '/../../storage/cache/' . $subDirectory;

        if (!is_dir($cacheDirectory)) {
            if (!mkdir($cacheDirectory, 0777, true)) {
                die('Failed to create cache directories...');
            }
        }

        self::$cache = new FilesystemAdapter($namespace, $lifetime, $cacheDirectory);
    }

    public static function getCache($key)
    {
        $cacheItem = self::$cache->getItem($key);
        if (!$cacheItem->isHit()) {
            return null;
        }
        return $cacheItem->get();
    }

    public static function setCache($key, $data, $ttl = 3600)
    {
        $cacheItem = self::$cache->getItem($key);
        $cacheItem->set($data);
        $cacheItem->expiresAfter($ttl);
        self::$cache->save($cacheItem);
    }
}