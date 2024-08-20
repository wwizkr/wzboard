<?php
// 파일 위치: /home/web/public_html/bootstrap.php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Web\PublicHtml\Helper\DatabaseQuery;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

// 환경 변수 로드
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 의존성 컨테이너 생성
$container = DependencyContainer::getInstance();

// DatabaseQuery 인스턴스 생성 및 컨테이너에 등록
$container->set('db', DatabaseQuery::getInstance());

// 현재 접속 중인 도메인 가져오기 (www 제외)
$host = preg_replace('/^www\./', '', $_SERVER["SERVER_NAME"]);
$tmp_host = explode(".", $host);
$tmp_owner = array();

foreach($tmp_host as $key => $val) {
    $tmp_owner[] = $val;
}

$owner_domain = implode(".", $tmp_owner);

// 캐시 디렉토리 설정 (고정된 DOMAIN 경로)
$cacheDirectory = __DIR__ . '/storage/cache/DOMAIN';

// 캐시 디렉토리가 없으면 생성
if (!is_dir($cacheDirectory)) {
    if (!mkdir($cacheDirectory, 0777, true)) {
        die('Failed to create directories...');
    }
}

// 캐시 어댑터 생성
$cache = new FilesystemAdapter(
    '', // 네임스페이스 (옵션)
    3600, // 기본 캐시 만료 시간 (초 단위)
    $cacheDirectory // 캐시 파일을 저장할 디렉토리
);

// 캐시 키 생성 (도메인 이름을 '-'로 대체)
$cacheKey = 'config_domain_' . str_replace(".", "-", $owner_domain);

// 캐시에서 데이터 가져오기
$config_domain = $cache->getItem($cacheKey);

if (!$config_domain->isHit()) {
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
        $config_domain->set($encryptedData);
        // 캐시 저장 (3600초 동안 유지)
        $cache->save($config_domain);
    } else {
        $config_domain_data = [];
    }
} else {
    // 캐시된 데이터를 가져와 복호화
    $encryptedData = $config_domain->get();
    $config_domain_data = CryptoHelper::decryptJson($encryptedData);
}

// config_domain 배열을 컨테이너에 등록
$container->set('config_domain', $config_domain_data);

/*
// 스킨 설정
$headerSkin = $_ENV['HEADER_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정
$footerSkin = $_ENV['FOOTER_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정
$layoutSkin = $_ENV['LAYOUT_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정

// 스킨 정보를 컨테이너에 등록
$container->set('headerSkin', $headerSkin);
$container->set('footerSkin', $footerSkin);
$container->set('layoutSkin', $layoutSkin);
*/
// 다른 초기 설정들...