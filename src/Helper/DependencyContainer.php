<?php
// 파일 위치: /home/web/public_html/src/Helper/DependencyContainer.php

namespace Web\PublicHtml\Helper;

class DependencyContainer
{
    private static $instance = null;
    private $container = [];

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function get($key)
    {
        return $this->container[$key] ?? null;
    }

    private function __clone() {}
    private function __wakeup() {}
}