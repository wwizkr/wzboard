<?php
namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\SessionManager;

class CsrfTokenHandler
{
    private $sessionManager;
    private $tokenTtl;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function generateToken(string $key): array
    {
        return $this->sessionManager->generateCsrfToken($key);
    }

    public function validateToken(string $token, string $key): void
    {
        if (!$this->sessionManager->validateCsrfToken($token, $key)) {
            $this->sendErrorResponse('CSRF token validation failed.', 403);
        }
    }

    private function sendErrorResponse(string $message, int $statusCode): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit;
    }
}