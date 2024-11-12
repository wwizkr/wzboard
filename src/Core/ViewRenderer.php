<?php
namespace Web\PublicHtml\Core;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\Core\LayoutManager;

/**
 * ViewRenderer 클래스
 * 
 * 이 클래스는 뷰 렌더링과 관련된 다양한 기능을 제공합니다.
 * 레이아웃, 헤더, 푸터 등의 렌더링을 담당하며 지연 로딩 패턴을 사용합니다.
 */
class ViewRenderer
{
    private DependencyContainer $container;
    private array $lazyLoadedProperties = [];
    private array $cssFiles = [];
    private array $jsFiles = [];
    
    /**
     * ViewRenderer 생성자
     * 
     * @param DependencyContainer $container 의존성 컨테이너
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

    private function lazyLoad(string $property, callable $loader): void
    {
        if (!isset($this->lazyLoadedProperties[$property])) {
            $this->lazyLoadedProperties[$property] = $loader();
        }
    }

    private function get(string $property)
    {
        return $this->lazyLoadedProperties[$property] ?? null;
    }

    /**
     * 설정 정보를 지연 로드합니다.
     * 
     * 이 메서드는 필요한 시점에 설정 정보를 로드하여 메모리 사용을 최적화합니다.
     */
    private function loadConfig(): void
    {
        $this->lazyLoad('config_domain', function() {
            return $this->container->get('ConfigHelper')->getConfig('config_domain');
        });

        $this->lazyLoad('headerSkin', function() {
            return $this->get('config_domain')['cf_skin_header'] ?? 'basic';
        });

        $this->lazyLoad('footerSkin', function() {
            return $this->get('config_domain')['cf_skin_footer'] ?? 'basic';
        });

        $this->lazyLoad('layoutSkin', function() {
            return $this->get('config_domain')['cf_skin_layout'] ?? 'basic';
        });

        $this->lazyLoad('headerSkinDirectory', function() {
            return WZ_SRC_PATH . "/View/Header/{$this->get('headerSkin')}/";
        });

        $this->lazyLoad('footerSkinDirectory', function() {
            return WZ_SRC_PATH . "/View/Footer/{$this->get('footerSkin')}/";
        });

        $this->lazyLoad('layoutSkinDirectory', function() {
            return WZ_SRC_PATH . "/View/Layout/{$this->get('layoutSkin')}/";
        });
    }

    private function loadHeadData(array $config_domain, array $headData): array
    {
        $protocol = $protocol = $_SERVER['REQUEST_SCHEME'] ?? 'http';
        $host = $config_domain['cf_domain'];
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');
        $fullUrl = $protocol . "://" . $host . $uri;

        $icoImage = '';
        if (file_exists(WZ_STORAGE_PATH.'/common/'.$config_domain['cf_id'].'/favicon.ico')) {
            $icoImage = '/storage/common/'.$config_domain['cf_id'].'/favicon.ico';
        }

        $ogImage = '';
        if (file_exists(WZ_STORAGE_PATH.'/common/'.$config_domain['cf_id'].'/og.image.jpg')) {
            $ogImage = '/storage/common/'.$config_domain['cf_id'].'/favicon.ico';
        }

        $addMeta = $config_domain['cf_add_meta'] ? explode("\n", $config_domain['cf_add_meta']) : [];
        $data = [
            'seoTitle' => $headData['title'] ?? $config_domain['cf_title'],
            'canonical' => htmlspecialchars($fullUrl),
            'seoKeyword' => $headData['seoKeyword'] ?? $config_domain['cf_seo_keyword'],
            'seoDescription' => $headData['seoDescription'] ?? $config_domain['cf_seo_description'],
            'icoImage' => $icoImage,
            'ogImage' => $headData['ogImage'] ?? $ogImage,
            'addMeta' => $addMeta,
        ];

        return $data;
    }

    private function loadfootData(array $config_domain, array $headData): array
    {
        $addScript = $config_domain['cf_add_script'] ? explode("\n", $config_domain['cf_add_script']) : [];
        $data = [
            'addScript' => $addScript,
            'analytics' => $config_domain['cf_analytics'],
            'snsChannel' => $config_domain['cf_sns_channel_url'] ? unserialize($config_domain['cf_sns_channel_url']) : [],
        ];

        return $data;
    }

    private function loadLayoutManager(): void
    {
        $this->lazyLoad('layoutManager', function() {
            return new LayoutManager($this->container);
        });
    }

    private function loadComponentsViewHelper(): void
    {
        $this->lazyLoad('componentsViewHelper', function() {
            return $this->container->get('ComponentsViewHelper');
        });
    }

    private function loadAuthInfo(): void
    {
        $this->lazyLoad('isLogin', function() {
            $sessionManager = $this->container->get('SessionManager');
            $authInfo = $sessionManager->get('auth');
            return !empty($authInfo);
        });
    }

    private function loadPageInfo(): void
    {
        $this->lazyLoad('isIndex', function() {
            return $this->isHomePage();
        });

        $this->lazyLoad('meCode', function() {
            return $this->updateMeCode();
        });
    }
    
    /**
     * 현재 페이지가 홈페이지인지 확인합니다.
     * 
     * @return bool 홈페이지인 경우 true, 아닌 경우 false를 반환
     */
    public function isHomePage(): bool
    {
        $homePagePatterns = [
            '#^/$#',
            '#^/index\.php$#',
        ];

        // 현재 경로에서 쿼리 문자열을 제거
        $currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        foreach ($homePagePatterns as $pattern) {
            if (preg_match($pattern, $currentRoute)) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * CSS 또는 JS 파일을 에셋 목록에 추가합니다.
     * 
     * @param string $type 에셋 타입 ('css' 또는 'js')
     * @param string $filePath 파일 경로
     */
    public function addAsset(string $type, string $filePath): void
    {
        if ($type === 'css') {
            $this->cssFiles[] = $filePath;
        } elseif ($type === 'js') {
            $this->jsFiles[] = $filePath;
        }
    }
    
    /**
     * 특정 타입의 에셋 목록을 반환합니다.
     * 
     * @param string $type 에셋 타입 ('css' 또는 'js')
     * @return array 요청된 타입의 에셋 목록
     */
    public function getAssets(string $type): array
    {
        return $type === 'css' ? $this->cssFiles : ($type === 'js' ? $this->jsFiles : []);
    }

    private function updateMeCode(): string
    {
        $navigation = $this->container->get('NavigationMiddleware')->buildNavigation();
        $me_code = $navigation['me_code'] ?? '';
        $this->container->set('me_code', $me_code);
        return $me_code;
    }
    
     /**
     * 페이지네이션을 렌더링합니다.
     * 
     * @param array $paginationData 페이지네이션 데이터
     */
    public function renderPagination(array $paginationData): void
    {
        $this->loadComponentsViewHelper();
        echo $this->get('componentsViewHelper')->renderComponent('pagination', $paginationData);
    }
    
    /**
     * 헤더를 렌더링합니다.
     * 
     * @param array $data 푸터 렌더링에 필요한 데이터
     */
    public function renderHeader(array $data = []): void
    {
        $this->loadConfig();
        $this->loadComponentsViewHelper();
        $this->loadLayoutManager();
        $this->loadPageInfo();

        $menuData = $this->container->get('menu_datas');
        $data['menu'] = $this->get('componentsViewHelper')->renderMenu($this->get('config_domain'), $menuData, $this->get('meCode'));
        $data['mainStyle'] = $this->get('isIndex') && $this->get('config_domain')['cf_index_wide'] === 0 ? '' : 'max-layout';
        $data['subContent'] = $this->get('layoutManager')->renderSubContent('subtop', $this->get('isIndex'), $this->get('meCode'));

        $headerPath = isset($data['headerPath']) && $data['headerPath'] ? $data['headerPath'] : $this->get('headerSkinDirectory') . 'Header';

        $this->render($headerPath, $data);
    }
    
    /**
     * 푸터를 렌더링합니다.
     * 
     * @param array $data 푸터 렌더링에 필요한 데이터
     */
    public function renderFooter(array $data = []): void
    {
        $this->loadConfig();
        $this->loadLayoutManager();
        $this->loadPageInfo();

        $data['subContent'] = $this->get('layoutManager')->renderSubContent('subfoot', $this->get('isIndex'), $this->get('meCode'));

        $footerPath = isset($data['footerPath']) && $data['footerPath'] ? $data['footerPath'] : $this->get('footerSkinDirectory') . 'Footer';
        $this->render($footerPath, $data);
    }
    
    /**
     * 레이아웃 시작 부분을 렌더링합니다.
     * 
     * @param array $data 레이아웃 렌더링에 필요한 데이터
     */
    public function renderLayoutOpen(array $data = []): void
    {
        $this->loadLayoutManager();
        $this->loadPageInfo();

        echo $this->get('layoutManager')->renderLayoutOpen($this->get('isIndex'), $this->get('meCode'));
    }
    
    /**
     * 레이아웃 종료 부분을 렌더링합니다.
     * 
     * @param array $data 레이아웃 렌더링에 필요한 데이터
     */
    public function renderLayoutClose(array $data = []): void
    {
        $this->loadLayoutManager();
        $this->loadPageInfo();

        echo $this->get('layoutManager')->renderLayoutClose($this->get('isIndex'), $this->get('meCode'));
    }
    
    /**
     * 뷰 파일을 렌더링합니다.
     * 
     * @param string $viewFilePath 렌더링할 뷰 파일의 경로
     * @param array $data 뷰에 전달할 데이터
     */
    public function render(string $viewFilePath, array $data = []): void
    {
        $this->loadConfig();
        $this->loadAuthInfo();

        $data['config_domain'] = $this->get('config_domain');
        $data['isLogin'] = $this->get('isLogin');

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
    
    /**
     * 뷰 파일의 전체 경로를 해석합니다.
     * 
     * @param string $viewFilePath 뷰 파일의 상대 경로
     * @return string 뷰 파일의 전체 경로
     */
    private function resolveViewPath(string $viewFilePath): string
    {
        if (strpos($viewFilePath, 'Plugins/') === 0) {
            return WZ_SRC_PATH . '/' . $viewFilePath . '.php';
        }
        return file_exists($viewFilePath . '.php')
            ? $viewFilePath . '.php'
            : WZ_SRC_PATH . '/View/' . $viewFilePath . '.php';
    }
    
    /**
     * 뷰 파일이 없을 때의 처리를 담당합니다.
     * 
     * @param string $fullViewFilePath 찾을 수 없는 뷰 파일의 전체 경로
     */
    private function handleMissingViewFile(string $fullViewFilePath): void
    {
        // 사용자에게 보여줄 오류 메시지
        echo "페이지를 표시할 수 없습니다. 관리자에게 문의해주세요.";
        // 로깅 추가
        error_log("Missing view file: $fullViewFilePath");
    }
    
    /**
     * 전체 페이지를 렌더링합니다.
     * 
     * 이 메서드는 헤더, 본문, 푸터 등 페이지의 모든 부분을 렌더링합니다.
     * 
     * @param string $view 메인 뷰 파일의 경로
     * @param array|null $headData 헤드 섹션에 전달할 데이터
     * @param array|null $headerData 헤더에 전달할 데이터
     * @param array|null $layoutData 레이아웃에 전달할 데이터
     * @param array|null $viewData 메인 뷰에 전달할 데이터
     * @param array|null $footerData 푸터에 전달할 데이터
     * @param array|null $footData 최하단 스크립트 섹션에 전달할 데이터
     * @param bool $fullPage 전체 페이지 렌더링 여부
     */
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
        $this->loadConfig();
        $this->loadPageInfo();

        $headData = $this->loadHeadData($this->get('config_domain'), $headData);
        $headPath = isset($headData['headPath']) && $headData['headPath'] ? $headData['headPath'] : WZ_SRC_PATH.'/View/partials/'.$this->get('layoutSkin').'/head';
        $this->render($headPath, $headData ?? []);

        if ($fullPage === false) {
            $this->renderHeader($headerData ?? []);
        }

        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, array_merge($viewData ?? [], ['me_code' => $this->get('meCode')]));
        $this->renderLayoutClose($layoutData ?? []);

        if ($fullPage === false) {
            $this->renderFooter($footerData ?? []);
        }
        
        $footData = $this->loadFootData($this->get('config_domain'), $footData);
        $footPath = isset($footData['footPath']) && $footData['footPath'] ? $headData['footPath'] : WZ_SRC_PATH.'/View/partials/'.$this->get('layoutSkin').'/foot';
        $this->render($footPath, $footData ?? []);
    }
}