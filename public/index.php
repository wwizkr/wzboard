<?php
// 파일 위치: /home/web/public_html/public/index.php

// PHP 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Composer의 autoloader 및 기본 환경 설정 파일 로드
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\View\ViewRenderer;
use Web\Admin\View\AdminViewRenderer;
use Web\PublicHtml\Middleware\AuthMiddleware;
use Web\PublicHtml\Helper\RouteHelper;

// ViewRenderer 클래스 인스턴스 생성 (일반 웹사이트와 관리자용)
$viewRenderer = new ViewRenderer($container);
$adminViewRenderer = new AdminViewRenderer($container);

// FastRoute 설정을 위한 디스패처 생성
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // 공통으로 사용되는 HTTP 메서드 배열
    $httpMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    // 관리자 라우트 그룹
    $r->addGroup('/admin', function (RouteCollector $r) use ($httpMethods) {
        $r->addRoute('GET', '', 'Web\\Admin\\Controller\\DashboardController@index');
        $r->addRoute($httpMethods, '/board/{boardId}/{method}[/{param}]', 'Web\\Admin\\Controller\\BoardController@handle');
        
        // 관리자 동적 라우트
        $r->addRoute($httpMethods, '/{controller}[/{method}[/{id:\d+}]]', 'AdminDynamicController');
    });

    // API 라우트 그룹
    $apiBaseUrl = $_ENV['API_BASE_URL'] ?? '/api/v1';
    $r->addGroup($apiBaseUrl, function (RouteCollector $r) use ($httpMethods) {
        $r->addRoute($httpMethods, '/{controller}/{method}[/{id:\d+}]', 'ApiController');
    });

    // 웹사이트 게시판 라우트
    $r->addRoute('GET', '/install', 'Web\\PublicHtml\\Controller\\DatabaseInstallerController@install');
    $r->addRoute($httpMethods, '/board/{boardId}/{method}[/{param}]', 'Web\\PublicHtml\\Controller\\BoardController@handle');

    // 일반 웹 라우트
    $r->addRoute('GET', '/', 'Web\\PublicHtml\\Controller\\HomeController@index');
    $r->addRoute($httpMethods, '/{controller}[/{method}[/{id:\d+}]]', 'DynamicController');
});

// HTTP 메서드와 요청 URI를 가져옴
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// 쿼리 스트링을 제거하고 URI 디코딩
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// FastRoute로 요청을 디스패치하여 라우트 매핑 처리
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        if (strpos($uri, '/admin') === 0) {
            // 관리자 페이지 처리
            RouteHelper::handleAdminRoute($handler, $vars, $container, $adminViewRenderer);
        } elseif (strpos($uri, $_ENV['API_BASE_URL'] ?? '/api/v1') === 0) {
            // API 처리
            RouteHelper::handleApiRoute($handler, $vars, $container);
        } else {
            // 일반 웹사이트 처리
            RouteHelper::handleWebRoute($handler, $vars, $container, $viewRenderer);
        }
        break;
}