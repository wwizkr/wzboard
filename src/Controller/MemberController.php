<?php
// 파일 위치: /src/Admin/Controller/MemberController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;

use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Controller\SocialController;

use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
//use Web\PublicHtml\Middleware\CsrfTokenHandler;

class MemberController
{
    protected $container;
    protected $config_domain;
    protected $sessionManager;
    protected $cookieManager;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $socialController;
    protected $formDataMiddleware;
    private $componentsViewHelper;
    
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->membersService = $this->container->get('MembersService');
        $this->membersModel = $this->container->get('MembersModel');
        $this->membersHelper = $this->container->get('MembersHelper');

        $this->sessionManager = $this->container->get('SessionManager');
        $this->cookieManager = $this->container->get('CookieManager');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->socialController = $this->container->get('SocialController');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    public function register($vars)
    {
        $config_domain = $this->container->get('config_domain');
        $contentSkin = $config_domain['cf_skin_content'] ?? 'basic';
        $param = isset($vars['param']) ? $vars['param'] : 'clause';
        $viewPath = 'Content/'.$contentSkin.'/Member/register_'.$param;
        $viewData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($param === 'clause') {
                $socialProvider = $this->socialController->getProviderList();
                $socialItems = !empty($socialProvider) ? $this->componentsViewHelper->renderComponent('socialItems', $socialProvider) : '';
                $viewData['socialProvider'] = $socialItems;
            }

            if ($param === 'join') {
                /*
                 * sns 가입 여부
                $sessionManager = $this->container->get('SessionManager');
                $encryptedProfile = $sessionManager->get('encrypted_social_profile');

                if ($encryptedProfile) {
                    $userProfile = CryptoHelper::decryptJson($encryptedProfile);
                    
                    // $userProfile을 사용하여 회원가입 폼 미리 채우기 등의 작업 수행
                    
                    // 사용 후 세션에서 삭제
                    $sessionManager->set('encrypted_social_profile', null);
                    $sessionManager->set('social_provider', null);
                }
                */
            }

            return [
                "viewPath" => $viewPath,
                "viewData" => $viewData,
                "fullPage" => true,
            ];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }
}