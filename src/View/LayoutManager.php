<?php
namespace Web\PublicHtml\View;

use Web\PublicHtml\Core\DependencyContainer;

class LayoutManager
{
    private $container;
    private $config_domain;
    private $viewRenderer;

    public function __construct(DependencyContainer $container, ViewRenderer $viewRenderer)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->viewRenderer = $viewRenderer;
    }
    
    public function renderLayoutOpen($isIndex, $me_code)
    {
        $layout = $this->determineLayout($me_code, $isIndex);

        ob_start();
        
        if (!$isIndex || $this->config_domain['cf_index_wide'] != '0') {
            echo '<div id="container">'.PHP_EOL;
            
            if ($layout['left']['display']) {
                echo "<aside id=\"aside_left\" class=\"mobile-none\" style=\"flex: 0 0 {$layout['left']['width']}px; max-width:{$layout['left']['width']}px;\">".PHP_EOL;
                //include(WZ_PATH.'/left.php');
                echo '</aside>';
            }
            
            if ($layout['right']['display']) {
                echo "<aside id=\"aside_right\" class=\"mobile-none\" style=\"flex: 0 0 {$layout['right']['width']}px; max-width:{$layout['right']['width']}px;\">".PHP_EOL;
                //include(WZ_PATH.'/right.php');
                echo '</aside>';
            }
            
            echo '<div id="container_wrap">'.PHP_EOL;
        } else {
            echo '<div id="container">'.PHP_EOL;
            echo '<div id="container_wrap">'.PHP_EOL;
        }

        if (!$isIndex) {
            // Placeholder for pagetop template data
            // $this->viewRenderer->renderTemplateData('pagetop', $me_code);
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

    private function shouldDisplaySection($section, $me_code)
    {
        if (!$me_code || $me_code == 'mb') {
            return false;
        }
        
        /*
        $table_name = $this->container->get('db_table_prefix') . 'custom_template_lists';
        
        $sql = "SELECT COUNT(ct_id) AS cnt FROM {$table_name} 
                WHERE cf_id = ? AND ct_use = '0' 
                AND ct_position = ? AND ct_position_sub = ?";
        
        $stmt = $this->container->get('db')->prepare($sql);
        $stmt->execute([$this->config_domain['cf_id'], $section, $me_code]);
        $result = $stmt->fetch();

        if ($result['cnt'] == 0) {
            $length = strlen($me_code) / 2;
            for ($i = 0; $i < $length; $i++) {
                $cut = strlen($me_code) - (($i * 2) + 2);
                $pa_mecode = ($cut == 0) ? 'all' : substr($me_code, 0, $cut);
                
                $sql = "SELECT COUNT(ct_id) AS cnt FROM {$table_name} 
                        WHERE cf_id = ? AND ct_use = '0'
                        AND ct_position = ? AND ct_position_sub = ? AND ct_position_subview = 'Y'";
                
                $stmt = $this->container->get('db')->prepare($sql);
                $stmt->execute([$this->config_domain['cf_id'], $section, $pa_mecode]);
                $result = $stmt->fetch();
                
                if ($result['cnt'] > 0) {
                    return true;
                }
            }
        }
        

        return $result['cnt'] > 0;
        */
    }
}