<?php
// 파일 위치: /home/web/public_html/bootstrap.php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Core\DatabaseQuery;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\MenuHelper;
use Web\PublicHtml\Controller\SocialController;

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

// 접속 환경 감지
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$isMobile = preg_match('/(Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini)/i', $userAgent);

// 접속 환경에 따라 설정 값 변경
if ($isMobile) {
    $config_domain_data['cf_page_rows'] = $config_domain_data['cf_mo_page_rows'];
    $config_domain_data['cf_page_nums'] = $config_domain_data['cf_mo_page_nums'];
    $config_domain_data['device_type'] = 'mo';
} else {
    $config_domain_data['cf_page_rows'] = $config_domain_data['cf_pc_page_rows'];
    $config_domain_data['cf_page_nums'] = $config_domain_data['cf_pc_page_nums'];
    $config_domain_data['device_type'] = 'pc';
}

// config_domain 배열을 컨테이너에 등록 --- 전체 수정된 후 삭제할 것.
$container->set('config_domain', $config_domain_data);

// ConfigHelper에 설정 등록
ConfigHelper::setConfig('config_domain', $config_domain_data);

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

// 사용자용 CSRF 토큰이 없는 경우에만 생성
$userCsrfTokenKey = $_ENV['USER_CSRF_TOKEN_KEY'];
$userCsrfToken = $sessionManager->get($userCsrfTokenKey);

if ($userCsrfToken === null) {
    // 세션에 토큰이 없으면 새로 생성
    $userCsrfToken = $sessionManager->generateCsrfToken($userCsrfTokenKey);
}

// 사용자용 CSRF 토큰을 컨테이너에 등록
$container->set('user_csrf_token', $userCsrfToken);

// 서비스 프로바이더 파일 포함
require_once __DIR__ . '/config/serviceProviders.php';

// 서비스와 모델 등록
registerServices($container);