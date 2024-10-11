<?php
// 파일 위치: /config/schemas/banner_schema.php

$schema = [
    'schema_group' => '배너 테이블',
    'schema_array' => [
                        'banner_table' => '배너 테이블',
                        'initial_data' => '설치 시 기본 입력값'
                      ],
    'schema_content' => [
                        'banner_table' => "
                            CREATE TABLE IF NOT EXISTS `banner_table` (
                                `ba_id` INT AUTO_INCREMENT PRIMARY KEY,
                                `cf_id` INT NOT NULL DEFAULT 1,
                                `ba_position` VARCHAR(25) NOT NULL DEFAULT '',
                                `ba_pc_image` VARCHAR(100) NOT NULL DEFAULT '',
                                `ba_mo_image` VARCHAR(100) NOT NULL DEFAULT '',
                                `ba_bg_image` VARCHAR(100) NOT NULL DEFAULT '',
                                `ba_title` VARCHAR(50) NOT NULL DEFAULT '',
                                `ba_link` VARCHAR(100) NOT NULL DEFAULT '',
                                `ba_pc_bgcolor` VARCHAR(10) NOT NULL DEFAULT '',
                                `ba_mo_bgcolor` VARCHAR(10) NOT NULL DEFAULT '',
                                `ba_utv_url` VARCHAR(100) NOT NULL DEFAULT '',
                                `ba_new_win` TINYINT NOT NULL DEFAULT 0,
                                `ba_begin_time` VARCHAR(25),
                                `ba_end_time` VARCHAR(25),
                                `ba_hit` INT NOT NULL DEFAULT 0,
                                `ba_order` INT NOT NULL DEFAULT 0,
                                `ba_use` TINYINT NOT NULL DEFAULT 1,
                                `ba_datetime` DATETIME NOT NULL DEFAULT current_timestamp(),
                                `ba_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                KEY idx_cf_id (cf_id)
                            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'initial_data' => [
                        ],
                        ],
];

return $schema;