<?php
// 파일 위치: /config/schemas/clause_schema.php

$schema = [
    'schema_group' => '이용약관 테이블',
    'schema_array' => [
                        'clause_table' => '이용약관 테이블',
                        'initial_data' => '설치 시 기본 입력값'
                      ],
    'schema_content' => [
                        'clause_table' => "
                            CREATE TABLE IF NOT EXISTS `clause_table` (
                                `ct_id` INT AUTO_INCREMENT PRIMARY KEY,
                                `cf_id` INT NOT NULL DEFAULT 1,
                                `ct_page_type` VARCHAR(50) NOT NULL DEFAULT '',
                                `ct_page_id` VARCHAR(25) NOT NULL DEFAULT '',
                                `ct_page_url` VARCHAR(255) NOT NULL DEFAULT '',
                                `ct_subject` VARCHAR(255) NOT NULL DEFAULT '',
                                `ct_content` MEDIUMTEXT NOT NULL DEFAULT '',
                                `ct_confirm` VARCHAR(255) NOT NULL DEFAULT '',
                                `ct_kind` VARCHAR(8) NOT NULL DEFAULT '',
                                `ct_order` INT NOT NULL DEFAULT 0,
                                `ct_use` TINYINT NOT NULL DEFAULT 1,
                                `ct_datetime` DATETIME NOT NULL DEFAULT current_timestamp(),
                                `ct_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                KEY idx_cf_id (cf_id)
                            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'initial_data' => [
                        ],
                        ],
];

return $schema;