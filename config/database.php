<?php
// .env 파일에서 환경 변수를 로드합니다. 사용 여부 체크..test_db_connect 에서 사용. 지금 필요한가???
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

// .env 파일 로드
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 데이터베이스 설정
return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'wwiz_web',
    'username' => $_ENV['DB_USERNAME'] ?? 'wwiz_web',
    'password' => $_ENV['DB_PASSWORD'] ?? 'Coo1jazz^^',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'prefix' => $_ENV['DB_PREFIX'] ?? 'wz_',
];