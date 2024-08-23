<?php

namespace Web\PublicHtml\Helper;

class SessionManager
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            // ���� �ɼ� ���� (��: secure, httpOnly �Ӽ�)
            session_start([
                'cookie_lifetime' => 0, // �������� ���� ������ ����
                'cookie_secure' => true, // HTTPS������ ����
                'cookie_httponly' => true, // JS���� ��Ű ���� ����
                'use_strict_mode' => true, // ���� Ż�븦 ����
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
        // ���� �����Ϳ� ��Ű�� �����ϰ� ���� ����
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
        session_regenerate_id(true); // true�� ���� �����͸� �����ϸ鼭 ID�� �����
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