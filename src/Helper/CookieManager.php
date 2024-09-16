<?php

namespace Web\PublicHtml\Helper;

class CookieManager
{
    // 기본값 설정
    private static $defaultExpiry = 0; // 0: 브라우저 종료 시까지
    private static $defaultPath = '/'; // 기본 경로는 루트
    private static $defaultDomain = ''; // 도메인은 기본적으로 현재 도메인
    private static $defaultSecure = false; // HTTPS에서만 전송 여부
    private static $defaultHttpOnly = true; // JavaScript에서 쿠키 접근 금지

    public static function set(
        string $key, 
        string $value, 
        int $expiry = null, 
        string $path = null, 
        string $domain = null, 
        bool $secure = null, 
        bool $httponly = null
    ): void {
        // 기본값 사용
        $expiry = $expiry ?? self::$defaultExpiry;
        $path = $path ?? self::$defaultPath;
        $domain = $domain ?? self::$defaultDomain;
        $secure = $secure ?? self::$defaultSecure;
        $httponly = $httponly ?? self::$defaultHttpOnly;

        setcookie($key, $value, [
            'expires' => $expiry,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly
        ]);
    }

    public static function get(string $key): ?string
    {
        return $_COOKIE[$key] ?? null;
    }

    public static function delete(
        string $key, 
        string $path = null, 
        string $domain = null, 
        bool $secure = null, 
        bool $httponly = null
    ): void {
        // 기본값 사용
        $path = $path ?? self::$defaultPath;
        $domain = $domain ?? self::$defaultDomain;
        $secure = $secure ?? self::$defaultSecure;
        $httponly = $httponly ?? self::$defaultHttpOnly;

        setcookie($key, '', time() - 3600, $path, $domain, $secure, $httponly);
        unset($_COOKIE[$key]);
    }
}