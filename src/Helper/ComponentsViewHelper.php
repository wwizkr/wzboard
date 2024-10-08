<?php
namespace Web\PublicHtml\Helper;

class ComponentsViewHelper
{
    protected $skinDirectory;
    protected static $templateCache = [];
    protected static $templateCacheTimes = [];

    public function __construct($skinName = 'basic')
    {
        $this->skinDirectory = WZ_SRC_PATH . "/View/components/{$skinName}/";
    }

    public function renderMenu($config_domain, $menuData, $me_code = '')
    {
        return $this->renderComponent('menu', ['config_domain' => $config_domain, 'menuData' => $menuData, 'me_code' => $me_code]);
    }

    public function renderPagination($paginationData)
    {
        return $this->renderComponent('pagination', ['paginationData' => $paginationData]);
    }

    public function renderComponent($componentName, $data, $page = '')
    {
        $templatePath = $this->skinDirectory . "{$componentName}.php";
        
        if (!file_exists($templatePath)) {
            error_log("Template file not found: {$templatePath}");
            return "<p>Error: {$componentName} component not found.</p>";
        }

        if (!isset(self::$templateCache[$componentName]) || $this->isTemplateUpdated($componentName, $templatePath)) {
            self::$templateCache[$componentName] = file_get_contents($templatePath);
            self::$templateCacheTimes[$componentName] = filemtime($templatePath);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        try {
            include $templatePath;
        } catch (\Exception $e) {
            error_log("Error rendering component {$componentName}: " . $e->getMessage());
            return "<p>Error rendering {$componentName} component.</p>";
        }
        $output = ob_get_clean();

        // Check if output is empty
        if (empty(trim($output))) {
            error_log("Warning: Empty output for component {$componentName}");
        }

        return $output;
    }

    private function isTemplateUpdated($componentName, $templatePath)
    {
        $lastModifiedTime = filemtime($templatePath);
        return !isset(self::$templateCacheTimes[$componentName]) || self::$templateCacheTimes[$componentName] < $lastModifiedTime;
    }
}