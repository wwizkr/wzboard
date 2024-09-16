<?php
// /src/Helper/ConfigHelper.php
namespace Web\PublicHtml\Helper;

class ConfigHelper
{
    private static $configs = [];

    /**
     * 설정값을 등록합니다.
     */
    public static function setConfig(string $configName, array $config)
    {
        self::$configs[$configName] = $config;
    }

    /**
     * 특정 설정값을 가져옵니다.
     */
    public static function getConfig(string $configName, string $key = null, $default = null)
    {
        if (!isset(self::$configs[$configName])) {
            return $default;
        }
        if ($key === null) {
            return self::$configs[$configName];
        }
        return self::$configs[$configName][$key] ?? $default;
    }

    /**
     * 설정값을 초기화합니다.
     */
    public static function resetConfig()
    {
        self::$configs = [];
    }
}