<?php
namespace Web\PublicHtml\View;

use Web\PublicHtml\Helper\DependencyContainer;

class ViewRenderer
{
    // 스킨 디렉토리 경로를 저장하는 변수들
    private $headerSkin;
    private $footerSkin;
    private $layoutSkin;
    private $headerSkinDirectory;
    private $footerSkinDirectory;
    private $layoutSkinDirectory;
    private $componentsView;
    private $container;

    // 생성자에서 스킨 디렉토리 설정
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        // 컨테이너에서 config_domain 배열을 가져옴
        $configDomain = $container->get('config_domain');

        // 컨테이너에서 각 스킨 이름을 가져옴, 기본값은 'basic'
        $this->headerSkin = $configDomain['cf_skin_header'] ?? 'basic';
        $this->footerSkin = $configDomain['cf_skin_footer'] ?? 'basic';
        $this->layoutSkin = $configDomain['cf_skin_layout'] ?? 'basic';
        
        // 각 스킨 디렉토리의 절대 경로를 설정
        $this->headerSkinDirectory = __DIR__ . "/Header/{$this->headerSkin}/";
        $this->footerSkinDirectory = __DIR__ . "/Footer/{$this->footerSkin}/";
        $this->layoutSkinDirectory = __DIR__ . "/Layout/{$this->layoutSkin}/";

        $this->componentsView = new ComponentsView($this->layoutSkin);
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

    // 헤더 스킨을 렌더링하는 메서드
    public function renderHeader(array $data = [])
    {
        // 컨테이너에서 트리화된 메뉴 데이터를 가져옴
        $menuData = $this->container->get('menu_datas');
        
        $data['menu'] = $this->componentsView->renderMenu($menuData);

        $this->render($this->headerSkinDirectory . 'Header', $data);
    }

    // 푸터 스킨을 렌더링하는 메서드
    public function renderFooter(array $data = [])
    {
        $this->render($this->footerSkinDirectory . 'Footer', $data);
    }

    // 레이아웃 시작 부분을 렌더링하는 메서드
    public function renderLayoutOpen(array $data = [])
    {
        $this->render($this->layoutSkinDirectory . 'LayoutOpen', $data);
    }

    // 레이아웃 종료 부분을 렌더링하는 메서드
    public function renderLayoutClose(array $data = [])
    {
        $this->render($this->layoutSkinDirectory . 'LayoutClose', $data);
    }

    // 특정 뷰 파일을 렌더링하는 메서드
    public function render($viewFilePath, array $data = [])
    {
        // 전달된 데이터를 개별 변수로 풀어줌 (예: $data['title'] -> $title)
        extract($data);

        // 전달된 경로가 이미 절대 경로일 경우 바로 사용
        if (file_exists($viewFilePath . '.php')) {
            include $viewFilePath . '.php';
        } else {
            // 그렇지 않은 경우 상대 경로로 처리하여 파일을 찾음
            $fullViewFilePath = __DIR__ . '/' . $viewFilePath . '.php';
            if (file_exists($fullViewFilePath)) {
                include $fullViewFilePath;
            } else {
                // 파일을 찾을 수 없는 경우 오류 메시지 출력
                echo "뷰 파일을 찾을 수 없습니다: {$fullViewFilePath}"; //에러페이지로 대체할 것.
            }
        }
    }

    // 전체 페이지를 렌더링하는 메서드
    public function renderPage($view, ?array $headData = null, ?array $headerData = null, ?array $layoutData = null, ?array $viewData = null, ?array $footerData = null, ?array $footData = null, ?bool $fullPage = false)
    {
        $this->render('/partials/'.$this->layoutSkin.'/head', $headData ?? []);
        if($fullPage === false) { $this->renderHeader($headerData ?? []); }
        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, $viewData ?? []);
        $this->renderLayoutClose($layoutData ?? []);
        if($fullPage === false) { $this->renderFooter($footerData ?? []); }
        $this->render('/partials/'.$this->layoutSkin.'/foot', $footData ?? []);
    }
}
