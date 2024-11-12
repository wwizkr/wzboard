<?php
namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Middleware\AuthMiddleware;

class RouteHelper
{
    protected $container;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

    public function handleAdminRoute($handler, $vars, $adminViewRenderer)
    {
        if (!isset($_COOKIE['jwtToken']) || !CryptoHelper::verifyJwtToken($_COOKIE['jwtToken'])) {
            header('Location: /auth/login');
            exit;
        }

        list($controller, $method) = $this->extractControllerAndMethod($handler, $vars, 'Admin');
        $this->executeControllerMethod($controller, $method, $vars, $adminViewRenderer);
    }

    public function handleWebRoute($handler, $vars, $viewRenderer)
    {
        // 플러그인 경로 처리
        if (strpos($handler, 'Plugins\\') === 0) {
            $parts = explode('\\', $handler);
            $pluginName = $parts[1] ?? '';
            $controllerName = $parts[3] ?? '';
            
            $isAdmin = ($controllerName === 'AdminController');
            $method = $vars['method'] ?? 'index';
            $param = $vars['param'] ?? null;
            
            // Admin 인증 처리
            if ($isAdmin) {
                if (!isset($_COOKIE['jwtToken']) || !CryptoHelper::verifyJwtToken($_COOKIE['jwtToken'])) {
                    header('Location: /auth/login');
                    exit;
                }
                $viewRenderer = $this->container->get('AdminViewRenderer');
            }
            
            // 컨트롤러 메소드 실행
            $this->executeControllerMethod($handler, $method, ['param' => $param], $viewRenderer);
            return;
        }
        
        // 기존의 웹 라우트 처리
        list($controller, $method) = $this->extractControllerAndMethod($handler, $vars, 'PublicHtml');
        $this->executeControllerMethod($controller, $method, $vars, $viewRenderer);
    }

    protected function extractControllerAndMethod($handler, $vars, $namespace = '')
    {
        if (isset($vars['boardId']) && $handler === "Web\\{$namespace}\\Controller\\BoardController@comment") {
            return ["Web\\{$namespace}\\Controller\\BoardController", 'comment'];
        } elseif (isset($vars['boardId'])) {
            return ["Web\\{$namespace}\\Controller\\BoardController", $vars['method'] ?? 'index'];
        } elseif ($handler === 'DynamicController') {
            return ["Web\\{$namespace}\\Controller\\" . ucfirst($vars['controller']) . 'Controller', $vars['method'] ?? 'index'];
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            return explode('@', $handler);
        } else {
            $controllerName = ucfirst($vars['controller'] ?? 'Dashboard') . 'Controller';
            return ["Web\\{$namespace}\\Controller\\{$controllerName}", $vars['method'] ?? 'index'];
        }
    }

    protected function executeControllerMethod($controller, $method, $params, $renderer)
    {
        if (!class_exists($controller)) {
            echo "Controller not found: $controller";
            return;
        }

        $controllerInstance = new $controller($this->container);
        if (!method_exists($controllerInstance, $method)) {
            echo "Method not found: $method in $controller";
            return;
        }

        $response = $controllerInstance->$method($params);

        if (!empty($response)) {
            $renderer->renderPage(
                $response['viewPath'] ?? null,
                $response['headData'] ?? [],
                $response['headerData'] ?? [],
                $response['layoutData'] ?? [],
                $response['viewData'] ?? [],
                $response['footerData'] ?? [],
                $response['footData'] ?? [],
                $response['fullPage'] ?? false
            );
        }
    }

    public function handleApiRoute($handler, $vars)
    {
        $apiVersion = $_ENV['API_VERSION'] ?? 'v1';
        $controller = 'Web\\PublicHtml\\Api\\' . $apiVersion . '\\' . ucfirst($vars['controller']) . 'Controller';
        $method = $vars['method'] ?? 'index';
        
        $this->executeJsonMethod($controller, $method, $vars);
    }

    public function handleTemplateRoute($handler, $vars)
    {
        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
            } else {
                $controller = $handler;
                $method = $vars['method'] ?? 'index';
            }
        } else {
            $this->sendJsonResponse(['error' => 'Invalid template handler'], 400);
            return;
        }

        $this->executeJsonMethod($controller, $method, $vars);
    }

    protected function executeJsonMethod($controller, $method, $vars)
    {
        if (!class_exists($controller)) {
            $this->sendJsonResponse(['error' => 'Controller not found'], 404);
            return;
        }

        $controllerInstance = new $controller($this->container);

        if (!method_exists($controllerInstance, $method)) {
            $this->sendJsonResponse(['error' => 'Method not found'], 404);
            return;
        }

        $result = $controllerInstance->$method($vars);
        $this->sendJsonResponse($result);
    }

    protected function sendJsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}