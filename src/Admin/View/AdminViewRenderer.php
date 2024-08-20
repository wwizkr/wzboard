<?php
// 파일 위치 src/Admin/View/AdminViewRenderer.php
namespace Web\Admin\View;

use Web\PublicHtml\Helper\DependencyContainer;

class AdminViewRenderer
{
    private $skinDirectory;

    public function __construct(DependencyContainer $container)
    {
        // 컨테이너에서 config_domain 배열을 가져옴
        $configDomain = $container->get('config_domain');
        // 관리 페이지용 스킨 이름을 컨테이너에서 가져옴, 기본값은 'basic'
        $adminSkin = $configDomain['cf_skin_admin'] ?? 'basic';
        // 스킨 디렉토리 설정
        $this->skinDirectory = __DIR__ . "/{$adminSkin}/";
    }

    // 공통 헤더를 렌더링하는 메서드
    public function renderHeader(array $data = [])
    {
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
        extract($data);

        $fullViewFilePath = $this->skinDirectory . $viewFilePath . '.php';
        if (file_exists($fullViewFilePath)) {
            include $fullViewFilePath;
        } else {
            // 예외를 던져 오류 처리
            throw new \Exception("뷰 파일을 찾을 수 없습니다: {$fullViewFilePath}");
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