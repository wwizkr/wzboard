<?php
// src/Helper/RouteHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Middleware\AuthMiddleware;

class RouteHelper
{
    /**
     * 관리자 라우트 처리 함수
     * 
     * @param string $handler 라우트 핸들러 (컨트롤러와 메서드 정보)
     * @param array $vars 라우트 변수
     * @param object $container 의존성 주입 컨테이너
     * @param object $adminViewRenderer 관리자 페이지 렌더러
     */
    public static function handleAdminRoute($handler, $vars, $container, $adminViewRenderer)
    {
        // JWT 토큰을 통한 인증 검증
        if (!isset($_COOKIE['jwtToken']) || !CryptoHelper::verifyJwtToken($_COOKIE['jwtToken'])) {
            header('Location: /auth/login');
            exit;
        }

        // 컨트롤러와 메서드 추출
        if (isset($vars['boardId']) && $handler === 'Web\\Admin\\Controller\\BoardController@comment') {
            // 댓글 처리 라우팅
            $controller = "Web\\Admin\\Controller\\BoardController";
            $method = 'comment';
        } elseif (isset($vars['boardId'])) { // boardId가 있는 경우
            $controller = "Web\\Admin\\Controller\\BoardController";
            $method = $vars['method'] ?? 'index';
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
        } else {
            $controllerName = ucfirst($vars['controller'] ?? 'Dashboard') . 'Controller';
            $controller = "Web\\Admin\\Controller\\{$controllerName}";
            $method = $vars['method'] ?? 'index';
        }

        // 컨트롤러 및 메서드가 존재하는지 확인하고 호출
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
    }

    /**
     * 웹 라우트 처리 함수
     * 
     * @param string $handler 라우트 핸들러 (컨트롤러와 메서드 정보)
     * @param array $vars 라우트 변수
     * @param object $container 의존성 주입 컨테이너
     * @param object $viewRenderer 뷰 렌더러
     */
    public static function handleWebRoute($handler, $vars, $container, $viewRenderer)
    {
        // 컨트롤러와 메서드 추출
        if (isset($vars['boardId']) && $handler === 'Web\\PublicHtml\\Controller\\BoardController@comment') {
            // 댓글 처리 라우팅
            $controller = "Web\\PublicHtml\\Controller\\BoardController";
            $method = 'comment';
        } elseif (isset($vars['boardId'])) { // boardId가 있는 경우
            $controller = "Web\\PublicHtml\\Controller\\BoardController";
            $method = $vars['method'] ?? 'index';
        } elseif($handler === 'DynamicController') {
            $controller = 'Web\\PublicHtml\\Controller\\' . ucfirst($vars['controller']) . 'Controller';
            $method = $vars['method'] ?? 'index';
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
        } else {
            echo 'Invalid handler';
            return;
        }
        
        // 컨트롤러 및 메서드가 존재하는지 확인하고 호출
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                $response = $controllerInstance->$method($vars);

                $viewPath = $response['viewPath'] ?? null;
                $headData = $response['headData'] ?? [];
                $headerData = $response['headerData'] ?? [];
                $layoutData = $response['layoutData'] ?? [];
                $viewData = $response['viewData'] ?? [];
                $footerData = $response['footerData'] ?? [];
                $footData = $response['footData'] ?? [];
                $fullPage = $response['fullPage'] ?? false;

                $viewRenderer->renderPage($viewPath, $headData, $headerData, $layoutData, $viewData, $footerData, $footData, $fullPage);
            } else {
                echo 'Method not found';
            }
        } else {
            echo 'Controller not found';
        }
    }

    /**
     * API 라우트 처리 함수
     * 
     * @param string $handler 라우트 핸들러 (컨트롤러와 메서드 정보)
     * @param array $vars 라우트 변수
     * @param object $container 의존성 주입 컨테이너
     */
    public static function handleApiRoute($handler, $vars, $container)
    {
        $apiVersion = $_ENV['API_VERSION'] ?? 'v1'; // 'V1'에서 'v1'로 변경

        // API 컨트롤러와 메서드 설정
        $controller = 'Web\\PublicHtml\\Api\\' . $apiVersion . '\\' . ucfirst($vars['controller']) . 'Controller';
        $method = $vars['method'] ?? 'index';
        
        // 컨트롤러 및 메서드가 존재하는지 확인하고 호출
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                $result = $controllerInstance->$method($vars);
                // API 응답을 JSON으로 반환
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Method not found']);
            }
        } else {
            echo json_encode(['error' => 'Controller not found']);
        }
    }

    /**
     * 템플릿 라우트 처리 함수
     * 
     * @param string $handler 라우트 핸들러 (컨트롤러와 메서드 정보)
     * @param array $vars 라우트 변수
     * @param object $container 의존성 주입 컨테이너
     */
    public static function handleTemplateRoute($handler, $vars, $container)
    {
        // 핸들러에서 컨트롤러와 메서드를 분리
        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
            } else {
                // 핸들러가 컨트롤러만 제공된 경우
                $controller = $handler;
                $method = $vars['method'] ?? null; // 경로에서 동적으로 메서드 추출
            }
        } else {
            echo json_encode(['error' => 'Invalid template handler']);
            return;
        }

        // 컨트롤러 및 메서드가 존재하는지 확인하고 호출
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if ($method && method_exists($controllerInstance, $method)) {
                $result = $controllerInstance->$method($vars);
                // 템플릿 로딩 응답 처리 (예: JSON 출력)
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Method not found']);
            }
        } else {
            echo json_encode(['error' => 'Controller not found']);
        }
    }
}
