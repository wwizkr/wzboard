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

// Trial
use Web\Admin\Model\AdminTrialModel;
use Web\Admin\Service\AdminTrialService;
use Web\Admin\Helper\AdminTrialHelper;
use Web\PublicHtml\Model\TrialModel;
use Web\PublicHtml\Service\TrialService;
use Web\PublicHtml\Helper\TrialHelper;

// Member
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\MembersHelper;

// Helper
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\ComponentsViewHelper;

// Middleware
use Web\PublicHtml\Middleware\AuthMiddleware;
use Web\PublicHtml\Middleware\NavigationMiddleware;

// Template
use Web\PublicHtml\Service\TemplateService;
use Web\Admin\Service\AdminTemplateService;
use Web\Admin\Model\AdminTemplateModel;

// ETC
use Web\PublicHtml\Controller\SocialController;
use Web\PublicHtml\Core\ViewRenderer;
use Web\PublicHtml\Core\AdminViewRenderer;


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

    // Trial -- Plugin으로 변환 완료 후 전체 삭제
    $container->addFactory('AdminTrialService', function($c) {
        return new AdminTrialService($c);
    });
    $container->addFactory('AdminTrialModel', function($c) {
        return new AdminTrialModel($c);
    });
    $container->addFactory('AdminTrialHelper', function($c) {
        return new AdminTrialHelper($c);
    });
    $container->addFactory('TrialService', function($c) {
        return new TrialService($c);
    });
    $container->addFactory('TrialModel', function($c) {
        return new TrialModel($c);
    });
    /*
     * 삭제
    $container->addFactory('TrialHelper', function($c) {
        return new TrialHelper($c);
    });
    */

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
    $container->addFactory('ComponentsViewHelper', function($c) {
        return new ComponentsViewHelper();
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
}