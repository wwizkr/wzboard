<?php
namespace Web\PublicHtml\View;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\View\LayoutManager;

class ViewRenderer
{
    private DependencyContainer $container;
    private array $cssFiles = [];
    private array $jsFiles = [];
    private array $config_domain;
    private string $deviceType;
    private string $headerSkin;
    private string $footerSkin;
    private string $layoutSkin;
    private string $headerSkinDirectory;
    private string $footerSkinDirectory;
    private string $layoutSkinDirectory;
    private ComponentsViewHelper $componentsViewHelper;
    private LayoutManager $layoutManager;
    private bool $isLogin;
    private $sessionManager;
    private $navigation;
    private $templateService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');

        $this->headerSkin = $this->config_domain['cf_skin_header'] ?? 'basic';
        $this->footerSkin = $this->config_domain['cf_skin_footer'] ?? 'basic';
        $this->layoutSkin = $this->config_domain['cf_skin_layout'] ?? 'basic';
        
        $this->headerSkinDirectory = __DIR__ . "/Header/{$this->headerSkin}/";
        $this->footerSkinDirectory = __DIR__ . "/Footer/{$this->footerSkin}/";
        $this->layoutSkinDirectory = __DIR__ . "/Layout/{$this->layoutSkin}/";

        $this->layoutManager = new LayoutManager($this->container, $this);

        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
        $this->templateService = $this->container->get('TemplateService');

        $this->sessionManager = $this->container->get('SessionManager');
        $authInfo = $this->sessionManager->get('auth');
        $this->isLogin = !empty($authInfo);
        
        $this->navigation = $this->container->get('NavigationMiddleware');
    }

    public function isHomePage(): bool
    {
        // 메인 페이지로 간주할 URL 패턴을 정의합니다.
        $homePagePatterns = [
            '#^/$#',                 // 루트 URL
            '#^/index\.php$#',       // index.php
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
        if ($type === 'css') {
            return $this->cssFiles;
        } elseif ($type === 'js') {
            return $this->jsFiles;
        }
        return [];
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
        extract($paginationData);

        $data = [];
        foreach (array_keys($paginationData) as $key) {
            $data[$key] = $$key;
        }

        echo $this->componentsViewHelper->renderComponent('pagination', $data);
    }

    public function renderHeader(array $data = []): void
    {
        $menuData = $this->container->get('menu_datas');
        $me_code = $this->updateMeCode();
        
        $data['menu'] = $this->componentsViewHelper->renderMenu($this->config_domain, $menuData, $me_code);
        $data['mainStyle'] = $this->isHomePage() && $this->config_domain['cf_index_wide'] === 0 ? '' : 'max-layout';

        $this->render($this->headerSkinDirectory . 'Header', $data);
    }

    public function renderFooter(array $data = []): void
    {
        $this->render($this->footerSkinDirectory . 'Footer', $data);
    }

    public function renderLayoutOpen(array $data = []): void
    {
        $isIndex = $this->isHomePage();
        $me_code = $this->updateMeCode();
        $layoutContent = $this->layoutManager->renderLayoutOpen($isIndex, $me_code);
        
        echo $layoutContent;
        
        /**
         * LayoutOpen 은 필요시에만 사용
         * $this->render($this->layoutSkinDirectory . 'LayoutOpen', $data);
        */
    }

    public function renderLayoutClose(array $data = []): void
    {
        echo '</div><!-- End container_wrap--->'.PHP_EOL;
        echo '</div><!-- End container--->'.PHP_EOL;
        /**
         * LayoutClose 는 필요시에만 사용
         * $this->render($this->layoutSkinDirectory . 'LayoutClose', $data);
        */
    }

    public function render(string $viewFilePath, array $data = []): void
    {
        $data['config_domain'] = $this->config_domain;
        extract($data, EXTR_SKIP);
        if (file_exists($viewFilePath . '.php')) {
            include $viewFilePath . '.php';
        } else {
            $fullViewFilePath = __DIR__ . '/' . $viewFilePath . '.php';
            if (file_exists($fullViewFilePath)) {
                include $fullViewFilePath;
            } else {
                echo "뷰 파일을 찾을 수 없습니다: {$fullViewFilePath}";
            }
        }
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
        $me_code = $this->updateMeCode();

        // head 부분 렌더링
        $this->render('/partials/'.$this->layoutSkin.'/head', $headData ?? []);

        if ($fullPage === false) {
            $this->renderHeader($headerData ?? []);
        }

        $this->renderLayoutOpen($layoutData ?? []);
        /*
         * 본문
         */
        $this->render($view, array_merge($viewData ?? [], ['me_code' => $me_code]));

        $this->renderLayoutClose($layoutData ?? []);

        if ($fullPage === false) {
            $this->renderFooter($footerData ?? []);
        }

        // foot 부분 렌더링
        $this->render('/partials/'.$this->layoutSkin.'/foot', $footData ?? []);
    }
}