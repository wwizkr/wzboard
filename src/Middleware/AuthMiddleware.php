<?php
// 파일 위치: src/Middleware/AuthMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\CryptoHelper;

class AuthMiddleware
{
    public static function handle($requestUri)
    {
        // 관리자 경로에 대한 요청인지 확인
        if (strpos($requestUri, '/admin') === 0) {
            $jwtToken = $_COOKIE['jwtToken'] ?? null;

            if (!$jwtToken || !CryptoHelper::verifyJwtToken($jwtToken)) {
                // 토큰이 없거나 유효하지 않은 경우 로그인 페이지로 리다이렉트 또는 에러 메시지 출력
                header('Location: /auth/login');
                exit;
            }

            // 토큰 검증 및 디코딩
            $decodedToken = CryptoHelper::verifyJwtToken($jwtToken);

            // 토큰 만료 여부 및 관리자 권한 확인
            if (!$decodedToken || $decodedToken['exp'] < time() || $decodedToken['is_admin'] == 0) {
                // 관리자 권한이 없거나, 토큰이 만료된 경우 홈페이지로 리다이렉트
                header('Location: /');
                exit;
            }
        }
    }
}
