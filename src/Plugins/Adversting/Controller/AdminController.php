<?php
/**
 * 광고 관리 관리자 컨트롤러
 * 
 */

namespace Plugins\Adversting\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminController
{
    protected $container;
    protected $config_domain;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $formDataMiddleware;

    /**
     * AdminController 생성자
     * 
     * @param DependencyContainer $container 의존성 컨테이너
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $container->get('ConfigHelper')->getConfig('config_domain');
        $this->membersModel = $container->get('MembersModel');
        $this->membersService = $container->get('MembersService');
        $this->membersHelper = $container->get('MembersHelper');
        $this->formDataMiddleware = $container->get('FormDataMiddleware');
    }

    public function config()
    {
        $viewData = [
            'title' => '광고 상품 환경 설정',
            'config_domain' => $this->config_domain,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/config',
            'viewData' => $viewData,
        ];
    }

    public function nshopList()
    {
        $viewData = [
            'title' => '네이버 쇼핑 상품 관리',
            'config_domain' => $this->config_domain,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/nshopList',
            'viewData' => $viewData,
        ];
    }
}