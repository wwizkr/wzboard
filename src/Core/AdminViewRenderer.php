<?php
namespace Web\PublicHtml\Core;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Core\ComponentsView;
use Web\PublicHtml\Service\AuthService;
use Web\Admin\Helper\AdminMenuHelper;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class AdminViewRenderer
{
    private DependencyContainer $container;
    private ?string $skinDirectory = null;
    private ?SessionManager $sessionManager = null;
    private ?ComponentsView $componentsView = null;
    private ?AdminMenuHelper $adminMenuHelper = null;
    private ?CsrfTokenHandler $csrfTokenHandler = null;
    private ?AuthService $authService = null;
    private array $cssFiles = [];
    private array $jsFiles = [];
    private ?array $config_domain = null;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

    private function lazyLoadConfig(): void
    {
        if ($this->config_domain === null) {
            $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
            $adminSkin = $this->config_domain['cf_skin_admin'] ?? 'basic';
            $this->skinDirectory = WZ_SRC_PATH . "/Admin/View/{$adminSkin}/";
        }
    }

    private function lazyLoadSessionManager(): void
    {
        if ($this->sessionManager === null) {
            $this->sessionManager = new SessionManager();
        }
    }

    private function lazyLoadComponentsView(): void
    {
        if ($this->componentsView === null) {
            $this->lazyLoadConfig();
            $layoutSkin = $this->config_domain['cf_layout_skin'] ?? 'basic';
            $this->componentsView = new ComponentsView($layoutSkin);
        }
    }

    private function lazyLoadCsrfTokenHandler(): void
    {
        if ($this->csrfTokenHandler === null) {
            $this->lazyLoadSessionManager();
            $this->csrfTokenHandler = new CsrfTokenHandler($this->sessionManager);
        }
    }

    private function lazyLoadAdminMenuHelper(): void
    {
        if ($this->adminMenuHelper === null) {
            $this->adminMenuHelper = new AdminMenuHelper($this->container);
        }
    }

    private function lazyLoadAuthService(): void
    {
        if ($this->authService === null) {
            $this->authService = $this->container->get('AuthService');
        }
    }

    private function checkCsrfToken(): void
    {
        $this->lazyLoadSessionManager();
        $this->lazyLoadAuthService();

        $csrfTokenKey = $_ENV['ADMIN_CSRF_TOKEN_KEY'] ?? 'admin_secure_key';
        $csrfToken = $this->sessionManager->getCsrfToken($csrfTokenKey);

        if (empty($csrfToken)) {
            /*
            $cookieManager = $this->container->get('CookieManager');
            
            $jwtToken = $cookieManager->get('jwtToken');
            $refreshToken = $cookieManager->get('refreshToken');

            if ($jwtToken && $decodedJwtToken = CryptoHelper::verifyJwtToken($jwtToken)) {
                return
            }
            */
            
            $this->logoutAndRedirect();
        }
    }

    private function logoutAndRedirect(): void
    {
        $this->authService->logout('/auth/login');
    }

    public function addAsset(string $type, string $filePath): void
    {
        if ($type === 'css') {
            $this->cssFiles[] = $filePath;
        } elseif ($type === 'js') {
            $this->jsFiles[] = $filePath;
        }
    }

    public function getAssets(string $type): array
    {
        return $type === 'css' ? $this->cssFiles : ($type === 'js' ? $this->jsFiles : []);
    }

    public function renderPagination(array $paginationData): void
    {
        $this->lazyLoadComponentsView();
        echo $this->componentsView->renderComponent('pagination', $paginationData);
    }

    public function renderHeader(array $data = []): void
    {
        $this->lazyLoadAdminMenuHelper();
        $data['menu'] = $this->adminMenuHelper->getAdminMenu();
        $this->render('Header', $data);
    }

    public function renderFooter(array $data = []): void
    {
        $this->render('Footer', $data);
    }

    public function renderLayoutOpen(array $data = []): void
    {
        $this->render('LayoutOpen', $data);
    }

    public function renderLayoutClose(array $data = []): void
    {
        $this->render('LayoutClose', $data);
    }

    private function getCommonViewData(): array
    {
        $this->lazyLoadSessionManager();
        $this->lazyLoadCsrfTokenHandler();

        $csrfTokenKey = $_ENV['ADMIN_CSRF_TOKEN_KEY'] ?? 'admin_secure_key';
        $csrfToken = $this->sessionManager->getCsrfToken($csrfTokenKey);

        return [
            'sessionManager' => $this->sessionManager,
            'csrfTokenHandler' => $this->csrfTokenHandler,
            'csrfToken' => $csrfToken['token'] ?? null,
        ];
    }

    public function render(?string $viewFilePath, array $data = []): void
    {
        $this->lazyLoadConfig();
        $this->checkCsrfToken();

        $commonData = $this->getCommonViewData();
        $mergedData = array_merge($commonData, $data);

        $fullViewFilePath = $this->resolveViewPath($viewFilePath);

        if (file_exists($fullViewFilePath)) {
            $this->includeView($fullViewFilePath, $mergedData);
        } else {
            $this->handleMissingViewFile($fullViewFilePath);
        }
    }

    private function resolveViewPath(?string $viewFilePath): string
    {
        if ($viewFilePath === null) {
            return '';
        }
        return file_exists($viewFilePath . '.php')
            ? $viewFilePath . '.php'
            : $this->skinDirectory . $viewFilePath . '.php';
    }

    private function includeView(string $viewFilePath, array $data): void
    {
        // 클로저를 사용하여 변수 스코프를 제어
        $renderView = function () use ($viewFilePath, $data) {
            // 뷰에서 사용할 변수들을 개별적으로 정의
            foreach ($data as $key => $value) {
                $$key = $value;
            }
            
            // 뷰 파일 포함
            include $viewFilePath;
        };
        
        $renderView();
    }

    private function handleMissingViewFile(string $fullViewFilePath): void
    {
        error_log("View file not found: {$fullViewFilePath}");
        echo "페이지를 표시할 수 없습니다. 관리자에게 문의해주세요.";
    }

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
        if ($fullPage === false) {
            $this->renderHeader($headerData ?? []);
        }
        $this->renderLayoutOpen($layoutData ?? []);
        if ($view !== null) {
            $this->render($view, $viewData ?? []);
        }
        $this->renderLayoutClose($layoutData ?? []);
        if ($fullPage === false) {
            $this->renderFooter($footerData ?? []);
        }
        $this->render('partials/foot', $footData ?? []);
    }
}