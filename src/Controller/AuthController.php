<?php
//파일위치 src/Controller/AuthController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;

/**
 * 인증 관련 기능을 처리하는 컨트롤러 클래스
 */
class AuthController
{
    protected $container;
    
    /**
     * AuthController 생성자
     * 
     * @param DependencyContainer $container 의존성 컨테이너
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

     /**
     * 로그인 처리를 담당하는 메소드
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array|void 뷰 데이터 또는 리다이렉트
     */
    public function login($vars)
    {
        // 필요한 서비스와 설정 로드
        $config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $membersService = $this->container->get('MembersService');
        $socialController = $this->container->get('SocialController');
        $componentsViewHelper = $this->container->get('ComponentsViewHelper');
        $cookieManager = $this->container->get('CookieManager');
        $cryptoHelper = $this->container->get('CryptoHelper');
        
        // 뷰 경로 설정
        $contentSkin = $config_domain['cf_skin_content'] ?? 'basic';
        $viewPath = 'Content/'.$contentSkin.'/Auth/login_form';

        // 토큰 확인
        $jwtToken = $cookieManager->get('jwtToken');
        $refreshToken = $cookieManager->get('refreshToken');

        // JWT 토큰 유효성 검사
        if ($jwtToken && $decodedJwtToken = $cryptoHelper->verifyJwtToken($jwtToken)) {
            // 관리자/일반 사용자 분기 처리
            if ($decodedJwtToken['is_admin']) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /');
            }
            exit();
        } else if ($refreshToken && $decodedRefreshToken = $cryptoHelper->verifyJwtToken($refreshToken)) { // 리프레시 토큰 유효성 검사
            // 새로운 JWT 토큰 생성 및 저장
            $member = $membersService->getMemberDataById($decodedRefreshToken['mb_id']);
            $level  = $membersService->getMemberLevelData($member['member_level']) ?? 0;

            $payload = [
                'mb_no' => $member['mb_no'],
                'cf_class' => $member['cf_class'],
                'mb_id' => $member['mb_id'],
                'mb_level' => $member['member_level'],
                'nickName' => $member['nickName'],
                'is_admin' => $level['is_admin'],
                'is_super' => $level['is_super'],
            ];
            $newJwtToken = $cryptoHelper->generateJwtToken($payload);
            $cookieManager->set('jwtToken', $newJwtToken);

            // 관리자/일반 사용자 분기 처리
            if ($level['is_admin']) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /');
            }
            exit();
        }

        // GET 요청 처리 (로그인 폼 표시)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $viewData = [];

            // social
            $socialProvider = $socialController->getProviderList();
            $socialItems = !empty($socialProvider) ? $componentsViewHelper->renderComponent('socialItems', $socialProvider, 'login') : '';
            
            // url
            $referrer = '';
            $url = isset($_GET['url']) ? strip_tags($_GET['url']) : '';
            if (!$url && isset($_SERVER['REFERRER']) && $_SERVER['REFERRER']) {
                $url = $_SERVER['REFERRER'];
            }
            if ($url) {
                $parseUrl = parse_url($url);
                if ($config_domain['cf_domain'] === $parseUrl['host']) {
                    $referrer = $parseUrl['path'];
                    $referrer.= isset($url['query']) ? '?'.$url['query'] : '';
                }
            }
            
            $viewData['socialProvider'] = $socialItems;
            $viewData['url'] = urlencode($referrer);

            return [
                "viewPath" => $viewPath,
                "viewData" => $viewData,
                "fullPage" => true,
            ];
        } 
        // POST 요청 처리 (로그인 시도)
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $password = $_POST['password'] ?? '';

            $member = $membersService->getMemberDataById($id);

            // 비밀번호 검증
            if ($member && $cryptoHelper->verifyPassword($password, $member['password'])) {
                echo '<pre>';
                var_dump($member['member_level']);
                echo '</pre>';
                $level = $membersService->getMemberLevelData($member['member_level']) ?? [];
                
                echo '<pre>';
                var_dump($level);
                echo '</pre>';

                $authService = $this->container->get('AuthService');
                $authService->login($member, $level);
            } else { 
                // 로그인 실패 처리
                $viewPath = 'Content/'.$contentSkin.'/Auth/login_form';
                
                $socialProvider = $socialController->getProviderList();
                $socialItems = !empty($socialProvider) ? $componentsViewHelper->renderComponent('socialItems', $socialProvider, 'login') : '';
                $viewData['socialProvider'] = $socialItems;

                $viewData = ['error' => 'Invalid email or password', 'email' => $email];
                return [
                    "viewPath" => $viewPath,
                    "viewData" => $viewData
                ];
            }
        }
    }

    /**
     * 로그아웃 처리를 담당하는 메소드
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     */
    public function logout($vars)
    {
        $authService = $this->container->get('AuthService');
        $authService->logout();
    }

    /**
     * 리프레시 토큰을 사용하여 로그인 세션을 연장하는 메소드
     * 
     * @param string $refreshToken 리프레시 토큰
     * @return bool 로그인 연장 성공 여부
     */
    public function extendLoginWithRefreshToken($refreshToken)
    {
        $cryptoHelper = $this->container->get('CryptoHelper');
        $cookieManager = $this->container->get('CookieManager');
        $membersService = $this->container->get('MembersService');

        // 리프레시 토큰 검증
        $decodedRefreshToken = $cryptoHelper->verifyJwtToken($refreshToken);
        if (!$decodedRefreshToken) {
            return false; // 리프레시 토큰이 유효하지 않음
        }

        // 사용자 정보 조회
        $member = $membersService->getMemberDataById($decodedRefreshToken['mb_id']);
        if (!$member) {
            return false; // 사용자를 찾을 수 없음
        }

        $level = $membersService->getMemberLevelData($member['member_level']) ?? [];

        // 새로운 JWT 토큰 생성
        $payload = [
            'mb_no' => $member['mb_no'],
            'cf_class' => $member['cf_class'],
            'mb_id' => $member['mb_id'],
            'mb_level' => $member['member_level'],
            'nickName' => $member['nickName'],
            'is_admin' => $level['is_admin'] ?? false,
            'is_super' => $level['is_super'] ?? false,
        ];
        $newJwtToken = $cryptoHelper->generateJwtToken($payload);

        // 새로운 JWT 토큰을 쿠키에 저장
        $cookieManager->set('jwtToken', $newJwtToken);

        return true; // 로그인 연장 성공
    }
}