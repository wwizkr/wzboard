<?php
// 파일 위치: /config/schemas/menu_schema.php

$schema = [
    'schema_group' => '메뉴 테이블',
    'schema_array' => [
                        'menus' => '메뉴 테이블',
                        'initial_data' => '설치 시 메뉴 기본 입력값'
                      ],
    'schema_content' => [
                        'menus' => "
                            CREATE TABLE IF NOT EXISTS menus (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                me_cate1 VARCHAR(25) NOT NULL DEFAULT '',
                                me_cate2 VARCHAR(25) NOT NULL DEFAULT '',
                                me_code VARCHAR(21) NOT NULL DEFAULT '',
                                me_parent INT(11) NOT NULL DEFAULT 0,
                                me_depth TINYINT(4) NOT NULL DEFAULT 1,
                                me_name VARCHAR(25) NOT NULL DEFAULT '',
                                me_icon VARCHAR(255) DEFAULT NULL,
                                me_image VARCHAR(255) DEFAULT NULL,
                                me_link VARCHAR(255) DEFAULT NULL,
                                me_target VARCHAR(255) DEFAULT NULL,
                                me_fcolor VARCHAR(25) DEFAULT NULL,
                                me_fsize TINYINT(4) NOT NULL DEFAULT 0,
                                me_fweight TINYINT(4) NOT NULL DEFAULT 0,
                                me_class VARCHAR(25) DEFAULT NULL,
                                me_order INT(11) NOT NULL DEFAULT 0,
                                me_pc_use TINYINT(4) NOT NULL DEFAULT 1,
                                me_mo_use TINYINT(4) NOT NULL DEFAULT 1,
                                me_pa_use TINYINT(4) NOT NULL DEFAULT 1,
                                me_title VARCHAR(100) NOT NULL DEFAULT '',
                                UNIQUE KEY idx_code (cf_id,me_code)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                        ],
                        ],
];

return $schema;
