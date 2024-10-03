<?php
// 파일 위치: /config/schemas/template_schema.php

$schema = [
    'schema_group' => '페이지 템플릿 관련 테이블',
    'schema_array' => [
                        'custom_template_lists' => '메인화면/페이지 템플릿 목록',
                        'initial_data' => '설치 시 기본 입력값'
                      ],
    'schema_content' => [
                        'custom_template_lists' => "
                            CREATE TABLE IF NOT EXISTS `custom_template_lists` (
                                `ct_id` INT AUTO_INCREMENT PRIMARY KEY,
                                `cf_id` INT NOT NULL DEFAULT 1,
                                `ct_section_id` VARCHAR(25) NOT NULL DEFAULT '',
                                `ct_admin_subject` VARCHAR(255) NOT NULL DEFAULT '',
                                `ct_position` VARCHAR(20) NOT NULL DEFAULT '',
                                `ct_position_sub` VARCHAR(25) NOT NULL DEFAULT '',
                                `ct_position_subtype` VARCHAR(25) NOT NULL DEFAULT 'menu',
                                `ct_position_subview` ENUM('Y','N') NOT NULL DEFAULT 'Y',
                                `ct_subject_view` VARCHAR(12),
                                `ct_subject` VARCHAR(255),
                                `ct_subject_color` VARCHAR(255),
                                `ct_subject_size` VARCHAR(255),
                                `ct_msubject_size` VARCHAR(255),
                                `ct_subject_pos` VARCHAR(255),
                                `ct_copytext` VARCHAR(255),
                                `ct_copytext_color` VARCHAR(255),
                                `ct_copytext_size` VARCHAR(255),
                                `ct_mcopytext_size` VARCHAR(255),
                                `ct_copytext_pos` VARCHAR(255),
                                `ct_subject_pc_image` VARCHAR(255),
                                `ct_subject_mo_image` VARCHAR(255),
                                `ct_subject_more_link` VARCHAR(255),
                                `ct_subject_more_url` VARCHAR(255),
                                `ct_list_width` TINYINT(1) NOT NULL DEFAULT 1,
                                `ct_list_pc_height` VARCHAR(12),
                                `ct_list_mo_height` VARCHAR(12),
                                `ct_list_pc_padding` VARCHAR(255),
                                `ct_list_mo_padding` VARCHAR(255),
                                `ct_list_bgcolor` VARCHAR(12),
                                `ct_list_bgimage` VARCHAR(255),
                                `ct_list_box_cnt` TINYINT(1) NOT NULL DEFAULT 1,
                                `ct_list_box_effect` VARCHAR(255),
                                `ct_list_box_margin` TINYINT(1) NOT NULL DEFAULT 0,
                                `ct_list_box_wtype` TINYINT(1) NOT NULL DEFAULT 1,
                                `ct_list_box_width` VARCHAR(255),
                                `ct_list_box_pc_padding` VARCHAR(255),
                                `ct_list_box_mo_padding` VARCHAR(255),
                                `ct_list_box_border_width` VARCHAR(255),
                                `ct_list_box_border_color` VARCHAR(255),
                                `ct_list_box_border_round` VARCHAR(255),
                                `ct_list_box_bgcolor` VARCHAR(255),
                                `ct_list_box_bgimage` TEXT,
                                `ct_list_box_itemtype` VARCHAR(255),
                                `ct_list_box_shoptype` VARCHAR(255),
                                `ct_list_box_skin` VARCHAR(255),
                                `ct_list_box_itemcnt` VARCHAR(255),
                                `ct_list_box_pcstyle` VARCHAR(255),
                                `ct_list_box_mostyle` VARCHAR(255),
                                `ct_list_box_pccols` VARCHAR(55),
                                `ct_list_box_mocols` VARCHAR(55),
                                `ct_list_box_items` TEXT,
                                `ct_order` tinyint(4) NOT NULL DEFAULT 1,
                                `ct_use` tinyint(4) NOT NULL DEFAULT 0,
                                `ct_datetime` DATETIME NOT NULL DEFAULT current_timestamp(),
                                `ct_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                KEY idx_cf_id (cf_id),
                                KEY idx_order (ct_order)
                            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'custom_template_items' => "
                            CREATE TABLE IF NOT EXISTS `custom_template_items` (
                                `ci_id` INT AUTO_INCREMENT PRIMARY KEY,
                                `cf_id` INT NOT NULL DEFAULT 1,
                                `ct_id` INT NOT NULL DEFAULT 0,
                                `ci_box_id` INT NOT NULL DEFAULT 0,
                                `ci_type` VARCHAR(25) NOT NULL DEFAULT '',
                                `ci_pc_item` VARCHAR(255) NOT NULL DEFAULT '',
                                `ci_mo_item` VARCHAR(255) NOT NULL DEFAULT '',
                                `ci_link` VARCHAR(255) NOT NULL DEFAULT '',
                                `ci_new_win` TINYINT NOT NULL DEFAULT 0,
                                `ci_content` TEXT,
                                `ci_option` TEXT,
                                KEY idx_cf_id (cf_id),
                                KEY idx_order (ct_id)
                            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'initial_data' => [
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