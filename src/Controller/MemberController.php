<?php
// 파일 위치: /src/Admin/Controller/MemberController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\CryptoHelper;

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
        $contentSkin = $config_domain['cf_skin_content'] ?? 'basic';
        $param = isset($vars['param']) ? $vars['param'] : 'clause';
        $viewPath = 'Content/'.$contentSkin.'/Member/register_'.$param;
        $viewData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($param === 'clause') {
                $socialProvider = $this->socialController->getProviderList();
                $socialItems = !empty($socialProvider) ? $this->componentsViewHelper->renderComponent('socialItems', $socialProvider, 'join') : '';
                $viewData['socialProvider'] = $socialItems;
            }

            if ($param === 'join') {
                $encryptedProfile = $this->sessionManager->get('encrypted_social_profile');
                $socialProvider = $this->sessionManager->get('social_provider') ?? null;

                /*
                 * social 가입일 경우 바로 회원 가입 후 로그인
                 */
                if ($encryptedProfile && $socialProvider) {
                    $isSocial = true;
                    $decryptData = CryptoHelper::decryptJson($encryptedProfile) ?? null;
                    $decryptData['data']['social_provider'] = $socialProvider;
                    $decryptData['data']['social_id'] = $decryptData['identifier'];
                    $decryptData['data']['is_social_login'] = 1;
                    $memberData = $decryptData['data'];

                    $result = $this->membersService->insertMemberData($memberData);
                }

                // social이 아닌 경우에만 회원가입 폼 출력.
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