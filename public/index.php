<?php
// 파일 위치: /home/web/public_html/public/index.php

// PHP 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 실행 시간 측정 시작
$startTime = microtime(true);

// Composer의 autoloader 및 기본 환경 설정 파일 로드
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\View\ViewRenderer;
use Web\Admin\View\AdminViewRenderer;
use Web\PublicHtml\Helper\RouteHelper;
use Web\PublicHtml\Middleware\AuthMiddleware;

// 의존성 컨테이너 가져오기
$container = DependencyContainer::getInstance();

// HTTP 메서드와 요청 URI를 가져옴
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// 정적 파일 패턴을 정의 (예: .css, .js, .png, .jpg, .gif, .webp, 등)
if (preg_match('/\.(?:png|jpg|jpeg|gif|webp|css|js|ico)$/', $uri)) {
    //return false; // 웹 서버가 정적 파일을 처리하도록 전달 (이 경우 FastRoute가 아니라 Apache/Nginx가 직접 처리)
}

// 쿼리 스트링을 제거하고 URI 디코딩
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// 끝에 있는 슬래시 처리
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
    header('Location: ' . $uri, true, 301);
    exit;
}

// AuthMiddleware를 통해 인증 처리
AuthMiddleware::handle($uri);

// FastRoute 설정을 위한 디스패처 생성
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // 공통으로 사용되는 HTTP 메서드 배열
    $httpMethods = ['GET', 'POST', 'PUT', 'DELETE'];
    
    // 관리자 라우트 그룹
    $r->addGroup('/admin', function (RouteCollector $r) use ($httpMethods) {
        $r->addRoute('GET', '', 'Web\\Admin\\Controller\\DashboardController@index');
        // **관리자 댓글 라우터 먼저 추가**
        $r->addRoute($httpMethods, '/board/comment/{boardId}[/{articleNo}]', 'Web\\Admin\\Controller\\BoardController@comment');
        // 관리자 게시판 라우트
        $r->addRoute($httpMethods, '/board/{boardId}/{method}[/{param}]', 'Web\\Admin\\Controller\\BoardController@handle');
        // 관리자 동적 라우트
        $r->addRoute($httpMethods, '/{controller}[/{method}[/{param}]]', 'AdminDynamicController');
    });
    
    // API 라우트 그룹
    $apiBaseUrl = $_ENV['API_FULL_BASE_URL'] ?? '/api/v1';
    $r->addGroup($apiBaseUrl, function (RouteCollector $r) use ($httpMethods) {
        $r->addRoute($httpMethods, '/{controller}[/{method}[/{param}]]', 'ApiController');
    });
    
    // DB 설치 라우트
    $r->addRoute('GET', '/install', 'Web\\PublicHtml\\Controller\\DatabaseInstallerController@install');
    
    // **사용자 댓글 라우터 먼저 추가**
    $r->addRoute($httpMethods, '/board/comment/{boardId}[/{articleNo}]', 'Web\\PublicHtml\\Controller\\BoardController@comment');

    // 기본 게시판 라우트
    $r->addRoute($httpMethods, '/board/{boardId}/{method}', 'Web\\PublicHtml\\Controller\\BoardController@handle');
    
    // 게시글 번호가 있는 라우트
    $r->addRoute($httpMethods, '/board/{boardId}/{method}/{param}', 'Web\\PublicHtml\\Controller\\BoardController@handle');
    
    // 게시글 번호와 슬러그가 모두 있는 라우트
    $r->addRoute($httpMethods, '/board/{boardId}/{method}/{param}/{slug}', 'Web\\PublicHtml\\Controller\\BoardController@handle');
    
    
    // 템플릿 관련 라우트 추가
    $r->addRoute('GET', '/template/{method}', 'Web\\PublicHtml\\Controller\\TemplateController');

    // 일반 웹 라우트
    $r->addRoute('GET', '/', 'Web\\PublicHtml\\Controller\\HomeController@index');
    $r->addRoute($httpMethods, '/{controller}[/{method}[/{param}]]', 'DynamicController');
});


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
        
        // 전체 API 기본 URL을 환경 변수에서 가져옴
        $apiFullBaseUrl = $_ENV['API_FULL_BASE_URL'] ?? '/api/v1';
        
        if (strpos($uri, '/admin') === 0) {
            // 관리자 페이지 처리
            $adminViewRenderer = new AdminViewRenderer($container);
            RouteHelper::handleAdminRoute($handler, $vars, $container, $adminViewRenderer);
        } elseif (strpos($uri, '/template') === 0) {
            // 템플릿 요청 처리
            RouteHelper::handleTemplateRoute($handler, $vars, $container);
        } elseif (strpos($uri, $apiFullBaseUrl) === 0) {
            // API 처리
            RouteHelper::handleApiRoute($handler, $vars, $container);
        } else {
            // 일반 웹사이트 처리
            $viewRenderer = new ViewRenderer($container);
            RouteHelper::handleWebRoute($handler, $vars, $container, $viewRenderer);
        }
        break;
}

// 실행 시간 측정 종료
$endTime = microtime(true);
$executionTime = $endTime - $startTime;

// 메모리 사용량 계산 함수
function formatMemoryUsage($size)
{
    $unit = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

// 메모리 사용량 출력
echo "현재 메모리 사용량: " . memory_get_usage() . " bytes (" . formatMemoryUsage(memory_get_usage()) . ")<br>";
echo "최대 메모리 사용량: " . memory_get_peak_usage() . " bytes (" . formatMemoryUsage(memory_get_peak_usage()) . ")<br>";

// 실행 시간 출력
echo "실행 시간: " . round($executionTime, 4) . " 초";
