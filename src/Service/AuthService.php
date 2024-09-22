<?php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\SessionManager;

class AuthService
{
    private $container;
    private $sessionManager;
    private $cookieManager;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $container->get('SessionManager');
        $this->cookieManager = $container->get('CookieManager');
    }

    public function login(array $memberData, array $level): void
    {
        $isAdmin = $level['is_admin'] ?? false;
        $isSuper = $level['is_super'] ?? false;

        $payload = [
            'mb_no' => $memberData['mb_no'],
            'mb_id' => $memberData['mb_id'],
            'mb_level' => $memberData['member_level'],
            'nickName' => $memberData['nickName'],
            'is_admin' => $isAdmin,
            'is_super' => $isSuper,
        ];

        $jwtToken = CryptoHelper::generateJwtToken($payload);
        
        $refreshTokenPayload = $payload;
        $refreshTokenPayload['type'] = 'refresh';
        $refreshToken = CryptoHelper::generateJwtToken($refreshTokenPayload, 60 * 60 * 24 * 30);

        $this->cookieManager->set('jwtToken', $jwtToken);
        $this->cookieManager->set('refreshToken', $refreshToken, time() + (60 * 60 * 24 * 30));

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
            header('Location: '.$url);
        } else {
            header('Location: /');
            exit();
        }
    }

    private function redirect(bool $isAdmin): void
    {
        if ($isAdmin) {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /');
        }
        exit();
    }
}