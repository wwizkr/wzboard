<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\TemplateViewHelper;

class HomeController
{
    protected $container;
    protected $config_domain;
    protected $templateService;
    protected $templateViewHelper;
    protected $viewRenderer;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->viewRenderer = $this->container->get('ViewRenderer');
        $this->templateService = $this->container->get('TemplateService');
        $this->templateViewHelper = new TemplateViewHelper($container);
    }
    
    public function index()
    {
        $skin = $this->config_domain['cf_skin_home'] ?? 'basic';
        $templateData = $this->templateService->getHomeTemplateData();

        // TemplateViewHelper�� ����Ͽ� ���ø� ������ ������
        $renderedContent = $this->templateViewHelper->render($templateData);
        
        return [
            "viewPath" => "Home/{$skin}/index",
            "viewData" => [
                "content" => $renderedContent,
            ]
        ];
    }
}