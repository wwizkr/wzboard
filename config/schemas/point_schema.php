<?php
// 파일 위치: /config/schemas/point_schema.php

$schema = [
    'schema_group' => '포인트 관련 테이블',
    'schema_array' => [
                        'points' => '포인트 테이블',
                        'points_compress' => '포인트 압축 테이블',
                        'initial_data' => '설치 시 기본 입력값'
                      ],
    'schema_content' => [
                        'points' => "
                            CREATE TABLE IF NOT EXISTS points (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                point INT NOT NULL DEFAULT 0,
                                point_content VARCHAR(255) NOT NULL DEFAULT '',
                                point_expire TINYINT NOT NULL DEFAULT 0,
                                point_expire_date DATE NOT NULL DEFAULT '9999-12-31',
                                point_type VARCHAR(25) NOT NULL DEFAULT '적립',
                                point_rel_type VARCHAR(25) NOT NULL DEFAULT '',
                                point_rel_id VARCHAR(100) NOT NULL DEFAULT '',
                                point_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                                KEY idx_user_id (cf_id, mb_id),
                                KEY idx_point_type (point_type),
                                UNIQUE KEY idx_point_rel (point_rel_type, point_rel_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'points_compress' => "
                            CREATE TABLE IF NOT EXISTS points_compress (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                point INT NOT NULL DEFAULT 0,
                                compress_start_date DATE NOT NULL,
                                compress_end_date DATE NOT NULL,
                                point_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                                KEY idx_user_id (cf_id, mb_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'cashs' => "
                            CREATE TABLE IF NOT EXISTS cashs (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                cash INT NOT NULL DEFAULT 0,
                                cash_content VARCHAR(255) NOT NULL DEFAULT '',
                                cash_expire TINYINT NOT NULL DEFAULT 0,
                                cash_expire_date DATE NOT NULL DEFAULT '9999-12-31',
                                cash_type VARCHAR(25) NOT NULL DEFAULT '적립',
                                cash_rel_type VARCHAR(25) NOT NULL DEFAULT '',
                                cash_rel_id VARCHAR(100) NOT NULL DEFAULT '',
                                cash_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                                KEY idx_user_id (cf_id, mb_id),
                                KEY idx_cash_type (cash_type),
                                UNIQUE KEY idx_point_rel (cash_rel_type, cash_rel_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'cashs_compress' => "
                            CREATE TABLE IF NOT EXISTS cashs_compress (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                cash INT NOT NULL DEFAULT 0,
                                compress_start_date DATE NOT NULL,
                                compress_end_date DATE NOT NULL,
                                cash_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                                KEY idx_user_id (cf_id, mb_id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'initial_data' => [
                            'points' => [
                                'data' => [
                                    [
                                        'cf_id' => 1,
                                        'mb_id' => 'admin',
                                        'point' => 10000000,
                                        'point_content' => '최고관리자 적립금',
                                        'point_type' => '적립',
                                        'point_rel_type' => '@admin',
                                        'point_rel_id' => 'admin-create',
                                    ],
                                ],
                                'encrypt' => [
                                ]
                            ],
                        ],
                        ],
];

return $schema;
