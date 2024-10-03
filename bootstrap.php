<?php
// 파일 위치: /home/web/public_html/bootstrap.php
// PHP 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 기본시간대 설정
date_default_timezone_set('Asia/Seoul');

// 주요 경로 상수 정의
define('WZ_PROJECT_ROOT', __DIR__);
define('WZ_PUBLIC_PATH', WZ_PROJECT_ROOT . '/public');
define('WZ_STORAGE_PATH',WZ_PUBLIC_PATH . '/storage');
define('WZ_SRC_PATH', WZ_PROJECT_ROOT . '/src');

//카테고리 단계 문자열 길이
define('WZ_CATEGORY_LENGTH', 3);

require_once WZ_PROJECT_ROOT . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Core\DatabaseQuery;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Helper\MenuHelper;
use Web\PublicHtml\Controller\SocialController;
use Web\PublicHtml\View\ViewRenderer;
use Web\Admin\View\AdminViewRenderer;

// 환경 변수 로드
$dotenv = Dotenv::createImmutable(WZ_PROJECT_ROOT);
$dotenv->load();

// 의존성 컨테이너 생성
$container = DependencyContainer::getInstance();

// DatabaseQuery 인스턴스 생성 및 컨테이너에 등록
$container->set('db', DatabaseQuery::getInstance());

// 서비스 프로바이더 파일 포함
require_once WZ_PROJECT_ROOT . '/config/serviceProviders.php';
require_once WZ_PROJECT_ROOT . '/config/configProviders.php';

// 서비스와 설정 등록
registerServices($container);
registerConfigs($container);

// ConfigProvider 인스턴스 가져오기
$configProvider = $container->get('ConfigProvider');

// ConfigHelper에 설정 등록
//ConfigHelper::setConfig('config_domain', $container->get('config_domain'));

// MenuController를 통해 트리화된 메뉴 데이터를 가져옴
$menuTree = MenuHelper::getMenuTree();

// 트리화된 메뉴 데이터를 컨테이너에 등록
$container->set('menu_datas', $menuTree);

// SessionManager
$sessionManager = new SessionManager();
$container->set('SessionManager', $sessionManager);

// CsrfTokenHandler
$container->addFactory('CsrfTokenHandler', function($c) {
    return new CsrfTokenHandler($c->get('SessionManager'));
});

// FormDataMiddleware
$container->addFactory('FormDataMiddleware', function($c) {
    return new FormDataMiddleware($c->get('CsrfTokenHandler'));
});

// SocialLogin
$container->addFactory('SocialController', function ($c) {
    return new SocialController($c);
});

// 사용자용 CSRF 토큰 생성
$userCsrfTokenKey = $_ENV['USER_CSRF_TOKEN_KEY'];
$userCsrfToken = $sessionManager->get($userCsrfTokenKey) ?? $sessionManager->generateCsrfToken($userCsrfTokenKey);
$container->set('user_csrf_token', $userCsrfToken);

// ViewRenderer 및 AdminViewRenderer 등록
$container->set('ViewRenderer', function($c) {
    return new ViewRenderer($c);
});

$container->set('AdminViewRenderer', function($c) {
    return new AdminViewRenderer($c);
});