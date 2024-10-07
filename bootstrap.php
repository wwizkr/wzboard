<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Seoul');

define('WZ_PROJECT_ROOT', __DIR__);
define('WZ_PUBLIC_PATH', WZ_PROJECT_ROOT . '/public');
define('WZ_STORAGE_PATH',WZ_PUBLIC_PATH . '/storage');
define('WZ_SRC_PATH', WZ_PROJECT_ROOT . '/src');
define('WZ_CATEGORY_LENGTH', 3);

require_once WZ_PROJECT_ROOT . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Core\DatabaseQuery;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\MenuHelper;

$dotenv = Dotenv::createImmutable(WZ_PROJECT_ROOT);
$dotenv->load();

$container = DependencyContainer::getInstance();
$container->set('db', DatabaseQuery::getInstance());

require_once WZ_PROJECT_ROOT . '/config/serviceProviders.php';
require_once WZ_PROJECT_ROOT . '/config/configProviders.php';

registerServices($container);
registerConfigs($container);

$configProvider = $container->get('ConfigProvider'); //CacheHelper::initialize() 초기화
$menuTree = MenuHelper::getMenuTree();
$container->set('menu_datas', $menuTree);

$userCsrfTokenKey = $_ENV['USER_CSRF_TOKEN_KEY'];
$userCsrfToken = $container->get('SessionManager')->get($userCsrfTokenKey) ?? $container->get('SessionManager')->generateCsrfToken($userCsrfTokenKey);
$container->set('user_csrf_token', $userCsrfToken);