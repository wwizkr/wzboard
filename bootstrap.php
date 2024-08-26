<?php
// 파일 위치: /home/web/public_html/bootstrap.php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
//use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Web\PublicHtml\Helper\DatabaseQuery;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Controller\MenuController;

// 환경 변수 로드
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 의존성 컨테이너 생성
$container = DependencyContainer::getInstance();

// DatabaseQuery 인스턴스 생성 및 컨테이너에 등록
$container->set('db', DatabaseQuery::getInstance());

// 현재 접속 중인 도메인 가져오기 (www 제외)
$host = preg_replace('/^www\./', '', $_SERVER["SERVER_NAME"]);
$owner_domain = implode(".", array_filter(explode(".", $host)));

// 도메인 기반으로 캐시 디렉토리 설정
$cacheDirectory = $owner_domain;
CacheHelper::initialize($cacheDirectory);

// 환경설정 캐시 키 생성
$configCacheKey = 'config_domain_' . $owner_domain;
$config_domain_data = CacheHelper::getCache($configCacheKey);

if ($config_domain_data === null) {
    // 캐시에 데이터가 없는 경우, 데이터베이스에서 정보 조회
    $db = $container->get('db');
    $query = "SELECT * FROM " . (new class {
        use DatabaseHelperTrait;
    })->getTableName('config_domain') . " WHERE cf_domain = :cf_domain";
    $stmt = $db->query($query, ['cf_domain' => $owner_domain]);
    $config_domain_data = $db->fetch($stmt);

    if ($config_domain_data) {
        // JSON으로 변환 후 암호화하여 캐시에 저장
        $encryptedData = CryptoHelper::encryptJson($config_domain_data);
        CacheHelper::setCache($configCacheKey, $encryptedData);
    } else {
        $config_domain_data = [];
    }
} else {
    // 캐시된 데이터를 복호화
    $config_domain_data = CryptoHelper::decryptJson($config_domain_data);
}

// config_domain 배열을 컨테이너에 등록
$container->set('config_domain', $config_domain_data);

// MenuController를 통해 트리화된 메뉴 데이터를 가져옴
$menuController = new MenuController($owner_domain);
$menuTree = $menuController->getMenuData();

// 트리화된 메뉴 데이터를 컨테이너에 등록
$container->set('menu_datas', $menuTree);

// SessionManager 인스턴스 생성 및 컨테이너에 등록
$sessionManager = new SessionManager();
$container->set('session_manager', $sessionManager);

// CsrfTokenHandler 인스턴스 생성 및 컨테이너에 등록
$csrfTokenHandler = new CsrfTokenHandler($sessionManager);
$container->set('csrf_token_handler', $csrfTokenHandler);

// 사용자용 CSRF 토큰 생성 및 컨테이너에 등록
$userCsrfToken = $sessionManager->generateCsrfToken($_ENV['USER_CSRF_TOKEN_KEY']);
$container->set('user_csrf_token', $userCsrfToken);