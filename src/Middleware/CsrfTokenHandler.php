<?php
// 파일 위치: src/Middleware/CsrfTokenHandler.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\SessionManager;

class CsrfTokenHandler
{
    private $sessionManager;

    /**
     * CSRF 토큰 핸들러 생성자.
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * CSRF 토큰을 생성 및 저장.
     */
    public function generateToken(string $key): string
    {
        $token = bin2hex(random_bytes(32));
        $this->sessionManager->set($key, $token);
        return $token;
    }

    /**
     * CSRF 토큰을 검증.
     * 검증 실패 시 JSON 응답을 반환하고 요청을 종료.
     */
    public function validateToken(string $token, string $key): void
    {
        $sessionToken = $this->sessionManager->get($key);

        if (empty($token) || empty($sessionToken)) {
            $this->sendErrorResponse('CSRF token validation failed: Token missing.');
        }

        if (!hash_equals($sessionToken, $token)) {
            $this->sendErrorResponse('CSRF token validation failed: Token mismatch.');
        }
    }

    /**
     * 에러 응답을 JSON으로 전송.
     * HTTP 상태 코드는 403 (Forbidden)으로 설정.
     */
    private function sendErrorResponse(string $message): void
    {
        header('Content-Type: application/json');
        http_response_code(403); // 403 Forbidden
        echo json_encode(['error' => $message]);
        exit;
    }
}