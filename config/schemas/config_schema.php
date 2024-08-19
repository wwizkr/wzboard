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
                            `cf_id` int(11) NOT NULL,
                            `cf_domain` varchar(55) NOT NULL DEFAULT '',
                            `cf_status` enum('Y','N') NOT NULL DEFAULT 'Y',
                            `cf_expire` datetime NOT NULL DEFAULT '2099-12-31 23:59:59',
                            `cf_level` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_agency_id` int(11) NOT NULL DEFAULT 0,
                            `cf_agent_id` int(11) NOT NULL DEFAULT 0,
                            `cf_super_admin` varchar(25) NOT NULL DEFAULT '',
                            `cf_title` varchar(25) NOT NULL DEFAULT '',
                            `cf_company_owner` varchar(55) DEFAULT NULL,
                            `cf_company_name` varchar(55) DEFAULT NULL,
                            `cf_company_number` varchar(12) DEFAULT NULL,
                            `cf_tongsin_number` varchar(55) DEFAULT NULL,
                            `cf_bugatongsin_number` varchar(55) DEFAULT NULL,
                            `cf_company_tel` varchar(55) DEFAULT NULL,
                            `cf_company_email` varchar(55) DEFAULT NULL,
                            `cf_company_zip` varchar(6) DEFAULT NULL,
                            `cf_company_addr1` varchar(255) DEFAULT NULL,
                            `cf_company_addr2` varchar(255) DEFAULT NULL,
                            `cf_company_addr3` varchar(255) DEFAULT NULL,
                            `cf_info_admin_name` varchar(55) DEFAULT NULL,
                            `cf_info_admin_email` varchar(55) DEFAULT NULL,
                            `cf_company_bankname` varchar(25) DEFAULT NULL,
                            `cf_company_banknumber` varchar(25) DEFAULT NULL,
                            `cf_company_bankuser` varchar(25) DEFAULT NULL,
                            `cf_max_width` int(11) NOT NULL DEFAULT 1200,
                            `cf_layout` tinyint(4) NOT NULL DEFAULT 1,
                            `cf_left_width` int(11) NOT NULL DEFAULT 0,
                            `cf_right_width` int(11) NOT NULL DEFAULT 0,
                            `cf_index_wide` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_skin_basic` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_skin_header` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_skin_footer` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_skin_content` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_skin_index` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_mobile_fix_widget` tinyint(4) NOT NULL DEFAULT 1,
                            `cf_mobile_fix_widget_skin` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_mobile_panel_widget` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_mobile_panel_widget_skin` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_right_widget` tinyint(4) NOT NULL DEFAULT 1,
                            `cf_right_widget_skin` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_left_widget` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_left_widget_skin` varchar(25) NOT NULL DEFAULT 'basic',
                            `cf_kakao_chat_url` varchar(255) DEFAULT NULL,
                            `cf_naver_chat_url` varchar(255) DEFAULT NULL,
                            `cf_support` varchar(255) DEFAULT NULL,
                            `cf_pc_page_rows` int(11) NOT NULL DEFAULT 16,
                            `cf_mo_page_rows` int(11) NOT NULL DEFAULT 16,
                            `cf_pc_page_nums` int(11) NOT NULL DEFAULT 10,
                            `cf_mo_page_nums` int(11) NOT NULL DEFAULT 5,
                            `cf_cert_use` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_use_email_certify` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_register_level` tinyint(4) NOT NULL DEFAULT 2,
                            `cf_register_allow` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_auto_levelup` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_use_hp` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_req_hp` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_use_addr` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_req_addr` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_use_recommend` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_use_point` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_join_point` int(11) NOT NULL DEFAULT 0,
                            `cf_login_point` int(11) NOT NULL DEFAULT 0,
                            `cf_recommend_member_point` int(11) NOT NULL DEFAULT 0,
                            `cf_recommend_point_type` tinyint(4) NOT NULL DEFAULT 1,
                            `cf_recommend_order_point` int(11) NOT NULL DEFAULT 0,
                            `cf_board_read_point` int(11) NOT NULL DEFAULT 0,
                            `cf_board_write_point` int(11) NOT NULL DEFAULT 0,
                            `cf_board_comment_point` int(11) NOT NULL DEFAULT 0,
                            `cf_board_download_point` int(11) NOT NULL DEFAULT 0,
                            `cf_editor` varchar(50) NOT NULL DEFAULT 'smarteditor2',
                            `cf_login_minutes` int(11) NOT NULL DEFAULT 0,
                            `cf_visit` varchar(255) DEFAULT NULL,
                            `cf_visit_del` int(11) NOT NULL DEFAULT 0,
                            `cf_popular_del` int(11) NOT NULL DEFAULT 0,
                            `cf_prohibit_id` text DEFAULT NULL,
                            `cf_prohibit_email` text DEFAULT NULL,
                            `cf_social_login_use` tinyint(4) NOT NULL DEFAULT 0,
                            `cf_social_servicelist` varchar(255) DEFAULT NULL,
                            `cf_naver_clientid` varchar(100) DEFAULT NULL,
                            `cf_naver_secret` varchar(100) DEFAULT NULL,
                            `cf_kakao_rest_key` varchar(100) DEFAULT NULL,
                            `cf_kakao_client_key` varchar(100) DEFAULT NULL,
                            `cf_kakao_js_apikey` varchar(100) DEFAULT NULL,
                            `cf_add_meta` text DEFAULT NULL,
                            `cf_add_script` text DEFAULT NULL,
                            `cf_analytics` text DEFAULT NULL,
                            `cf_seo_keyword` varchar(255) DEFAULT NULL,
                            `cf_seo_description` varchar(255) DEFAULT NULL,
                            `cf_allow_admin_ip` text DEFAULT NULL,
                            `cf_possible_ip` text DEFAULT NULL,
                            `cf_intercept_ip` text DEFAULT NULL,
                            `cf_optimize_date` date NOT NULL DEFAULT '0000-00-00',
                            `cf_datetime` datetime NOT NULL DEFAULT current_timestamp(),
                            `cf_modified` datetime NOT NULL DEFAULT current_timestamp(),
                            `cf_sns_channel_url` text DEFAULT NULL,
                            `cf_naver_visit` varchar(255) DEFAULT NULL,
                            `cf_google_visit` varchar(255) DEFAULT NULL,
                            `cf_use_naver_ad` char(1) NOT NULL DEFAULT 'Y',
                            `cf_use_naver_ad_key` varchar(25) DEFAULT NULL,
                            `cf_use_naver_ad_type` varchar(100) DEFAULT NULL,
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",

                        'initial_data' => [
                            'config_domain' => [
                                'data' => [
                                    ['cf_super_admin' => 'admin']
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