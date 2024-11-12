<?php
// 파일 위치: /config/schemas/config_schema.php

$schema = [
    'schema_group' => '환경설정 관련 테이블',
    'schema_array' => [
                        'config_domain' => '홈페이지 기본 환경설정 테이블',
                        'initial_data' => '설치 시 기본 입력값'
                      ],
    'schema_content' => [
                        'config_domain' => "
                            CREATE TABLE IF NOT EXISTS `config_domain` (
                                `cf_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                `cf_class` VARCHAR(25) DEFAULT NULL
                                `cf_domain` VARCHAR(55) NOT NULL DEFAULT '',
                                `cf_status` ENUM('Y','N') NOT NULL DEFAULT 'Y',
                                `cf_expire` DATETIME NOT NULL DEFAULT '2099-12-31 23:59:59',
                                `cf_level` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_agency_id` INT(11) NOT NULL DEFAULT 0,
                                `cf_agent_id` INT(11) NOT NULL DEFAULT 0,
                                `cf_super_admin` VARCHAR(25) NOT NULL DEFAULT '',
                                `cf_title` VARCHAR(25) NOT NULL DEFAULT '',
                                `cf_company_owner` VARCHAR(55) DEFAULT NULL,
                                `cf_company_name` VARCHAR(55) DEFAULT NULL,
                                `cf_company_number` VARCHAR(12) DEFAULT NULL,
                                `cf_tongsin_number` VARCHAR(55) DEFAULT NULL,
                                `cf_bugatongsin_number` VARCHAR(55) DEFAULT NULL,
                                `cf_company_tel` VARCHAR(55) DEFAULT NULL,
                                `cf_company_email` VARCHAR(55) DEFAULT NULL,
                                `cf_company_zip` VARCHAR(6) DEFAULT NULL,
                                `cf_company_addr1` VARCHAR(255) DEFAULT NULL,
                                `cf_company_addr2` VARCHAR(255) DEFAULT NULL,
                                `cf_company_addr3` VARCHAR(255) DEFAULT NULL,
                                `cf_info_admin_name` VARCHAR(55) DEFAULT NULL,
                                `cf_info_admin_email` VARCHAR(55) DEFAULT NULL,
                                `cf_company_bankname` VARCHAR(25) DEFAULT NULL,
                                `cf_company_banknumber` VARCHAR(25) DEFAULT NULL,
                                `cf_company_bankuser` VARCHAR(25) DEFAULT NULL,
                                `cf_layout_max_width` INT(11) NOT NULL DEFAULT 1200,
                                `cf_content_max_width` INT(11) NOT NULL DEFAULT 1200,
                                `cf_layout` TINYINT(4) NOT NULL DEFAULT 1,
                                `cf_left_width` INT(11) NOT NULL DEFAULT 0,
                                `cf_right_width` INT(11) NOT NULL DEFAULT 0,
                                `cf_index_wide` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_skin_partials` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_header` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_footer` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_layout` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_content` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_home` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_skin_home` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_color_set` TEXT,
                                `cf_mobile_fix_widget` TINYINT(4) NOT NULL DEFAULT 1,
                                `cf_mobile_fix_widget_skin` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_mobile_panel_widget` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_mobile_panel_widget_skin` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_right_widget` TINYINT(4) NOT NULL DEFAULT 1,
                                `cf_right_widget_skin` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_left_widget` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_left_widget_skin` VARCHAR(25) NOT NULL DEFAULT 'basic',
                                `cf_kakao_chat_url` VARCHAR(255) DEFAULT NULL,
                                `cf_naver_chat_url` VARCHAR(255) DEFAULT NULL,
                                `cf_support` VARCHAR(255) DEFAULT NULL,
                                `cf_pc_page_rows` INT(11) NOT NULL DEFAULT 16,
                                `cf_mo_page_rows` INT(11) NOT NULL DEFAULT 16,
                                `cf_pc_page_nums` INT(11) NOT NULL DEFAULT 10,
                                `cf_mo_page_nums` INT(11) NOT NULL DEFAULT 5,
                                `cf_cert_use` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_auto_register` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_use_email_certify` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_register_level` TINYINT(4) NOT NULL DEFAULT 2,
                                `cf_register_allow` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_auto_levelup` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_use_hp` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_req_hp` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_use_addr` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_req_addr` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_use_recommend` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_use_point` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_join_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_login_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_recommend_member_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_recommend_point_type` TINYINT(4) NOT NULL DEFAULT 1,
                                `cf_recommend_order_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_board_read_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_board_write_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_board_comment_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_board_download_point` INT(11) NOT NULL DEFAULT 0,
                                `cf_editor` VARCHAR(25) NOT NULL DEFAULT 'tinymce',
                                `cf_login_minutes` INT(11) NOT NULL DEFAULT 0,
                                `cf_visit` VARCHAR(255),
                                `cf_visit_del` INT(11) NOT NULL DEFAULT 0,
                                `cf_popular_del` INT(11) NOT NULL DEFAULT 0,
                                `cf_prohibit_id` TEXT DEFAULT NULL,
                                `cf_prohibit_email` TEXT DEFAULT NULL,
                                `cf_social_login_use` TINYINT(4) NOT NULL DEFAULT 0,
                                `cf_social_servicelist` VARCHAR(255) DEFAULT NULL,
                                `cf_naver_clientid` VARCHAR(100) DEFAULT NULL,
                                `cf_naver_secret` VARCHAR(100) DEFAULT NULL,
                                `cf_kakao_rest_key` VARCHAR(100) DEFAULT NULL,
                                `cf_kakao_client_key` VARCHAR(100) DEFAULT NULL,
                                `cf_kakao_js_apikey` VARCHAR(100) DEFAULT NULL,
                                `cf_add_meta` TEXT,
                                `cf_add_script` TEXT,
                                `cf_analytics` TEXT,
                                `cf_seo_keyword` VARCHAR(255),
                                `cf_seo_description` VARCHAR(255),
                                `cf_allow_admin_ip` TEXT,
                                `cf_possible_ip` TEXT,
                                `cf_intercept_ip` TEXT,
                                `cf_optimize_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                                `cf_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                                `cf_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                                `cf_sns_channel_url` TEXT DEFAULT NULL,
                                `cf_naver_visit` VARCHAR(255) DEFAULT NULL,
                                `cf_google_visit` VARCHAR(255) DEFAULT NULL,
                                `cf_use_naver_ad` CHAR(1) NOT NULL DEFAULT 'N',
                                `cf_use_naver_ad_key` VARCHAR(25) DEFAULT NULL,
                                `cf_use_naver_ad_type` VARCHAR(100) DEFAULT NULL
                            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'initial_data' => [
                            'config_domain' => [
                                'data' => [
                                    [
                                        'cf_class' => '1',
                                        'cf_super_admin' => 'admin',
                                    ]
                                ],
                                'encrypt' => []
                            ],
                        ],
                        ],
];

return $schema;
/*
return [
    'board_groups' => "
        CREATE TABLE IF NOT EXISTS board_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL DEFAULT '',
            order_num INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_configs' => "
        CREATE TABLE IF NOT EXISTS board_configs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_id INT NOT NULL DEFAULT 0,
            board_name VARCHAR(100) NOT NULL DEFAULT '',
            board_table VARCHAR(50) NOT NULL DEFAULT '',
            read_level INT DEFAULT 0,
            write_level INT DEFAULT 0,
            download_level INT DEFAULT 0,
            is_use_file BOOLEAN DEFAULT TRUE,
            file_size_limit INT DEFAULT 2097152,
            use_separate_table BOOLEAN DEFAULT FALSE,
            table_name VARCHAR(100) NOT NULL DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY idx_board_table (board_table),
            INDEX idx_group_id (group_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_categories' => "
        CREATE TABLE IF NOT EXISTS board_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL DEFAULT '',
            description VARCHAR(255) NOT NULL DEFAULT '',
            order_num INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_category_mapping' => "
        CREATE TABLE IF NOT EXISTS board_category_mapping (
            id INT AUTO_INCREMENT PRIMARY KEY,
            board_id INT NOT NULL,
            category_id INT NOT NULL,
            board_table VARCHAR(50) NOT NULL DEFAULT '',
            UNIQUE KEY idx_board_category (board_id, category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_articles' => "
        CREATE TABLE IF NOT EXISTS board_articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_id INT NOT NULL DEFAULT 0,
            board_id INT NOT NULL DEFAULT 0,
            category_id INT NOT NULL DEFAULT 0,
            author_id INT NOT NULL,
            title VARCHAR(255) NOT NULL DEFAULT '',
            content TEXT NOT NULL,
            view_count INT DEFAULT 0,
            is_notice BOOLEAN DEFAULT FALSE,
            read_level INT DEFAULT 0,
            download_level INT DEFAULT 0,
            like_count INT DEFAULT 0,
            dislike_count INT DEFAULT 0,
            loc_lat DECIMAL(10,8) DEFAULT NULL,
            loc_lng DECIMAL(11,8) DEFAULT NULL,
            user_ip VARCHAR(25) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_board_id (group_id,board_id),
            INDEX idx_author_id (author_id),
            INDEX idx_location (loc_lat,loc_lng),
            INDEX idx_created_at (created_at),
            INDEX idx_board_category (board_id, category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_comments' => "
        CREATE TABLE IF NOT EXISTS board_comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            article_id INT NOT NULL,
            author_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_article_id (article_id),
            INDEX idx_author_id (author_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_attachments' => "
        CREATE TABLE IF NOT EXISTS board_attachments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            article_id INT NOT NULL,
            filename VARCHAR(255) NOT NULL DEFAULT '',
            filesize INT NOT NULL DEFAULT 0,
            filepath VARCHAR(255) NOT NULL DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_article_id (article_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'board_reactions' => "
        CREATE TABLE IF NOT EXISTS board_reactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            article_id INT NOT NULL,
            user_id INT NOT NULL,
            reaction_type ENUM('like', 'dislike') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY idx_article_user_reaction (article_id, user_id, reaction_type),
            INDEX idx_article_id (article_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",

    'initial_data' => [
        'board_groups' => [
            'data' => [
                ['name' => '일반 게시판', 'order_num' => 1],
                ['name' => '자유 게시판', 'order_num' => 2],
            ],
            'encrypt' => []
        ],
        'board_configs' => [
            'data' => [
                [
                    'group_id' => 1,
                    'board_name' => '공지사항',
                    'board_table' => 'notice',
                    'read_level' => 0,
                    'write_level' => 9,
                    'download_level' => 0,
                ],
                [
                    'group_id' => 2,
                    'board_name' => '자유게시판',
                    'board_table' => 'free',
                    'read_level' => 0,
                    'write_level' => 1,
                    'download_level' => 1,
                ],
            ],
            'encrypt' => []
        ],
    ],
];
*/