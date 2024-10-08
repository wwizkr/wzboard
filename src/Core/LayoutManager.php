<?php
namespace Web\PublicHtml\Core;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\TemplateViewHelper;

class LayoutManager
{
    private $container;
    private $config_domain;
    protected $templateService;
    protected $templateViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->templateService = $this->container->get('TemplateService');
        $this->templateViewHelper = new TemplateViewHelper($container);
    }
    
    public function renderLayoutOpen($isIndex, $me_code)
    {
        $layout = $this->determineLayout($me_code, $isIndex);

        $leftTemplate = [];
        if ($layout['left']['display']) {
            $leftTemplate = $this->templateService->getSideTemplateData('template', 'left', $me_code, 'menu');
        }

        $rightTemplate = [];
        if ($layout['right']['display']) {
            $rightTemplate = $this->templateService->getSideTemplateData('template', 'right', $me_code, 'menu');
        }

        $pageTopTemplate = [];
        if (!$isIndex) {
            $pageTopTemplate = $this->templateService->getPageTemplateData('template', 'pagetop', $me_code, 'menu');
        }

        ob_start();
        
        if (!$isIndex || $this->config_domain['cf_index_wide'] != '0') {
            echo '<div id="container">'.PHP_EOL;
            
            if ($layout['left']['display']) {
                echo "<aside id=\"aside_left\" class=\"mobile-none\" style=\"flex: 0 0 {$layout['left']['width']}px; max-width:{$layout['left']['width']}px;\">".PHP_EOL;
                    echo !empty($leftTemplate) ? $this->templateViewHelper->render($leftTemplate) : '';
                echo '</aside>';
            }
            
            if ($layout['right']['display']) {
                echo "<aside id=\"aside_right\" class=\"mobile-none\" style=\"flex: 0 0 {$layout['right']['width']}px; max-width:{$layout['right']['width']}px;\">".PHP_EOL;
                    echo !empty($rightTemplate) ? $this->templateViewHelper->render($rightTemplate) : '';
                echo '</aside>';
            }
            
            echo '<div id="container_wrap">'.PHP_EOL;
        } else {
            echo '<div id="container">'.PHP_EOL;
            echo '<div id="container_wrap">'.PHP_EOL;
        }

        if (!$isIndex) {
            echo 'test pagetop Content';
            echo !empty($pageTopTemplate) ? $this->templateViewHelper->render($pageTopTemplate) : '';
        }

        return ob_get_clean();
    }

    public function renderLayoutClose($isIndex, $me_code)
    {
        $pageFootTemplate = [];
        if (!$isIndex) {
            $pageFootTemplate = $this->templateService->getPageTemplateData('template', 'pagefoot', $me_code, 'menu');
        }

        ob_start();
        echo '</div><!-- End container_wrap--->'.PHP_EOL;
        echo '</div><!-- End container--->'.PHP_EOL;

        if (!$isIndex) {
            echo 'test pagefoot Content';
            echo !empty($pageFootTemplate) ? $this->templateViewHelper->render($pageFootTemplate) : '';
        }

        return ob_get_clean();
    }

    public function renderSubContent($position, $isIndex, $me_code)
    {
        $contentTemplate = [];
        if (!$isIndex) {
            $contentTemplate = $this->templateService->getPageTemplateData('template', $position, $me_code, 'menu');
        }

        ob_start();
            
        if (!$isIndex) {
            echo 'test ' . $position . ' Content';
            echo !empty($contentTemplate) ? $this->templateViewHelper->render($contentTemplate) : '';
        }

        return ob_get_clean();
    }
    
    public function determineLayout($me_code, $isIndex)
    {
        $layout = [
            'left' => ['display' => false, 'width' => 0],
            'content' => ['display' => true, 'width' => '100%'],
            'right' => ['display' => false, 'width' => 0]
        ];

        // 메인 페이지이고 와이드 레이아웃 설정이 되어 있으면 컨텐츠만 표시
        if ($isIndex && $this->config_domain['cf_index_wide'] == '0') {
            return $layout;
        }

        // cf_layout 설정에 따라 레이아웃 결정
        switch ($this->config_domain['cf_layout']) {
            case '2': // 왼쪽 섹션 및 컨텐츠
                $layout['left']['display'] = true;
                break;
            case '3': // 컨텐츠 및 오른쪽 섹션
                $layout['right']['display'] = true;
                break;
            case '4': // 왼쪽, 오른쪽 섹션 및 내용 컨텐츠
                $layout['left']['display'] = true;
                $layout['right']['display'] = true;
                break;
            case '1': // 내용 컨텐츠만
            default:
                // 기본 레이아웃 (내용 컨텐츠만)
                break;
        }

        // 너비 설정
        if ($layout['left']['display']) {
            $layout['left']['width'] = (int)$this->config_domain['cf_left_width'];
        }
        if ($layout['right']['display']) {
            $layout['right']['width'] = (int)$this->config_domain['cf_right_width'];
        }

        // 컨텐츠 영역의 너비 조정
        $contentWidth = 100 - $layout['left']['width'] - $layout['right']['width'];
        $layout['content']['width'] = $contentWidth . '%';

        return $layout;
    }
}