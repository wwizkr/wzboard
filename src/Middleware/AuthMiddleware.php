<?php
// 파일 위치: src/Middleware/AuthMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Core\DependencyContainer;
class AuthMiddleware
{
    private $sessionManager;
    private $membersService;
    private $container;
    private $protectedPaths = [
        '/admin',
        '/mypage',
        // 다른 보호된 페이지 경로를 여기에 추가
    ];

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $container->get('SessionManager');
        $this->membersService = $container->get('MembersService');
    }

    public function handle($uri)
    {
        // 모든 페이지에서 로그인 상태를 확인합니다.
        $this->checkAuthStatus();
        // 로그인이 필요한 페이지에 대해서만 로그인 여부를 검증
        if ($this->isProtectedPath($uri)) {
            $this->validateProtectedAccess($uri);
        }
    }

    private function checkAuthStatus()
    {
        $jwtToken = CookieManager::get('jwtToken');
        $refreshToken = CookieManager::get('refreshToken');
        $auth = $this->sessionManager->get('auth');

        if ($jwtToken && CryptoHelper::verifyJwtToken($jwtToken)) {
            // JWT 토큰이 유효한 경우
            if (!$auth) {
                $decodedToken = CryptoHelper::verifyJwtToken($jwtToken);
                $this->createAuthSession($decodedToken);
            }
        } elseif ($refreshToken) {
            // JWT 토큰이 없거나 유효하지 않지만 리프레시 토큰이 있는 경우
            $decodedRefreshToken = CryptoHelper::verifyJwtToken($refreshToken);
            
            if ($decodedRefreshToken) {
                // 리프레시 토큰이 유효한 경우, 새로운 JWT 토큰 생성
                $member = $this->membersService->getMemberDataById($decodedRefreshToken['mb_id']);
                $level = $this->membersService->getMemberLevelData($member['member_level']) ?? [];
                
                $payload = [
                    'mb_no' => $member['mb_no'],
                    'mb_id' => $member['mb_id'],
                    'mb_level' => $member['member_level'],
                    'nickName' => $member['nickName'],
                    'is_admin' => $level['is_admin'] ?? false,
                    'is_super' => $level['is_super'] ?? false,
                ];
                
                $newJwtToken = CryptoHelper::generateJwtToken($payload);
                CookieManager::set('jwtToken', $newJwtToken);
                
                $this->createAuthSession($payload);
            } else {
                // 리프레시 토큰이 유효하지 않은 경우
                CookieManager::delete('refreshToken');
                $this->sessionManager->delete('auth');
            }
        } elseif ($auth) { // 비로그인 상태
            // JWT 토큰과 리프레시 토큰이 모두 없지만 auth 세션이 있는 경우
            $this->sessionManager->delete('auth');
        }
    }

    private function isProtectedPath($uri)
    {
        foreach ($this->protectedPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }
        return false;
    }

    private function createAuthSession($decodedToken)
    {
        $this->sessionManager->set('auth', [
            'mb_no' => $decodedToken['mb_no'],
            'mb_id' => $decodedToken['mb_id'],
            'mb_level' => $decodedToken['mb_level'] ?? 0,
            'nickName' => $decodedToken['nickName'] ?? '',
            'is_admin' => $decodedToken['is_admin'] ?? 0,
            'is_super' => $decodedToken['is_super'] ?? 0,
        ]);
    }

    private function validateProtectedAccess($uri)
    {
        $auth = $this->sessionManager->get('auth');
        if (!$auth) {
            header('Location: /auth/login');
            //exit;
        }
        // 관리자 페이지에 대한 추가 검증
        if (strpos($uri, '/admin') === 0 && (!$auth['is_admin'] && !$auth['is_super'])) {
            header('Location: /');
            //exit;
        }
    }
}