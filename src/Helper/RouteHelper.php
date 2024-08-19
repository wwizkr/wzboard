<?php

namespace Web\PublicHtml\Helper;

class RouteHelper
{
    public static function handleAdminRoute($handler, $vars, $container, $adminViewRenderer)
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
        } else {
            $controllerName = ucfirst($vars['controller'] ?? 'Dashboard') . 'Controller';
            $controller = "Web\\Admin\\Controller\\{$controllerName}";
            $method = $vars['method'] ?? 'index';
        }
        
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

    public static function handleApiRoute($handler, $vars, $container)
    {
        $controller = 'Web\\PublicHtml\\Api\\v1\\' . ucfirst($vars['controller']) . 'Controller';
        $method = $vars['method'] ?? 'index';
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                $result = $controllerInstance->$method($vars);
                // API 응답 처리 (예: JSON 출력)
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Method not found']);
            }
        } else {
            echo json_encode(['error' => 'Controller not found']);
        }
    }

    public static function handleWebRoute($handler, $vars, $container, $viewRenderer)
    {
        if ($handler === 'DynamicController') {
            $controller = 'Web\\PublicHtml\\Controller\\' . ucfirst($vars['controller']) . 'Controller';
            $method = $vars['method'] ?? 'index';
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
        } else {
            echo 'Invalid handler';
            return;
        }
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller($container);
            if (method_exists($controllerInstance, $method)) {
                list($viewPath, $viewData) = $controllerInstance->$method($vars);
                $viewRenderer->renderPage($viewPath, [], [], [], $viewData, [], []);
            } else {
                echo 'Method not found';
            }
        } else {
            echo 'Controller not found';
        }
    }
}