<?php
// 파일 위치: /config/serviceProviders.php

use Web\PublicHtml\Core\DependencyContainer;

/*
 * bootstarp 등록
 * -- $this->db(/Core/DatabaseQeury)
 */

// Auth
use Web\PublicHtml\Service\AuthService;

// Board
use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\Admin\Helper\AdminBoardsHelper;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Helper\BoardsHelper;

// Member
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\MembersHelper;

// Helper
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\ComponentsViewHelper;
use Web\PublicHtml\Helper\CacheHelper;

// Middleware
use Web\PublicHtml\Middleware\AuthMiddleware;
use Web\PublicHtml\Middleware\NavigationMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Middleware\FormDataMiddleware;

// Template
use Web\PublicHtml\Service\TemplateService;
use Web\Admin\Service\AdminTemplateService;
use Web\Admin\Model\AdminTemplateModel;

// ETC
use Web\PublicHtml\Controller\SocialController;
use Web\PublicHtml\Core\ViewRenderer;
use Web\PublicHtml\Core\AdminViewRenderer;
use Web\Admin\Service\AdminBannerService;


function registerServices(DependencyContainer $container)
{
    $container->addFactory('AuthService', function($c) {
        return new AuthService($c);
    });

    // Boards
    $container->addFactory('AdminBoardsModel', function($c) {
        return new AdminBoardsModel($c);
    });
    $container->addFactory('AdminBoardsService', function($c) {
        return new AdminBoardsService($c);
    });
    $container->addFactory('AdminBoardsHelper', function($c) {
        return new AdminBoardsHelper($c);
    });
    $container->addFactory('BoardsModel', function($c) {
        return new BoardsModel($c);
    });
    $container->addFactory('BoardsService', function($c) {
        return new BoardsService($c);
    });
    $container->addFactory('BoardsHelper', function($c) {
        return new BoardsHelper($c);
    });

    // Members
    $container->addFactory('MembersModel', function($c) {
        return new MembersModel($c);
    });
    $container->addFactory('MembersService', function($c) {
        return new MembersService($c);
    });
    $container->addFactory('MembersHelper', function($c) {
        return new MembersHelper($c);
    });

    // Helpers
    $container->addFactory('SessionManager', function($c) {
        return new SessionManager();
    });
    $container->addFactory('CsrfTokenHandler', function($c) {
        return new CsrfTokenHandler($c->get('SessionManager'));
    });
    $container->addFactory('FormDataMiddleware', function($c) {
        return new FormDataMiddleware($c->get('CsrfTokenHandler'));
    });
    $container->addFactory('CookieManager', function($c) {
        return new CookieManager();
    });
    $container->addFactory('ConfigHelper', function($c) {
        return new ConfigHelper();
    });
    $container->addFactory('CryptoHelper', function($c) {
        return new CryptoHelper();
    });
    $container->addFactory('ComponentsViewHelper', function($c) {
        return new ComponentsViewHelper();
    });
    $container->addFactory('CacheHelper', function($c) {
        return new CacheHelper();
    });

    // Middleware
    $container->addFactory('AuthMiddleware', function($c) {
        return new AuthMiddleware($c);
    });
    $container->addFactory('NavigationMiddleware', function($c) {
        return new NavigationMiddleware($c);
    });

    // Template
    $container->addFactory('TemplateService', function($c) {
        return new TemplateService($c);
    });
    $container->addFactory('AdminTemplateService', function($c) {
        return new AdminTemplateService($c);
    });
    $container->addFactory('AdminTemplateModel', function($c) {
        return new AdminTemplateModel($c);
    });

    // ETC
    $container->addFactory('SocialController', function ($c) {
        return new SocialController($c);
    });
    // ViewRenderer 및 AdminViewRenderer 등록
    $container->set('ViewRenderer', function($c) {
        return new ViewRenderer($c);
    });
    $container->set('AdminViewRenderer', function($c) {
        return new AdminViewRenderer($c);
    });
    $container->set('AdminBannerService', function($c) {
        return new AdminBannerService($c);
    });
}