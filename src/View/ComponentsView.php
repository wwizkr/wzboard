<?php
// src/View/ComponentsView.php

namespace Web\PublicHtml\View;

class ComponentsView
{
    protected $skinDirectory;

    public function __construct($skinName = 'basic')
    {
        // 스킨 디렉토리 설정
        $this->skinDirectory = __DIR__ . "/components/{$skinName}/";
    }

    public function renderMenu($menuData)
    {
        return $this->renderComponent('menu', ['menuData' => $menuData]);
    }

    public function renderPagination($paginationData)
    {
        return $this->renderComponent('pagination', ['paginationData' => $paginationData]);
    }

    public function renderComponent($componentName, $data)
    {
        $templatePath = $this->skinDirectory . "{$componentName}.php";

        if (file_exists($templatePath)) {
            // extract 함수를 통해 배열의 키를 변수로 변환
            extract($data);
            ob_start();
            include $templatePath;
            return ob_get_clean();
        } else {
            return "<p>{$componentName} 컴포넌트를 찾을 수 없습니다.</p>";
        }
    }
}