<?php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CryptoHelper;
use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\AuthService;
use Web\PublicHtml\Service\MembersService;

class SocialController
{
    private $container;
    private $config_domain;
    private $sessionManager;
    private $membersModel;
    private $membersService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->membersModel = $this->container->get('MembersModel');
        $this->membersService = $this->container->get('MembersService');
    }

    public function getSocialConfig(): array
    {
        $callbackUrl = $_ENV['APP_URL'] . '/social/callback?provider=';
        $providers = $this->config_domain['cf_social_servicelist'] ? explode(",", $this->config_domain['cf_social_servicelist']) : [];
        
        $socialConfig = [
            'callback' => '',
            'debug_mode' => false,
            'debug_file' => dirname(__DIR__, 2) . '/storage/social_log.log',
            'providers' => [],
        ];

        foreach ($providers as $provider) {
            $providerName = ucfirst(strtolower($provider));
            $socialConfig['providers'][$providerName] = $this->getProviderConfig($provider, $callbackUrl);
        }

        return $socialConfig;
    }

    private function getProviderConfig(string $provider, string $callbackUrl): array
    {
        $baseConfig = [
            'enabled' => true,
            'keys' => [
                'id' => $this->config_domain["cf_{$provider}_clientid"],
                'secret' => $this->config_domain["cf_{$provider}_secret"],
            ],
            'callback' => $callbackUrl . ucfirst(strtolower($provider)),
        ];

        if ($provider === 'naver') {
            $baseConfig['wrapper'] = [
                'path' => '/home/web/public_html/vendor/hybridauth/hybridauth/src/Provider/Naver.php',
                'class' => 'Hybridauth\\Provider\\Naver',
            ];
        }

        return $baseConfig;
    }

    public function login(array $vars): void
    {
        try {
            $providerName = ucfirst(strtolower($vars['param'] ?? 'Naver'));
            $this->validateProvider($providerName);

            $hybridauth = new Hybridauth($this->getSocialConfig());
            $adapter = $hybridauth->authenticate($providerName);

            if ($adapter->isConnected()) {
                $userProfile = $adapter->getUserProfile();
                $this->handleUserLogin($providerName, $userProfile);
                $adapter->disconnect();
            } else {
                echo '사용자가 연결되지 않았습니다.';
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    public function callback(): void
    {
        try {
            $providerName = $_GET['provider'] ?? '';
            $this->validateProvider($providerName);

            $hybridauth = new Hybridauth($this->getSocialConfig());
            $adapter = $hybridauth->authenticate($providerName);

            if ($adapter->isConnected()) {
                $userProfile = $adapter->getUserProfile();
                $this->handleUserLogin($providerName, $userProfile);
                $adapter->disconnect();
            } else {
                echo '사용자가 연결되지 않았습니다.';
            }
        } catch (Exception $e) {
            echo '콜백 처리 중 오류 발생: ' . $e->getMessage();
        }
    }

    private function validateProvider(string $providerName): void
    {
        if (empty($this->getSocialConfig()['providers'][$providerName])) {
            throw new Exception('Unknown Provider: ' . $providerName);
        }
    }

    private function handleUserLogin(string $providerName, $userProfile): void
    {
        $isMember = $this->membersModel->findBySocialId($providerName, $userProfile->identifier);

        if (!empty($isMember)) {
            $level = $this->membersService->getMemberLevelData($isMember['member_level']) ?? [];
            $authService = $this->container->get('AuthService');
            $authService->login($isMember, $level);
        } else {
            $encryptedProfile = CryptoHelper::encryptJson((array)$userProfile);
            
            // 회원 가입 완료 후 세션 삭제
            $this->sessionManager->set('encrypted_social_profile', $encryptedProfile);
            $this->sessionManager->set('social_provider', $providerName);
            header('Location: /member/register/join');
            exit;
        }
    }

    public function getProviderList(): array
    {
        return array_keys(array_filter($this->getSocialConfig()['providers'], function($settings) {
            return !empty($settings['enabled']);
        }));
    }
}