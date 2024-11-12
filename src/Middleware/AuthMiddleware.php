<?php
// 파일 위치: src/Middleware/AuthMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Service\AuthService;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Service\AdminLevelService;

class AuthMiddleware
{
    private $container;
    private $sessionManager;
    private $authService;
    private $protectedPaths = [
        '/admin',
        '/mypage',
        // 다른 보호된 페이지 경로를 여기에 추가
    ];

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $this->container->get('SessionManager');
        $this->authService = $this->container->get('AuthService');
    }

    public function handle($uri)
    {
        $this->authService->validateAuth();
        if ($this->isProtectedPath($uri)) {
            $this->validateProtectedAccess($uri);
        }
    }

    public function getAuthUser()
    {
        return $this->authService->getCurrentUser();
    }

    private function isProtectedPath($uri)
    {
        foreach ($this->protectedPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }
        return false;
    }

    private function validateProtectedAccess($uri)
    {
        $auth = $this->sessionManager->get('auth');
        if (!$auth) {
            header('Location: /auth/login');
            //exit;
        }
        // 관리자 페이지에 대한 추가 검증
        if (strpos($uri, '/admin') === 0 && (!empty($auth) && !$auth['is_admin'] && !$auth['is_super'])) {
            echo '<pre>';
            var_dump($auth);
            echo '</pre>';
            exit;
            header('Location: /');
            //exit;
        }
    }

    // 관리자 페이지에 대한 관리 권한 체크
    public function checkAdminAuth($action = false, $activeCode = '')
    {
        $authUser = $this->getAuthUser();
        if (empty($authUser) || !$authUser['is_admin']) {
            CommonHeler::alertAndBack('접근 권한이 없습니다.');
            exit;
        }

        if ($authUser['is_super']) {
            return;
        }

        if ($action === false) {
            CommonHeler::alertAndBack('접근 권한이 없습니다.');
            exit;
        }
        
        if (!$activeCode) {
            $activeCode = CommonHelper::validateparam('activeCode', 'string', false, false, INPUT_GET);
        }
        if (!$activeCode) {
            $activeCode = CommonHelper::validateparam('activeCode', 'string', false, false, INPUT_POST);
        }

        if (!$activeCode) {
            CommonHeler::alertAndBack('접근 권한이 없습니다.');
            exit;
        }

        $adminLevelService = new AdminLevelService($this->container);

        $result = $adminLevelService->processedAdminAction($action, $authUser['member_data']['member_level'], $activeCode);
        
        if ($result === false) {
            CommonHeler::alertAndBack('접근 권한이 없습니다.');
            exit;
        }
    }
}