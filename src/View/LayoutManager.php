<?php
namespace Web\PublicHtml\View;

class LayoutManager
{
    private $config;
    private $viewRenderer;

    public function __construct(array $config, ViewRenderer $viewRenderer)
    {
        $this->config = $config;
        $this->viewRenderer = $viewRenderer;
    }

    public function renderLayout($me_code = '')
    {
        $layout = $this->determineLayout($me_code);
        
        echo '<div id="container">';
        
        foreach ($layout as $section => $config) {
            if ($config['display']) {
                $this->renderSection($section, $config);
            }
        }
        
        echo '<div id="container_wrap">';
    }

    private function determineLayout($me_code)
    {
        $layout = [
            'left' => ['display' => false, 'width' => 0],
            'content' => ['display' => true, 'width' => '100%'],
            'right' => ['display' => false, 'width' => 0]
        ];

        if (defined('_INDEX_') && $this->config['cf_index_wide'] == '0') {
            return $layout;
        }

        $layout['left']['display'] = $this->shouldDisplaySection('left', $me_code);
        $layout['right']['display'] = $this->shouldDisplaySection('right', $me_code);

        if ($layout['left']['display']) {
            $layout['left']['width'] = (int)$this->config['cf_left_width'] + 25;
        }
        if ($layout['right']['display']) {
            $layout['right']['width'] = (int)$this->config['cf_right_width'] + 25;
        }

        return $layout;
    }

    private function shouldDisplaySection($section, $me_code)
    {
        if (!$me_code || $me_code == 'mb') {
            return false;
        }

        // 여기에 기존의 복잡한 로직을 넣을 수 있습니다.
        // 예: SQL 쿼리를 통한 항목 수 확인 등

        return true; // 간단한 예시를 위해 항상 true 반환
    }

    private function renderSection($section, $config)
    {
        if ($section === 'content') {
            return; // content는 이 메소드에서 처리하지 않음
        }

        $class = "mobile-none";
        $style = "flex: 0 0 {$config['width']}px; max-width: {$config['width']}px;";
        
        echo "<aside id=\"aside_{$section}\" class=\"{$class}\" style=\"{$style}\">";
        $this->viewRenderer->render("WZ_PATH/{$section}");
        echo '</aside>';
    }
}