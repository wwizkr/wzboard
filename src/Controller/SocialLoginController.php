<?php

namespace Web\PublicHtml\Controller;

use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception;

class SocialLoginController
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/social.php';
    }

    public function login($providerName)
    {
        try {
            $hybridauth = new Hybridauth($this->config);
            $adapter = $hybridauth->authenticate($providerName);
            $userProfile = $adapter->getUserProfile();
            
            // 사용자 프로필 정보 사용
            echo 'Hello, ' . $userProfile->displayName;

            // 로그인 후 처리 로직 (세션 설정, DB 저장 등)
            $adapter->disconnect();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function callback()
    {
        // 콜백 로직은 Hybridauth에 의해 자동 처리됩니다.
    }
}