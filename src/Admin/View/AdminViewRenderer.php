<?php
// 파일 위치 src/Admin/View/AdminViewRenderer.php
namespace Web\Admin\View;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\View\ComponentsView;
use Web\PublicHtml\Service\AuthService;
use Web\Admin\Helper\AdminMenuHelper;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class AdminViewRenderer
{
    private DependencyContainer $container;
    private string $skinDirectory;
    private SessionManager $sessionManager;
    private ComponentsView $componentsView;
    private AdminMenuHelper $adminMenuHelper;
    private CsrfTokenHandler $csrfTokenHandler;
    private AuthService $authService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $config_domain = $container->get('config_domain');
        $adminSkin = $config_domain['cf_skin_admin'] ?? 'basic';
        $layoutSkin = $config_domain['cf_layout_skin'] ?? 'basic';
        
        $this->skinDirectory = __DIR__ . "/{$adminSkin}/";
        $this->sessionManager = new SessionManager();
        $this->componentsView = new ComponentsView($layoutSkin);
        $this->csrfTokenHandler = new CsrfTokenHandler($this->sessionManager);
        $this->adminMenuHelper = new AdminMenuHelper($this->container);
        $this->authService = $this->container->get('AuthService');

        // CSRF 토큰 세션 검증
        $this->checkCsrfToken();
    }

    /**
     * CSRF 토큰이 세션에 없거나 만료되었으면 로그아웃 후 로그인 페이지로 리다이렉트
     */
    private function checkCsrfToken(): void
    {
        $csrfTokenKey = $_ENV['ADMIN_CSRF_TOKEN_KEY'] ?? 'admin_secure_key';
        $csrfToken = $this->sessionManager->getCsrfToken($csrfTokenKey);
        if (empty($csrfToken)) {
            $this->logoutAndRedirect();
        }
    }

    /**
     * 로그아웃 처리 및 로그인 페이지로 리다이렉트
     */
    private function logoutAndRedirect(): void
    {
        $this->authService->logout('/auth/login');
    }

    public function renderPagination(array $paginationData): void
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
    public function renderHeader(array $data = []): void
    {
        $data['menu'] = $this->adminMenuHelper->getAdminMenu();
        $this->render('Header', $data);
    }

    // 공통 푸터를 렌더링하는 메서드
    public function renderFooter(array $data = []): void
    {
        $this->render('Footer', $data);
    }

    // 레이아웃 시작 부분을 렌더링하는 메서드
    public function renderLayoutOpen(array $data = []): void
    {
        $this->render('LayoutOpen', $data);
    }

    // 레이아웃 종료 부분을 렌더링하는 메서드
    public function renderLayoutClose(array $data = []): void
    {
        $this->render('LayoutClose', $data);
    }

    // 특정 뷰 파일을 렌더링하는 메서드
    public function render(?string $viewFilePath, array $data = []): void
    {
        // SessionManager와 CsrfTokenHandler를 데이터에 추가
        $data['sessionManager'] = $this->sessionManager;
        $data['csrfTokenHandler'] = $this->csrfTokenHandler;
        
        // CSRF 토큰 추가
        $csrfTokenKey = $_ENV['ADMIN_CSRF_TOKEN_KEY'] ?? 'admin_secure_key';
        $csrfToken = $this->sessionManager->getCsrfToken($csrfTokenKey);
        if ($csrfToken) {
            $data['csrfToken'] = $csrfToken['token'];
        }

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
    public function renderPage(
        ?string $view = null, 
        ?array $headData = null, 
        ?array $headerData = null, 
        ?array $layoutData = null, 
        ?array $viewData = null, 
        ?array $footerData = null, 
        ?array $footData = null, 
        bool $fullPage = false
    ): void {
        $this->render('partials/head', $headData ?? []);
        if($fullPage === false) { $this->renderHeader($headerData ?? []); }
        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, $viewData ?? []);
        $this->renderLayoutClose($layoutData ?? []);
        if($fullPage === false) { $this->renderFooter($footerData ?? []); }
        $this->render('partials/foot', $footData ?? []);
    }
}