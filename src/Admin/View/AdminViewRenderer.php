<?php
// 파일 위치 src/Admin/View/AdminViewRenderer.php
namespace Web\Admin\View;

use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\View\ComponentsView;
use Web\Admin\Helper\AdminMenuHelper;

class AdminViewRenderer
{
    private $container;
    private $skinDirectory;
    private $sessionManager;
    private $componentsView;
    private $adminMenuHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $configDomain = $container->get('config_domain');
        $adminSkin = $configDomain['cf_skin_admin'] ?? 'basic';
        $layoutSkin = $configDomain['cf_layout_skin'] ?? 'basic';
        
        $this->skinDirectory = __DIR__ . "/{$adminSkin}/";
        $this->sessionManager = new SessionManager();
        $this->componentsView = new ComponentsView($layoutSkin);
        $this->adminMenuHelper = new AdminMenuHelper($this->container);

        // CSRF 토큰 세션 검증
        $this->checkCsrfToken();
    }

    /**
     * CSRF 토큰이 세션에 없으면 로그아웃 후 로그인 페이지로 리다이렉트
     */
    private function checkCsrfToken()
    {
        $csrfToken = $this->sessionManager->get($_ENV['ADMIN_CSRF_TOKEN_KEY']);
        if (empty($csrfToken)) {
            $this->logoutAndRedirect();
        }
    }

    /**
     * 로그아웃 처리 및 로그인 페이지로 리다이렉트
     */
    private function logoutAndRedirect()
    {
        // 세션 파기
        $this->sessionManager->destroy();

        // 쿠키 삭제
        setcookie('jwtToken', '', time() - 3600, '/');
        setcookie('refreshToken', '', time() - 3600, '/');
        // 로그아웃 후 로그인 페이지로 리다이렉트
        header('Location: /auth/login');
        exit();
    }

    public function renderPagination($paginationData)
    {
        extract($paginationData);

        $data = [];
        foreach (array_keys($paginationData) as $key) {
            $data[$key] = $$key;
        }

        // 추출한 변수들을 renderComponent에 배열로 전달
        echo $this->componentsView->renderComponent('pagination', $data);
    }

    // 공통 헤더를 렌더링하는 메서드
    public function renderHeader(array $data = [])
    {
        $data['menu'] = $this->adminMenuHelper->getAdminMenu();
        $this->render('Header', $data);
    }

    // 공통 푸터를 렌더링하는 메서드
    public function renderFooter(array $data = [])
    {
        $this->render('Footer', $data);
    }

    // 레이아웃 시작 부분을 렌더링하는 메서드
    public function renderLayoutOpen(array $data = [])
    {
        $this->render('LayoutOpen', $data);
    }

    // 레이아웃 종료 부분을 렌더링하는 메서드
    public function renderLayoutClose(array $data = [])
    {
        $this->render('LayoutClose', $data);
    }

    // 특정 뷰 파일을 렌더링하는 메서드
    public function render($viewFilePath, array $data = [])
    {
        // SessionManager를 데이터에 추가
        $data['sessionManager'] = $this->sessionManager; //head에 추가

        extract($data);

        // 전달된 경로가 이미 절대 경로일 경우 바로 사용
        if (file_exists($viewFilePath . '.php')) {
            include $viewFilePath . '.php';
        } else {
            // 그렇지 않은 경우 상대 경로로 처리하여 파일을 찾음
            $fullViewFilePath = $this->skinDirectory . $viewFilePath . '.php';
            if (file_exists($fullViewFilePath)) {
                include $fullViewFilePath;
            } else {
                // 파일을 찾을 수 없는 경우 오류 메시지 출력
                echo "뷰 파일을 찾을 수 없습니다: {$fullViewFilePath}"; //에러페이지로 대체할 것.
            }
        }
    }

    // 전체 페이지를 렌더링하는 메서드
    public function renderPage($view, ?array $headData = null, ?array $headerData = null, ?array $layoutData = null, ?array $viewData = null, ?array $footerData = null, ?array $footData = null)
    {
        $this->render('partials/head', $headData ?? []);
        $this->renderHeader($headerData ?? []);
        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, $viewData ?? []);
        $this->renderLayoutClose($layoutData ?? []);
        $this->renderFooter($footerData ?? []);
        $this->render('partials/foot', $footData ?? []);
    }
}