<?php
// 파일 위치: src/Middleware/AuthMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\CryptoHelper;

class AuthMiddleware
{
    public static function handle($requestUri)
    {
        // 로그인이 필요한 경로 목록
        $protectedPaths = [
            // 이 배열에 로그인이 필요한 경로를 추가
        ];

        // 관리자 경로에 대한 요청인지 확인
        if (strpos($requestUri, '/admin') === 0) {
            self::startSession('admin_session'); // 관리자를 위한 세션 이름 사용
            self::handleAdminAuthentication($requestUri);
        } else {
            self::startSession('user_session'); // 사용자를 위한 세션 이름 사용

            // 로그인이 필요한 경로인지 확인
            if (in_array($requestUri, $protectedPaths)) {
                self::handleUserAuthentication($requestUri);
            }
        }
    }

    private static function startSession($sessionName)
    {
        if (session_status() === PHP_SESSION_NONE) {
            // 세션 옵션 설정
            session_name($sessionName);
            session_start([
                'cookie_lifetime' => 0, // 브라우저가 닫힐 때까지 유지
                'cookie_secure' => false, // HTTPS에서만 전송
                'cookie_httponly' => true, // JS에서 쿠키 접근 금지
                'use_strict_mode' => true, // 세션 탈취를 방지
            ]);
        }
    }

    private static function handleAdminAuthentication($requestUri)
    {
        $jwtToken = $_COOKIE['jwtToken'] ?? null;
        $refreshToken = $_COOKIE['refreshToken'] ?? null;

        // 토큰 검증 및 디코딩
        $decodedToken = $jwtToken ? CryptoHelper::verifyJwtToken($jwtToken) : null;

        // 액세스 토큰이 없거나, 만료되었거나, 유효하지 않은 경우
        if (!$decodedToken || $decodedToken['exp'] < time()) {
            if ($refreshToken && $decodedRefreshToken = CryptoHelper::verifyJwtToken($refreshToken, true)) {
                // 리프레시 토큰이 유효하다면 새로운 액세스 토큰 발급
                $newAccessTokenPayload = [
                    'mb_no' => $decodedRefreshToken['mb_no'],
                    'mb_id' => $decodedRefreshToken['mb_id'],
                    'mb_level' => $decodedRefreshToken['mb_level'],
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
            } else {
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

    private static function handleUserAuthentication($requestUri)
    {
        $jwtToken = $_COOKIE['jwtToken'] ?? null;
        $refreshToken = $_COOKIE['refreshToken'] ?? null;

        // 현재 요청이 로그인 페이지인지를 확인
        $isLoginPage = ($requestUri === '/auth/login');

        // 토큰 검증 및 디코딩
        $decodedToken = $jwtToken ? CryptoHelper::verifyJwtToken($jwtToken) : null;

        // 액세스 토큰이 없거나, 만료되었거나, 유효하지 않은 경우
        if (!$decodedToken || $decodedToken['exp'] < time()) {
            if ($refreshToken && $decodedRefreshToken = CryptoHelper::verifyJwtToken($refreshToken, true)) {
                // 리프레시 토큰이 유효하다면 새로운 액세스 토큰 발급
                $newAccessTokenPayload = [
                    'mb_no' => $decodedRefreshToken['mb_no'],
                    'mb_id' => $decodedRefreshToken['mb_id'],
                    'mb_level' => $decodedRefreshToken['mb_level'],
                    'nickName' => $decodedRefreshToken['nickName'] ?? '',
                    'is_admin' => $decodedRefreshToken['is_admin'] ?? 0,
                    'is_super' => $decodedRefreshToken['is_super'] ?? 0,
                ];

                // 새로운 액세스 토큰 생성
                $newAccessToken = CryptoHelper::generateJwtToken($newAccessTokenPayload);

                // 새로운 액세스 토큰을 쿠키에 저장
                setcookie('jwtToken', $newAccessToken, 0, '/'); // 세션 쿠키로 저장

                // 새로운 액세스 토큰을 사용해 사용자 권한을 다시 확인
                $decodedToken = CryptoHelper::verifyJwtToken($newAccessToken);
            } else {
                // 리프레시 토큰이 없거나, 유효하지 않다면 JWT 및 리프레시 토큰 삭제
                setcookie('jwtToken', '', time() - 3600, '/');
                setcookie('refreshToken', '', time() - 3600, '/');

                // 로그인 페이지로 리다이렉트 (이미 로그인 페이지로 요청이 아닌 경우에만)
                if (!$isLoginPage) {
                    header('Location: /auth/login');
                    exit;
                }
            }
        }

        // 유효한 토큰이 있는 경우 사용자 인증 정보를 세션에 저장
        if ($decodedToken) {
            $_SESSION['auth'] = [
                'mb_no' => $decodedToken['mb_no'],
                'mb_id' => $decodedToken['mb_id'],
                'mb_level' => $decodedToken['mb_level'] ?? 0,
                'nickName' => $decodedToken['nickName'] ?? '',
                'is_admin' => $decodedToken['is_admin'] ?? 0,
                'is_super' => $decodedToken['is_super'] ?? 0,
            ];
        }
    }
}