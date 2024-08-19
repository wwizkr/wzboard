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

// ViewRenderer 클래스 인스턴스 생성 (일반 웹사이트와 관리자용)
$viewRenderer = new ViewRenderer($container);
$adminViewRenderer = new AdminViewRenderer($container);

// FastRoute 설정을 위한 디스패처 생성
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // 관리자 경로
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/board/{boardId}/{method}/{param}', 'Web\\Admin\\Controller\\BoardController@handle');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/board/{boardId}/{method}', 'Web\\Admin\\Controller\\BoardController@handle');

    // DashboardController를 /admin 경로에 매핑
    $r->addRoute('GET', '/admin', 'Web\\Admin\\Controller\\DashboardController@index');

    // 기본 패턴 라우트 추가 (동적 라우팅)
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}/{method}/{id:\d+}', 'AdminDynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}/{method}', 'AdminDynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}', 'AdminDynamicController@index');

    // 웹사이트 경로
    $r->addRoute('GET', '/install', 'Web\\PublicHtml\\Controller\\DatabaseInstallerController@install');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/board/{boardId}/{method}/{param}', 'Web\\PublicHtml\\Controller\\BoardController@handle');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/board/{boardId}/{method}', 'Web\\PublicHtml\\Controller\\BoardController@handle');

    // API 라우트 정의
    $apiBaseUrl = $_ENV['API_BASE_URL'] ?? '/api/v1';
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], "{$apiBaseUrl}/{controller}/{method}/{id:\d+}", 'ApiController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], "{$apiBaseUrl}/{controller}/{method}", 'ApiController');

    // 웹 라우트 정의 (다양한 HTTP 메서드를 지원)
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/{controller}/{method}/{id:\d+}', 'DynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/{controller}/{method}', 'DynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/{controller}', 'DynamicController@index');
    $r->addRoute('GET', '/', 'Web\\PublicHtml\\Controller\\HomeController@index');
});

// HTTP 메서드와 요청 URI를 가져옴
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// 쿼리 스트링을 제거하고 URI 디코딩
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// cf_id 설정
if (!isset($_SESSION['cf_id'])) {
    $_SESSION['cf_id'] = 1;  // 첫 접속 시 cf_id를 1로 설정
}
//인스턴스 등록
$container->set('cf_id', 1);

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
            list($controller, $method) = explode('@', $handler);
            if (class_exists($controller)) {
                $controllerInstance = new $controller($container);
                if (method_exists($controllerInstance, $method)) {
                    list($viewPath, $viewData) = $controllerInstance->$method($vars);
                    $adminViewRenderer->renderPage($viewPath, [], [], [], $viewData, [], []);
                } else {
                    echo 'Method not found';
                }
            } else {
                echo 'Controller not found';
            }
        } else {
            // 일반 웹사이트 처리
            if ($handler === 'ApiController') {
                // API 컨트롤러 처리 로직
                $controller = 'Web\\PublicHtml\\Api\\v1\\' . ucfirst($vars['controller']) . 'Controller';
                $method = $vars['method'] ?? 'index'; // 기본 메서드를 설정 (예: index)
            } else if ($handler === 'DynamicController') {
                $controller = 'Web\\PublicHtml\\Controller\\' . ucfirst($vars['controller']) . 'Controller';
                $method = $vars['method'] ?? 'index'; // 기본 메서드를 설정 (예: index)
            } else if ($handler === 'BoardController@handle') {
                // 게시판 컨트롤러 처리 로직
                $controller = 'Web\\PublicHtml\\Controller\\BoardController';
                $method = 'handle';
            } else {
                list($controller, $method) = explode('@', $handler);
            }

            if (class_exists($controller)) {
                $controllerInstance = new $controller($container);
                
                if (method_exists($controllerInstance, $method)) {
                    list($viewPath, $viewData) = $controllerInstance->$method($vars);
                    // renderPage($path,$headData,$headerData,$layoutData,$viewData,$footerData,$footData)
                    $viewRenderer->renderPage($viewPath, [], [], [], $viewData, [], []);
                } else {
                    echo 'Method not found';
                }
            } else {
                echo 'Controller not found';
            }
        }
        break;
}
