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
            $refreshToken = $_COOKIE['refreshToken'] ?? null;

            // 토큰 검증 및 디코딩
            $decodedToken = $jwtToken ? CryptoHelper::verifyJwtToken($jwtToken) : null;

            // 액세스 토큰이 없거나, 만료되었거나, 유효하지 않은 경우
            if (!$decodedToken || $decodedToken['exp'] < time()) {
                // 리프레시 토큰이 있는지 확인하고, 유효한지 검사
                if ($refreshToken && $decodedRefreshToken = CryptoHelper::verifyJwtToken($refreshToken, true)) {
                    // 리프레시 토큰이 유효하다면 새로운 액세스 토큰 발급
                    $newAccessTokenPayload = [
                        'mb_no' => $decodedRefreshToken['mb_no'],
                        'mb_id' => $decodedRefreshToken['mb_id'],
                        'mb_level' => $decodedRefreshToken['member_level'],
                        'nickName' => $decodedRefreshToken['nickName'] ?? '',
                        'is_admin' => $decodedRefreshToken['is_admin'] ?? 0,
                        'is_super' => $decodedRefreshToken['is_super'] ?? 0,
                    ];

                    // 새로운 액세스 토큰 생성
                    $newAccessToken = CryptoHelper::generateJwtToken($newAccessTokenPayload);

                    // 새로운 액세스 토큰을 쿠키에 저장
                    setcookie('jwtToken', $newAccessToken, 0, '/'); // 세션 쿠키로 저장

                    // 새로운 액세스 토큰을 사용해 관리자 권한을 다시 확인
                    $decodedToken = CryptoHelper::verifyJwtToken($newAccessToken);
                    echo '<pre>';
                    var_dump('새 토큰 발급성공');
                    echo '</pre>';
                    echo '<pre>';
                    var_dump($decodedToken);
                    echo '</pre>';
                } else {
                    echo '<pre>';
                    var_dump($refreshToken);
                    echo '</pre>';

                    echo '<pre>';
                    var_dump(CryptoHelper::verifyJwtToken($refreshToken));
                    echo '</pre>';

                    echo '<pre>';
                    var_dump('새 토큰 발급실패');
                    echo '</pre>';
                    exit;
                    // 리프레시 토큰이 없거나, 유효하지 않다면 JWT 및 리프레시 토큰 삭제
                    setcookie('jwtToken', '', time() - 3600, '/');
                    setcookie('refreshToken', '', time() - 3600, '/');
                    // 로그인 페이지로 리다이렉트
                    if ($requestUri !== '/auth/login') {
                        header('Location: /auth/login');
                    }
                    exit;
                }
            }

            // 유효한 토큰이 있는 경우 최소한의 인증 정보만 세션에 저장
            if ($decodedToken) {
                $_SESSION['auth'] = [
                    'mb_no' => $decodedToken['mb_no'],
                    'is_admin' => $decodedToken['is_admin'] ?? 0,
                    'is_super' => $decodedToken['is_super'] ?? 0,
                ];
            }

            if (!$decodedToken || $decodedToken['is_admin'] == 0) {
                // 관리자 권한이 없으면 홈페이지로 리다이렉트
                header('Location: /');
                exit;
            }
        }
    }
}
