<?php
// 파일 위치: /home/web/public_html/public/admin.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use Web\PublicHtml\Helper\DependencyContainer;
use Web\Admin\View\ViewRenderer;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$container = DependencyContainer::getInstance();

// ViewRenderer 클래스 인스턴스 생성
$viewRenderer = new ViewRenderer($container);

$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    // 게시판 라우트 정의 (이 규칙이 위에 있어야 함)
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/board/{boardId}/{method}/{param}', 'BoardController@handle');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/board/{boardId}/{method}', 'BoardController@handle');

    // DashboardController를 /admin 경로에 매핑
    $r->addRoute('GET', '/admin', 'DashboardController@index'); // /admin 경로에서 DashboardController의 index 메서드 호출

    // 기본 패턴 라우트 추가 (동적 라우팅)
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}/{method}/{id:\d+}', 'DynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}/{method}', 'DynamicController');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/admin/{controller}', 'DynamicController');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// FastRoute로 요청을 디스패치
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

        if ($handler === 'BoardController@handle') {
            // 게시판 컨트롤러 호출 처리
            $controllerNamespace = "Web\\Admin\\Controller\\BoardController";

            if (class_exists($controllerNamespace)) {
                $controllerInstance = new $controllerNamespace($container);
                $viewData = $controllerInstance->handle($vars);
                $viewRenderer->renderPage($viewData[0], [], [], $viewData[1], []);
            } else {
                echo '404 Not Found';
            }
        } elseif ($handler === 'DynamicController') {
            // URL에서 동적 컨트롤러 및 메서드 결정
            $controller = ucfirst($vars['controller']) . 'Controller';
            $method = $vars['method'] ?? 'index';
            $controllerNamespace = "Web\\Admin\\Controller\\{$controller}";

            if (class_exists($controllerNamespace) && method_exists($controllerNamespace, $method)) {
                $controllerInstance = new $controllerNamespace($container);
                $viewData = $controllerInstance->$method($vars);
                $viewRenderer->renderPage($viewData[0], [], [], $viewData[1], []);
            } else {
                echo '404 Not Found';
            }
        }
        break;
}

/*
2.2 컨트롤러 및 메서드 규칙 설정
URL 패턴에 따라 컨트롤러와 메서드를 자동으로 매핑합니다.
DynamicController가 라우팅을 처리하며, URL 세그먼트를 기준으로 컨트롤러와 메서드를 동적으로 호출합니다.
예를 들어:

/admin/user/list는 Web\Admin\Controller\UserController::list를 호출합니다.
/admin/product/view/10은 Web\Admin\Controller\ProductController::view(10)을 호출합니다.
*/