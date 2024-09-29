<?php
// src/Helper/RouteHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Middleware\AuthMiddleware;
use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\View\AdminViewRenderer;
use Web\PublicHtml\View\ViewRenderer;

class RouteHelper
{
    /**
     * 관리자 라우트 처리 함수
     */
    public static function handleAdminRoute(string $handler, array $vars, DependencyContainer $container, AdminViewRenderer $adminViewRenderer): void
    {
        if (!isset($_COOKIE['jwtToken']) || !CryptoHelper::verifyJwtToken($_COOKIE['jwtToken'])) {
            header('Location: /auth/login');
            exit;
        }

        [$controller, $method] = self::extractControllerAndMethod($handler, $vars, 'Admin');
        self::executeControllerMethod($controller, $method, $vars, $container, $adminViewRenderer);
    }

    /**
     * 웹 라우트 처리 함수
     */
    public static function handleWebRoute(string $handler, array $vars, DependencyContainer $container, ViewRenderer $viewRenderer): void
    {
        [$controller, $method] = self::extractControllerAndMethod($handler, $vars, 'PublicHtml');
        self::executeControllerMethod($controller, $method, $vars, $container, $viewRenderer);
    }

    /**
     * API 라우트 처리 함수
     */
    public static function handleApiRoute(string $handler, array $vars, DependencyContainer $container): void
    {
        $apiVersion = $_ENV['API_VERSION'] ?? 'v1';
        $controller = 'Web\\PublicHtml\\Api\\' . $apiVersion . '\\' . ucfirst($vars['controller']) . 'Controller';
        $method = $vars['method'] ?? 'index';

        self::executeApiMethod($controller, $method, $vars, $container);
    }

    /**
     * 템플릿 라우트 처리 함수
     */
    public static function handleTemplateRoute(string $handler, array $vars, DependencyContainer $container): void
    {
        [$controller, $method] = self::extractControllerAndMethod($handler, $vars);
        self::executeApiMethod($controller, $method, $vars, $container);
    }

    /**
     * 컨트롤러와 메서드 추출
     */
    private static function extractControllerAndMethod(string $handler, array $vars, string $namespace = ''): array
    {
        if (isset($vars['boardId'])) {
            return ["Web\\{$namespace}\\Controller\\BoardController", $vars['method'] ?? 'index'];
        }

        if ($handler === 'DynamicController') {
            return ["Web\\{$namespace}\\Controller\\" . ucfirst($vars['controller']) . 'Controller', $vars['method'] ?? 'index'];
        }

        if (strpos($handler, '@') !== false) {
            return explode('@', $handler);
        }

        return [$handler, $vars['method'] ?? 'index'];
    }

    /**
     * 컨트롤러 메서드 실행 (웹/관리자)
     */
    private static function executeControllerMethod(string $controller, string $method, array $vars, DependencyContainer $container, $renderer): void
    {
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                $response = $controllerInstance->$method($vars);
                self::renderResponse($response, $renderer);
            } else {
                echo 'Method not found';
            }
        } else {
            echo 'Controller not found';
        }
    }

    /**
     * API 메서드 실행
     */
    private static function executeApiMethod(string $controller, string $method, array $vars, DependencyContainer $container): void
    {
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                $result = $controllerInstance->$method($vars);
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
     * 응답 렌더링
     */
    private static function renderResponse(array $response, $renderer): void
    {
        $viewPath = $response['viewPath'] ?? null;
        $headData = $response['headData'] ?? [];
        $headerData = $response['headerData'] ?? [];
        $layoutData = $response['layoutData'] ?? [];
        $viewData = $response['viewData'] ?? [];
        $footerData = $response['footerData'] ?? [];
        $footData = $response['footData'] ?? [];
        $fullPage = $response['fullPage'] ?? false;

        $renderer->renderPage($viewPath, $headData, $headerData, $layoutData, $viewData, $footerData, $footData, $fullPage);
    }
}