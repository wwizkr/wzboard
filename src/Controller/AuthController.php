<?php
//파일위치 src/Controller/AuthController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;

use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Service\AuthService;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\Helper\CryptoHelper;

use Web\PublicHtml\Controller\SocialController;

class AuthController
{
    protected $container;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $sessionManager;
    protected $socialController;
    private $componentsViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->membersModel = $this->container->get('MembersModel');
        $this->membersService = $this->container->get('MembersService');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->membersHelper = $this->container->get('MembersHelper');
        $this->socialController = $this->container->get('SocialController');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    // 로그인
    public function login($vars)
    {
        $configDomain = $this->container->get('config_domain');
        $contentSkin = $configDomain['cf_skin_content'] ?? 'basic';
        $viewPath = 'Content/'.$contentSkin.'/Auth/login_form';

        $jwtToken = CookieManager::get('jwtToken');
        $refreshToken = CookieManager::get('refreshToken');

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
            CookieManager::set('jwtToken', $newJwtToken); // 새로운 JWT 토큰을 쿠키에 저장

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
            $socialProvider = $this->socialController->getProviderList();
            $socialItems = !empty($socialProvider) ? $this->componentsViewHelper->renderComponent('socialItems', $socialProvider, 'login') : '';
            $viewData['socialProvider'] = $socialItems;

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

            // 비밀번호 검증
            if ($member && CryptoHelper::verifyPassword($password, $member['password'])) {
                $level = $this->membersHelper->getMemberLevelData($member['member_level']) ?? [];
                $authService = $this->container->get('AuthService');
                $authService->login($member, $level);
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
        $authService = $this->container->get('AuthService');
        $authService->logout();
    }
}