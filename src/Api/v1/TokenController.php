<?php
namespace Web\PublicHtml\Api\V1;

use Web\PublicHtml\Helper\SessionManager;

class TokenController
{
    protected $sessionManager;

    public function __construct()
    {
        // 세션 매니저 인스턴스를 생성하거나 가져옵니다.
        $this->sessionManager = new SessionManager();
    }

    /**
     * CSRF 토큰을 반환합니다.
     */
    public function getUserCsrfToken()
    {
        // 세션에서 CSRF 토큰 키를 정의합니다.
        $csrfTokenKey = $_ENV['USER_CSRF_TOKEN_KEY'];

        // 세션에 저장된 CSRF 토큰이 있는지 확인합니다.
        $csrfToken = $this->sessionManager->get($csrfTokenKey);

        // CSRF 토큰이 없으면 새로운 토큰을 생성하고 세션에 저장합니다.
        if (!$csrfToken) {
            $csrfToken = $this->sessionManager->generateCsrfToken($csrfTokenKey);
        }

        // JSON 형식으로 CSRF 토큰을 반환합니다.
        echo json_encode([
            'token' => $csrfToken,
            'tokenKey' => $csrfTokenKey,
            'sessionToken' => $csrfToken,
        ]);
        
        exit;
    }
}