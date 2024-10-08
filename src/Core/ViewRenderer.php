<?php
namespace Web\PublicHtml\Core;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\Core\LayoutManager;

class ViewRenderer
{
    private DependencyContainer $container;
    private array $cssFiles = [];
    private array $jsFiles = [];
    private ?array $config_domain = null;
    private ?string $deviceType = null;
    private ?string $headerSkin = null;
    private ?string $footerSkin = null;
    private ?string $layoutSkin = null;
    private ?string $headerSkinDirectory = null;
    private ?string $footerSkinDirectory = null;
    private ?string $layoutSkinDirectory = null;
    private ?ComponentsViewHelper $componentsViewHelper = null;
    private ?LayoutManager $layoutManager = null;
    private ?bool $isLogin = null;
    private $sessionManager;
    private $navigation;
    private $templateService;
    private ?bool $isIndex = null;
    private ?string $meCode = null;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->sessionManager = $this->container->get('SessionManager');
        $this->navigation = $this->container->get('NavigationMiddleware');
        $this->templateService = $this->container->get('TemplateService');
    }

    private function lazyLoadConfig(): void
    {
        if ($this->config_domain === null) {
            $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
            $this->headerSkin = $this->config_domain['cf_skin_header'] ?? 'basic';
            $this->footerSkin = $this->config_domain['cf_skin_footer'] ?? 'basic';
            $this->layoutSkin = $this->config_domain['cf_skin_layout'] ?? 'basic';
            
            $this->headerSkinDirectory = WZ_SRC_PATH . "/View/Header/{$this->headerSkin}/";
            $this->footerSkinDirectory = WZ_SRC_PATH . "/View/Footer/{$this->footerSkin}/";
            $this->layoutSkinDirectory = WZ_SRC_PATH . "/View/Layout/{$this->layoutSkin}/";
        }
    }

    private function lazyLoadLayoutManager(): void
    {
        if ($this->layoutManager === null) {
            $this->layoutManager = new LayoutManager($this->container);
        }
    }

    private function lazyLoadComponentsViewHelper(): void
    {
        if ($this->componentsViewHelper === null) {
            $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
        }
    }

    private function lazyLoadAuthInfo(): void
    {
        if ($this->isLogin === null) {
            $authInfo = $this->sessionManager->get('auth');
            $this->isLogin = !empty($authInfo);
        }
    }

    private function lazyLoadPageInfo(): void
    {
        if ($this->isIndex === null) {
            $this->isIndex = $this->isHomePage();
        }
        if ($this->meCode === null) {
            $this->meCode = $this->updateMeCode();
        }
    }

    public function isHomePage(): bool
    {
        $homePagePatterns = [
            '#^/$#',
            '#^/index\.php$#',
        ];

        $currentRoute = $_SERVER['REQUEST_URI'] ?? '/';

        foreach ($homePagePatterns as $pattern) {
            if (preg_match($pattern, $currentRoute)) {
                return true;
            }
        }

        return false;
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

    private function updateMeCode(): string
    {
        $navigation = $this->navigation->buildNavigation();
        $me_code = $navigation['me_code'] ?? '';
        $this->container->set('me_code', $me_code);
        return $me_code;
    }

    public function renderPagination(array $paginationData): void
    {
        $this->lazyLoadComponentsViewHelper();
        echo $this->componentsViewHelper->renderComponent('pagination', $paginationData);
    }

    public function renderHeader(array $data = []): void
    {
        $this->lazyLoadConfig();
        $this->lazyLoadComponentsViewHelper();
        $this->lazyLoadLayoutManager();
        $this->lazyLoadPageInfo();

        $menuData = $this->container->get('menu_datas');
        $data['menu'] = $this->componentsViewHelper->renderMenu($this->config_domain, $menuData, $this->meCode);
        $data['mainStyle'] = $this->isIndex && $this->config_domain['cf_index_wide'] === 0 ? '' : 'max-layout';
        $data['subContent'] = $this->layoutManager->renderSubContent('subtop', $this->isIndex, $this->meCode);

        $this->render($this->headerSkinDirectory . 'Header', $data);
    }

    public function renderFooter(array $data = []): void
    {
        $this->lazyLoadConfig();
        $this->lazyLoadLayoutManager();
        $this->lazyLoadPageInfo();

        $data['subContent'] = $this->layoutManager->renderSubContent('subfoot', $this->isIndex, $this->meCode);
        $this->render($this->footerSkinDirectory . 'Footer', $data);
    }

    public function renderLayoutOpen(array $data = []): void
    {
        $this->lazyLoadLayoutManager();
        $this->lazyLoadPageInfo();

        echo $this->layoutManager->renderLayoutOpen($this->isIndex, $this->meCode);
    }

    public function renderLayoutClose(array $data = []): void
    {
        $this->lazyLoadLayoutManager();
        $this->lazyLoadPageInfo();

        echo $this->layoutManager->renderLayoutClose($this->isIndex, $this->meCode);
    }

    public function render(string $viewFilePath, array $data = []): void
    {
        $this->lazyLoadConfig();
        $data['config_domain'] = $this->config_domain;

        // 변수 추출 시 안전한 방식 사용
        foreach ($data as $key => $value) {
            if (!isset($$key)) {
                $$key = $value;
            }
        }

        $fullViewFilePath = $this->resolveViewPath($viewFilePath);

        if (file_exists($fullViewFilePath)) {
            include $fullViewFilePath;
        } else {
            $this->handleMissingViewFile($fullViewFilePath);
        }
    }

    private function resolveViewPath(string $viewFilePath): string
    {
        if (strpos($viewFilePath, 'Plugins/') === 0) {
            return WZ_SRC_PATH . '/' . $viewFilePath . '.php';
        }
        return file_exists($viewFilePath . '.php')
            ? $viewFilePath . '.php'
            : WZ_SRC_PATH . '/View/' . $viewFilePath . '.php';
    }

    private function handleMissingViewFile(string $fullViewFilePath): void
    {
        // 사용자에게 보여줄 오류 메시지
        echo "페이지를 표시할 수 없습니다. 관리자에게 문의해주세요.";
    }

    public function renderPage(
        string $view, 
        ?array $headData = null, 
        ?array $headerData = null, 
        ?array $layoutData = null, 
        ?array $viewData = null, 
        ?array $footerData = null, 
        ?array $footData = null, 
        bool $fullPage = false
    ): void {
        $this->lazyLoadConfig();
        $this->lazyLoadPageInfo();

        $this->render(WZ_SRC_PATH.'/View/partials/'.$this->layoutSkin.'/head', $headData ?? []);

        if ($fullPage === false) {
            $this->renderHeader($headerData ?? []);
        }

        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, array_merge($viewData ?? [], ['me_code' => $this->meCode]));
        $this->renderLayoutClose($layoutData ?? []);

        if ($fullPage === false) {
            $this->renderFooter($footerData ?? []);
        }

        $this->render(WZ_SRC_PATH.'/View/partials/'.$this->layoutSkin.'/foot', $footData ?? []);
    }
}