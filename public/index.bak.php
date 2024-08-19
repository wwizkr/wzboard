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

// 의존성 컨테이너 생성
$container = DependencyContainer::getInstance();

// ViewRenderer 클래스 인스턴스 생성
$viewRenderer = new ViewRenderer($container);

// FastRoute 설정을 위한 디스패처 생성
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    // DatabaseInstallerController 라우트 추가
    $r->addRoute('GET', '/install', 'Web\PublicHtml\Controller\DatabaseInstallerController@install');

    // 게시판 라우트 정의 (이 규칙이 위에 있어야 함)
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/board/{boardId}/{method}/{param}', 'BoardController@handle');
    $r->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/board/{boardId}/{method}', 'BoardController@handle');

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

        /*
         * $r->addRoute('GET', '/install', 'Web\PublicHtml\Controller\DatabaseInstallerController@install'); 별도 처리 필요
        */
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
            
            // $method가 설정되지 않은 경우 기본 메서드 설정
            if (!isset($method) || !$method) {
                $method = 'index'; // 예를 들어 기본 메서드를 'index'로 설정
            }
            
            if (method_exists($controllerInstance, $method)) {
                list($viewPath, $viewData) = $controllerInstance->$method($vars);
                $viewRenderer->renderPage($viewPath, [], [], $viewData, []);
            } else {
                echo 'Method not found';
            }
        } else {
            echo 'Controller not found';
        }
            
        break;
}

/*
 * 웹라우트
 도매인/user => /src/Controller/UserController.php index() 호출
 도매인/user/list => /src/Controller/UserController.php list() 호출
 도매인/user/view/id  => /src/Controller/UserController.php view(id) 호출

 * 게시판
 설명:
BoardController 라우트 처리:

BoardController의 handle 메서드는 boardId, method, param 변수를 받아, 해당 게시판의 작업을 처리합니다.
예를 들어 /board/free/list 같은 URL은 BoardController의 list 메서드를 호출하고, /board/free/view/12는 view 메서드를 호출하게 됩니다.
라우팅:

라우터에서 BoardController를 처리할 때, 다른 컨트롤러들과 마찬가지로 클래스와 메서드를 동적으로 호출합니다.
이 구조를 통해 게시판 라우트 또한 동적으로 처리할 수 있으며, boardId에 따라 다양한 게시판을 유연하게 처리할 수 있습니다.
*/