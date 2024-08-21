<?php
//파일위치 src/Controller/AuthController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Model\MemberModel;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CryptoHelper;

class AuthController
{
    protected $container;
    protected $memberModel;
    protected $session;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->memberModel = new MemberModel($container);
        $this->session = new SessionManager(); //세션 사용안함.
    }

    // 로그인
    public function login($vars)
    {
        $configDomain = $this->container->get('config_domain');
        $contentSkin = $configDomain['cf_skin_content'] ?? 'basic';
        $viewPath = 'Content/'.$contentSkin.'/Auth/login_form';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // 로그인 폼을 보여줌
            $viewData = [];
            return [$viewPath, $viewData];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 로그인 처리
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $member = $this->memberModel->getMemberData($email);
            $level  = $this->memberModel->getMemberLevelData($member['member_level']) ?? 0;

            // 비밀번호 검증
            if ($member && CryptoHelper::verifyPassword($password, $member['password'])) {
                // JWT 토큰 생성
                $payload = [
                    'mb_no' => $member['mb_no'],
                    'mb_id' => $member['mb_id'],
                    'nickName' => $member['nickName'],
                    'is_admin' => $level['is_admin'],
                    'is_super' => $level['is_super_admin'],
                ];
                // JWT 토큰 생성 (유효시간은 토큰 자체에서 관리)
                $jwtToken = CryptoHelper::generateJwtToken($payload);
                // JWT 토큰을 쿠키에 저장 (유효시간은 토큰에 의해 관리되므로 쿠키 유효시간은 설정하지 않음)
                setcookie('jwtToken', $jwtToken, 0, '/'); // 0은 브라우저 종료 시 쿠키 삭제
                // 로그인 성공 후 리다이렉트
                header('Location: /');
            } else { // 로그인 실패
                $viewData = ['error' => 'Invalid email or password'];
                return [$viewPath, $viewData];
            }
        }
    }

    // 로그아웃 처리
    public function logout($vars)
    {
        // 세션 파괴
        $this->session->destroy();
        header('Location: /');
        exit;
    }
}