<?php
// 파일 위치: /home/web/public_html/src/Helper/DependencyContainer.php

namespace Web\PublicHtml\Helper;

class DependencyContainer
{
    private static $instance = null;
    private $container = [];

    // 싱글톤 패턴을 위한 private 생성자
    private function __construct() {}

    // 싱글톤 인스턴스 반환
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 의존성 등록
    public function set($key, $value)
    {
        // 클로저 또는 값으로 등록 가능
        $this->container[$key] = $value;
    }

    // 의존성 가져오기
    public function get($key)
    {
        // 등록된 의존성이 있는지 확인
        if (!isset($this->container[$key])) {
            return null;
        }

        // 등록된 값이 클로저일 경우 실행하여 인스턴스 반환
        if (is_callable($this->container[$key])) {
            return $this->container[$key]($this);
        }

        // 그렇지 않으면 값 반환
        return $this->container[$key];
    }

    // 복제 및 unserialize 방지
    private function __clone() {}
    public function __wakeup() {}
}