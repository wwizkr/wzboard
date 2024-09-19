<?php
// 파일 위치: src/Middleware/AuthMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CryptoHelper;

class AuthMiddleware
{
    private static $sessionManager;
    private static $protectedPaths = [
        '/admin',
        '/mypage',
        // 다른 보호된 페이지 경로를 여기에 추가
    ];

    public static function handle($uri)
    {
        self::$sessionManager = new SessionManager();

        // 모든 페이지에서 로그인 상태를 확인합니다.
        self::checkAuthStatus();

        // 보호된 페이지에 대한 추가 검증
        if (self::isProtectedPath($uri)) {
            self::validateProtectedAccess($uri);
        }
    }

    private static function checkAuthStatus()
    {
        $jwtToken = CookieManager::get('jwtToken');
        $auth = self::$sessionManager->get('auth');

        // JWT 토큰이 유효하고 auth 세션이 없는 경우에만 세션을 생성합니다.
        if ($jwtToken && !$auth) {
            $decodedToken = CryptoHelper::verifyJwtToken($jwtToken);
            if ($decodedToken) {
                self::createAuthSession($decodedToken);
            } else {
                // 토큰이 유효하지 않은 경우 쿠키를 삭제합니다.
                CookieManager::delete('jwtToken');
                CookieManager::delete('refreshToken');
            }
        } elseif (!$jwtToken && $auth) {
            // JWT 토큰이 없지만 auth 세션이 있는 경우, 세션을 삭제합니다.
            self::$sessionManager->delete('auth');
        }
    }

    private static function isProtectedPath($uri)
    {
        foreach (self::$protectedPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }
        return false;
    }

    private static function createAuthSession($decodedToken)
    {
        self::$sessionManager->set('auth', [
            'mb_no' => $decodedToken['mb_no'],
            'mb_id' => $decodedToken['mb_id'],
            'mb_level' => $decodedToken['mb_level'] ?? 0,
            'nickName' => $decodedToken['nickName'] ?? '',
            'is_admin' => $decodedToken['is_admin'] ?? 0,
            'is_super' => $decodedToken['is_super'] ?? 0,
        ]);
    }

    private static function validateProtectedAccess($uri)
    {
        $auth = self::$sessionManager->get('auth');
        if (!$auth) {
            header('Location: /auth/login');
            exit;
        }

        // 관리자 페이지에 대한 추가 검증
        if (strpos($uri, '/admin') === 0 && (!$auth['is_admin'] && !$auth['is_super'])) {
            header('Location: /');
            exit;
        }
    }
}