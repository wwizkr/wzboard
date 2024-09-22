<?php
namespace Web\PublicHtml\Api\v1;

use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class TokenController
{
    protected $sessionManager;
    protected $csrfTokenHandler;

    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->csrfTokenHandler = new CsrfTokenHandler($this->sessionManager);
    }

    public function getUserCsrfToken()
    {
        $csrfTokenKey = $_ENV['USER_CSRF_TOKEN_KEY'] ?? 'user_secure_key';
        $tokenData = $this->sessionManager->get($csrfTokenKey);
        
        if (!$tokenData || time() > ($tokenData['expiry'] ?? 0)) {
            $tokenData = $this->csrfTokenHandler->generateToken($csrfTokenKey);
        }

        echo json_encode([
            'token' => $tokenData['token'],
            'tokenKey' => $csrfTokenKey,
            'expiresAt' => $tokenData['expiry'],
        ]);
        
        exit;
    }
}