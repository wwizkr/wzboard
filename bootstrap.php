<?php
// 파일 위치: /home/web/public_html/bootstrap.php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Web\PublicHtml\Helper\DatabaseQuery;
use Web\PublicHtml\Helper\DependencyContainer;

// 환경 변수 로드
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 의존성 컨테이너 생성
$container = DependencyContainer::getInstance();

// DatabaseQuery 인스턴스 생성 및 컨테이너에 등록
$container->set('db', DatabaseQuery::getInstance());

// 스킨 설정
$headerSkin = $_ENV['HEADER_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정
$footerSkin = $_ENV['FOOTER_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정
$layoutSkin = $_ENV['LAYOUT_SKIN'] ?? 'basic'; // 환경변수 또는 기본값으로 설정

// 스킨 정보를 컨테이너에 등록
$container->set('headerSkin', $headerSkin);
$container->set('footerSkin', $footerSkin);
$container->set('layoutSkin', $layoutSkin);

// 다른 초기 설정들...