<?php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;

class AuthService
{
    private $container;
    private $sessionManager;
    private $cookieManager;
    private $cryptoHelper;
    private $membersService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $container->get('SessionManager');
        $this->cookieManager = $container->get('CookieManager');
        $this->cryptoHelper = $container->get('CryptoHelper');

        // Lazy loading으로 변경
        $this->membersService = null;
    }

    private function getMembersService()
    {
        if ($this->membersService === null) {
            $this->membersService = $this->container->get('MembersService');
        }
        return $this->membersService;
    }

    public function validateAuth(): void
    {
        $jwtToken = $this->cookieManager->get('jwtToken');
        $refreshToken = $this->cookieManager->get('refreshToken');
        $auth = $this->sessionManager->get('auth');

        if ($jwtToken && $this->cryptoHelper->verifyJwtToken($jwtToken)) {
            if (!$auth) {
                $decodedToken = $this->cryptoHelper->verifyJwtToken($jwtToken);
                $this->createAuthSession($decodedToken);
            }
        } elseif ($refreshToken) {
            $this->handleRefreshToken($refreshToken);
        } elseif ($auth) {
            $this->sessionManager->delete('auth');
        }
    }

    public function getCurrentUser()
    {
        $auth = $this->sessionManager->get('auth');
        if (!$auth) {
            return null;
        }

        $membersService = $this->getMembersService();
        $member = $membersService->getMemberDataById($auth['mb_id']);
        $levelData = $membersService->getMemberLevelData($auth['mb_level']) ?? [];
        $level = $levelData[0] ?? [];
        unset($member['password']);

        return [
            'mb_no' => $auth['mb_no'],
            'cf_class' => $auth['cf_class'],
            'mb_id' => $auth['mb_id'],
            'mb_level' => $auth['mb_level'],
            'nickName' => $auth['nickName'],
            'is_admin' => $auth['is_admin'],
            'is_super' => $auth['is_super'],
            'member_data' => $member,
            'level_data' => $level,
        ];
    }

    public function login(array $memberData, array $level): void
    {
        $isAdmin = $level['is_admin'] ?? false;
        $isSuper = $level['is_super'] ?? false;

        echo '<pre>';
        var_dump($level);
        echo '</pre>';
        
        $payload = [
            'mb_no' => $memberData['mb_no'],
            'cf_class' => $memberData['cf_class'],
            'mb_id' => $memberData['mb_id'],
            'mb_level' => $memberData['member_level'],
            'nickName' => $memberData['nickName'],
            'is_admin' => $isAdmin,
            'is_super' => $isSuper,
        ];
        
        $jwtToken = $this->cryptoHelper->generateJwtToken($payload);
        $refreshTokenPayload = array_merge($payload, ['type' => 'refresh']);
        $refreshToken = $this->cryptoHelper->generateJwtToken($refreshTokenPayload, 60 * 60 * 24 * 30);
        
        $this->cookieManager->set('jwtToken', $jwtToken);
        $this->cookieManager->set('refreshToken', $refreshToken, time() + (60 * 60 * 24 * 30));
        
        $this->createAuthSession($payload);
        
        if ($isAdmin) {
            $this->sessionManager->generateCsrfToken($_ENV['ADMIN_CSRF_TOKEN_KEY']);
        }
        
        $this->redirect($isAdmin);
    }

    public function logout($url = ''): void
    {
        $this->sessionManager->destroy();
        $this->cookieManager->delete('jwtToken');
        $this->cookieManager->delete('refreshToken');
        
        if ($url) {
            header('Location: ' . $url);
        } else {
            header('Location: /');
        }
        exit();
    }

    private function handleRefreshToken($refreshToken): void
    {
        $decodedRefreshToken = $this->cryptoHelper->verifyJwtToken($refreshToken);
        if (!$decodedRefreshToken) {
            $this->cookieManager->delete('refreshToken');
            $this->sessionManager->delete('auth');
            return;
        }

        $membersService = $this->getMembersService();
        $member = $membersService->getMemberDataById($decodedRefreshToken['mb_id']);
        $level = $membersService->getMemberLevelData($member['member_level']) ?? [];
        
        $payload = [
            'mb_no' => $member['mb_no'],
            'cf_class' => $member['cf_class'],
            'mb_id' => $member['mb_id'],
            'mb_level' => $member['member_level'],
            'nickName' => $member['nickName'],
            'is_admin' => $level['is_admin'] ?? false,
            'is_super' => $level['is_super'] ?? false,
        ];
        
        $newJwtToken = $this->cryptoHelper->generateJwtToken($payload);
        $this->cookieManager->set('jwtToken', $newJwtToken);
        $this->createAuthSession($payload);
    }

    private function createAuthSession($decodedToken): void
    {
        $this->sessionManager->set('auth', [
            'mb_no' => $decodedToken['mb_no'],
            'cf_class' => $decodedToken['cf_class'],
            'mb_id' => $decodedToken['mb_id'],
            'mb_level' => $decodedToken['mb_level'] ?? 0,
            'nickName' => $decodedToken['nickName'] ?? '',
            'is_admin' => $decodedToken['is_admin'] ?? 0,
            'is_super' => $decodedToken['is_super'] ?? 0,
        ]);
    }

    private function redirect(bool $isAdmin): void
    {
        header('Location: ' . ($isAdmin ? '/admin/dashboard' : '/'));
        exit();
    }
}