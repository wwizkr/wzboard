<?php
//파일위치 src/Controller/AuthController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CryptoHelper;

class AuthController
{
    protected $container;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $sessionManager;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->sessionManager = $this->container->get('session_manager');
        $this->membersHelper = new MembersHelper($this->container, $this->membersModel);
    }

    // 로그인
    public function login($vars)
    {
        $configDomain = $this->container->get('config_domain');
        $contentSkin = $configDomain['cf_skin_content'] ?? 'basic';
        $viewPath = 'Content/'.$contentSkin.'/Auth/login_form';

        $jwtToken = $_COOKIE['jwtToken'] ?? null;
        $refreshToken = $_COOKIE['refreshToken'] ?? null;

        // 인증 토큰 유효성 검사
        if ($jwtToken && $decodedJwtToken = CryptoHelper::verifyJwtToken($jwtToken)) {
            // 인증 토큰이 유효한 경우
            if ($decodedJwtToken['is_admin']) {
                header('Location: /admin/dashboard'); // 관리 페이지로 리다이렉트
            } else {
                header('Location: /'); // 일반 사용자 대시보드로 리다이렉트
            }
            exit();
        } elseif ($refreshToken && $decodedRefreshToken = CryptoHelper::verifyJwtToken($refreshToken)) {
            // 리프레시 토큰이 유효한 경우 새로운 JWT 토큰 생성
            $member = $this->membersHelper->getMemberDataById($decodedRefreshToken['mb_id']);
            $level  = $this->membersHelper->getMemberLevelData($member['member_level']) ?? 0;

            // 새로운 인증 토큰 생성
            $payload = [
                'mb_no' => $member['mb_no'],
                'mb_id' => $member['mb_id'],
                'mb_level' => $member['member_level'],
                'nickName' => $member['nickName'],
                'is_admin' => $level['is_admin'],
                'is_super' => $level['is_super'],
            ];
            $newJwtToken = CryptoHelper::generateJwtToken($payload);
            setcookie('jwtToken', $newJwtToken, 0, '/'); // 새로운 JWT 토큰을 쿠키에 저장

            // 대시보드로 리다이렉트
            if ($level['is_admin']) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /');
            }
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $viewData = [];
            return [
                "viewPath" => $viewPath,
                "viewData" => $viewData,
                "fullPage" => true,
            ];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 로그인 처리
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $member = $this->membersHelper->getMemberDataById($email);
            $level  = $this->membersHelper->getMemberLevelData($member['member_level']) ?? 0;

            // 비밀번호 검증
            if ($member && CryptoHelper::verifyPassword($password, $member['password'])) {
                // JWT 토큰 생성
                $payload = [
                    'mb_no' => $member['mb_no'],
                    'mb_id' => $member['mb_id'],
                    'mb_level' => $member['member_level'],
                    'nickName' => $member['nickName'],
                    'is_admin' => $level['is_admin'],
                    'is_super' => $level['is_super'],
                ];
                $jwtToken = CryptoHelper::generateJwtToken($payload);
                
                // 리프레시 토큰에도 필요한 정보를 포함
                $refreshTokenPayload = $payload;
                $refreshTokenPayload['type'] = 'refresh';
                $refreshToken = CryptoHelper::generateJwtToken($refreshTokenPayload, 60 * 60 * 24 * 30);

                // JWT 토큰을 쿠키에 저장
                setcookie('jwtToken', $jwtToken, 0, '/');
                setcookie('refreshToken', $refreshToken, time() + (60 * 60 * 24 * 30), '/');

                // 관리자 권한이 있는 경우 관리자용 CSRF 토큰 생성
                if ($level['is_admin']) {
                    $this->sessionManager->generateCsrfToken($_ENV['ADMIN_CSRF_TOKEN_KEY']);
                    header('Location: /admin/dashboard'); // 관리 페이지로 리다이렉트
                } else {
                    header('Location: /'); // 일반 사용자 대시보드로 리다이렉트
                }
                exit();
            } else { // 로그인 실패
                $viewData = ['error' => 'Invalid email or password', 'email' => $email];
                return [
                    "viewPath" => $viewPath,
                    "viewData" => $viewData
                ];
            }
        }
    }

    // 로그아웃 처리
    public function logout($vars)
    {
        // 세션 파괴
        $this->sessionManager->destroy();
        // 쿠키 삭제
        setcookie('jwtToken', '', time() - 3600, '/');
        setcookie('refreshToken', '', time() - 3600, '/');
        header('Location: /login'); // 로그아웃 후 로그인 페이지로 리다이렉트
        exit();
    }
}