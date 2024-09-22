<?php
namespace Web\PublicHtml\Helper;

class SessionManager
{
    private $csrfTokenTtl;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 0,
                'cookie_secure' => false, // 프로덕션에서는 true로 설정
                'cookie_httponly' => true,
                'use_strict_mode' => true,
            ]);
        }
        $this->csrfTokenTtl = (int)($_ENV['CSRF_TOKEN_TTL'] ?? 3600); // 기본값 1시간
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function regenerateSessionId(): void
    {
        session_regenerate_id(true);
    }

    public function generateCsrfToken(string $key): array
    {
        $token = bin2hex(random_bytes(32));
        $expiryTime = time() + $this->csrfTokenTtl;
        $tokenData = [
            'token' => $token,
            'expiry' => $expiryTime
        ];
        $this->set($key, $tokenData);
        return $tokenData;
    }

    public function getCsrfToken(string $key): ?array
    {
        $tokenData = $this->get($key);
        if (!is_array($tokenData) || !isset($tokenData['token']) || !isset($tokenData['expiry'])) {
            return null;
        }
        if (time() > $tokenData['expiry']) {
            $this->set($key, null); // 만료된 토큰 제거
            return null;
        }
        return $tokenData;
    }

    public function validateCsrfToken(string $token, string $key): bool
    {
        $storedTokenData = $this->getCsrfToken($key);
        if (!$storedTokenData) {
            return false;
        }
        return hash_equals($storedTokenData['token'], $token);
    }
}