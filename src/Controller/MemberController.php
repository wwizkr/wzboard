<?php
// 파일 위치: /src/Admin/Controller/MemberController.php

namespace Web\PublicHtml\Controller;

use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class BoardController
{
    protected $container;
    protected $sessionManager;
    protected $adminBoardsModel;
    protected $adminBoardsService;
    protected $boardsHelper;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $boardsService;
    protected $boardsModel;
    protected $config_domain;
    protected $formDataMiddleware;
    
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = ConfigHelper::getConfig('config_domain');
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->sessionManager = new SessionManager();
        $adminBoardsModel = new AdminBoardsModel($this->container);
        $this->adminBoardsService = new AdminBoardsService($adminBoardsModel);

        $membersModel = new MembersModel($this->container);
        $this->membersService = new MembersService($membersModel);

        $boardsModel = new BoardsModel($this->container);
        $this->boardsHelper = new BoardsHelper($this->adminBoardsService, $boardsModel);
        $this->membersHelper = new MembersHelper($this->container, $membersModel);

        $csrfTokenHandler = new CsrfTokenHandler($this->sessionManager);
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);

        $this->boardsService = new BoardsService(
            $boardsModel,
            $this->boardsHelper,
            $this->membersHelper,
            $this->formDataMiddleware
        );

        $this->boardsHelper->setBoardsService($this->boardsService);
    }

    public function register()
    {

    }
}