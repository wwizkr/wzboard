<?php

namespace Web\PublicHtml\Helper;

class SessionManager
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            // 세션 옵션 설정 (예: secure, httpOnly 속성)
            session_start([
                'cookie_lifetime' => 0, // 브라우저가 닫힐 때까지 유지
                'cookie_secure' => true, // HTTPS에서만 전송
                'cookie_httponly' => true, // JS에서 쿠키 접근 금지
                'use_strict_mode' => true, // 세션 탈취를 방지
            ]);
        }
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
        // 세션 데이터와 쿠키를 삭제하고 세션 종료
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
        session_regenerate_id(true); // true는 세션 데이터를 유지하면서 ID를 재생성
    }

    public function generateCsrfToken(string $key): string
    {
        $token = bin2hex(random_bytes(32));
        $this->set($key, $token);
        return $token;
    }

    public function getCsrfToken(string $key): ?string
    {
        return $this->get($key);
    }
}