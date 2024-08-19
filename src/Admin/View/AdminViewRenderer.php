<?php
// 파일 위치 src/Admin/View/AdminViewRenderer.php
namespace Web\Admin\View;

use Web\PublicHtml\Helper\DependencyContainer;

class AdminViewRenderer
{
    private $headerSkinDirectory;
    private $footerSkinDirectory;
    private $layoutSkinDirectory;

    public function __construct(DependencyContainer $container)
    {
        // 관리 페이지용 스킨 이름을 컨테이너에서 가져옴, 기본값은 'basic'
        $headerSkin = $container->get('adminHeaderSkin') ?? 'basic';
        $footerSkin = $container->get('adminFooterSkin') ?? 'basic';
        $layoutSkin = $container->get('adminLayoutSkin') ?? 'basic';

        // 스킨 디렉토리 설정
        $this->headerSkinDirectory = __DIR__ . "/Header/{$headerSkin}/";
        $this->footerSkinDirectory = __DIR__ . "/Footer/{$footerSkin}/";
        $this->layoutSkinDirectory = __DIR__ . "/Layout/{$layoutSkin}/";
    }

    // 공통 헤더를 렌더링하는 메서드
    public function renderHeader(array $data = [])
    {
        $this->render($this->headerSkinDirectory . 'Header', $data);
    }

    // 공통 푸터를 렌더링하는 메서드
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
        extract($data);

        // 전달된 경로가 이미 절대 경로인 경우 바로 사용, 파일이 있는 경우.
        if (file_exists($viewFilePath . '.php')) {
            include $viewFilePath . '.php';
        } else {
            // 그렇지 않은 경우 상대 경로로 처리하여 파일을 찾음
            $fullViewFilePath = __DIR__ . '/' . $viewFilePath . '.php';

            if (file_exists($fullViewFilePath)) {
                include $fullViewFilePath;
            } else {
                // 파일을 찾을 수 없는 경우 오류 메시지 출력
                echo "<pre>뷰 파일을 찾을 수 없습니다: {$fullViewFilePath}</pre>";
            }
        }
    }

    // 전체 페이지를 렌더링하는 메서드
    public function renderPage($view, ?array $headData = null, ?array $headerData = null, ?array $layoutData = null, ?array $viewData = null, ?array $footerData = null, ?array $footData = null)
    {
        $this->render('/partials/head', $headData ?? []);
        $this->renderHeader($headerData ?? []);
        $this->renderLayoutOpen($layoutData ?? []);
        $this->render($view, $viewData ?? []);
        $this->renderLayoutClose($layoutData ?? []);
        $this->renderFooter($footerData ?? []);
        $this->render('/partials/foot', $footData ?? []);
    }
}