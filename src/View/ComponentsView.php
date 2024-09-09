<?php
namespace Web\PublicHtml\View;

class ComponentsView
{
    protected $skinDirectory;
    protected static $templateCache = []; // 템플릿 캐시를 위한 정적 변수
    protected static $templateCacheTimes = []; // 템플릿 파일 수정 시간을 저장하는 정적 변수

    public function __construct($skinName = 'basic')
    {
        $this->skinDirectory = __DIR__ . "/components/{$skinName}/";
    }

    public function renderMenu($deviceType, $menuData)
    {
        return $this->renderComponent('menu', ['config_domain' => ['device_type' => $deviceType], 'menuData' => $menuData]);
    }

    public function renderPagination($paginationData)
    {
        return $this->renderComponent('pagination', ['paginationData' => $paginationData]);
    }

    public function renderComponent($componentName, $data)
    {
        $templatePath = $this->skinDirectory . "{$componentName}.php";

        if (!isset(self::$templateCache[$componentName]) || $this->isTemplateUpdated($componentName, $templatePath)) {
            if (file_exists($templatePath)) {
                self::$templateCache[$componentName] = file_get_contents($templatePath); // 파일을 읽어서 캐시에 저장
                self::$templateCacheTimes[$componentName] = filemtime($templatePath); // 파일의 마지막 수정 시간을 저장
            } else {
                return "<p>{$componentName} 컴포넌트를 찾을 수 없습니다.</p>";
            }
        }

        // 캐싱된 템플릿 사용
        $template = self::$templateCache[$componentName];

        extract($data); // 데이터를 변수로 추출
        ob_start();
        include $templatePath; // eval 대신 include를 사용하여 템플릿을 로드
        return ob_get_clean();
    }

    private function isTemplateUpdated($componentName, $templatePath)
    {
        $lastModifiedTime = filemtime($templatePath);
        return !isset(self::$templateCacheTimes[$componentName]) || self::$templateCacheTimes[$componentName] < $lastModifiedTime;
    }
}
?>