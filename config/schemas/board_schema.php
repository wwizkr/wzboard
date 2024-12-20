<?php
// 파일 위치: /config/schemas/board_schema.php

$schema = [
    'schema_group' => '게시판 관련 테이블',
    'schema_array' => [
                        'board_groups' => '게시판 그룹 테이블',
                        'board_configs' => '게시판 설정 테이블',
                        'board_categories' => '게시판 카테고리 테이블',
                        'board_category_mapping' => '카테고리 매핑 테이블',
                        'board_articles' => '게시판 게시글 테이블',
                        'board_comments' => '게시판 댓글 테이블',
                        'board_attachements' => '게시판 첨부파일테이블',
                        'board_links' => '게시판 링크 테이블',
                        'board_reactions'=> '게시판 반응테이블',
                        'initial_data' => '설치 시 게시판별 기본 입력값'
                      ],
    'schema_content' => [
                        'board_groups' => "
                            CREATE TABLE IF NOT EXISTS board_groups (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 1,
                                group_id VARCHAR(25) NOT NULL DEFAULT '',
                                group_name VARCHAR(100) NOT NULL DEFAULT '',
                                group_admin VARCHAR(255) DEFAULT NULL,
                                list_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                read_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                write_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                comment_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                download_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                order_num INT DEFAULT 0
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_configs' => "
                            CREATE TABLE IF NOT EXISTS board_configs (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 1,
                                group_no INT NOT NULL DEFAULT 0,
                                board_admin VARCHAR(255) DEFAULT NULL,
                                board_name VARCHAR(100) NOT NULL DEFAULT '',
                                board_id VARCHAR(25) NOT NULL DEFAULT '',
                                board_skin VARCHAR(25) NOT NULL DEFAULT 'basic',
                                board_editor VARCHAR(25),
                                list_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                read_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                write_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                comment_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                download_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                read_point INT NOT NULL NOT NULL DEFAULT 0,
                                write_point INT NOT NULL NOT NULL DEFAULT 0,
                                download_point INT NOT NULL DEFAULT 0,
                                comment_point INT NOT NULL DEFAULT 0,
                                is_article_reaction VARCHAR(255) NOT NULL DEFAULT '',
                                is_use_comment TINYINT NOT NULL DEFAULT 1,
                                is_comment_reaction VARCHAR(255) NOT NULL DEFAULT '',
                                is_use_link TINYINT NOT NULL DEFAULT 0,
                                is_use_file TINYINT NOT NULL DEFAULT 1,
                                file_size_limit INT DEFAULT 2097152,
                                board_list_type TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                use_separate_table TINYINT NOT NULL DEFAULT 0,
                                table_name VARCHAR(100) NOT NULL DEFAULT '',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_board_id (board_id),
                                INDEX idx_group_no (group_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_categories' => "
                            CREATE TABLE IF NOT EXISTS board_categories (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 1,
                                category_name VARCHAR(25) NOT NULL DEFAULT '',
                                category_desc VARCHAR(255) NOT NULL DEFAULT '',
                                list_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                read_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                write_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                comment_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                download_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                order_num INT DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_category_mapping' => "
                            CREATE TABLE IF NOT EXISTS board_category_mapping (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL,
                                category_no INT NOT NULL,
                                board_id VARCHAR(25) NOT NULL DEFAULT '',
                                UNIQUE KEY idx_board_category (board_no, category_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_articles' => "
                            CREATE TABLE IF NOT EXISTS board_articles (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 1,
                                group_no INT NOT NULL DEFAULT 0,
                                board_no INT NOT NULL DEFAULT 0,
                                category_no INT NOT NULL DEFAULT 0,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                nickName VARCHAR(50) NOT NULL DEFAULT '',
                                password VARCHAR(255) NOT NULL DEFAULT '',
                                title VARCHAR(255) NOT NULL DEFAULT '',
                                slug VARCHAR(100) NOT NULL DEFAULT '',
                                content TEXT NOT NULL,
                                view_count INT NOT NULL DEFAULT 0,
                                is_notice BOOLEAN DEFAULT FALSE,
                                read_level INT NOT NULL DEFAULT 0,
                                download_level INT NOT NULL DEFAULT 0,
                                comment_count INT NOT NULL DEFAULT 0,
                                like_count INT NOT NULL DEFAULT 0,
                                dislike_count INT NOT NULL DEFAULT 0,
                                read_point INT NOT NULL DEFAULT 0,
                                loc_lat DECIMAL(10,8) DEFAULT NULL,
                                loc_lng DECIMAL(11,8) DEFAULT NULL,
                                user_ip VARCHAR(25) DEFAULT NULL,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                latest_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX idx_board_no (group_no,board_no),
                                INDEX idx_mb_id (mb_id),
                                INDEX idx_location (loc_lat,loc_lng),
                                INDEX idx_created_at (created_at),
                                INDEX idx_board_category (board_no, category_no),
                                INDEX idx_slug (slug)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_comments' => "
                            CREATE TABLE IF NOT EXISTS board_comments (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL DEFAULT 0,
                                article_no INT NOT NULL DEFAULT 0,
                                parent_no INT NOT NULL DEFAULT 0,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                nickName VARCHAR(50) NOT NULL DEFAULT '',
                                password VARCHAR(255) NOT NULL DEFAULT '',
                                content TEXT NOT NULL,
                                like_count INT NOT NULL DEFAULT 0,
                                dislike_count INT NOT NULL DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                path VARCHAR(255) NOT NULL,
                                INDEX idx_article_no (board_no, article_no),
                                INDEX idx_mb_id (mb_id),
                                INDEX idx_path (path)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_attachments' => "
                            CREATE TABLE IF NOT EXISTS board_attachments (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL DEFAULT 0,
                                article_no INT NOT NULL,
                                filename VARCHAR(255) NOT NULL DEFAULT '',
                                filesize INT NOT NULL DEFAULT 0,
                                filepath VARCHAR(255) NOT NULL DEFAULT '',
                                fileurl VARCHAR(255) NOT NULL DEFAULT '',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX idx_article_no (board_no, article_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_links' => "
                            CREATE TABLE IF NOT EXISTS board_attachments (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL DEFAULT 0,
                                article_no INT NOT NULL,
                                link VARCHAR(255) NOT NULL DEFAULT '',
                                hit INT NOT NULL DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX idx_article_no (board_no, article_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'board_articles_reactions' => "
                            CREATE TABLE IF NOT EXISTS board_reactions (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL DEFAULT 0,
                                article_no INT NOT NULL DEFAULT 0,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                reaction_type VARCHAR(12) NOT NULL DEFAULT '',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_article_user_reaction (article_no, mb_id),
                                INDEX idx_article_no (article_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'board_comments_reactions' => "
                            CREATE TABLE IF NOT EXISTS board_reactions (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                board_no INT NOT NULL DEFAULT 0,
                                article_no INT NOT NULL DEFAULT 0,
                                comment_no INT NOT DEFAULT 0,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                reaction_type VARCHAR(12) NOT NULL DEFAULT '',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_comment_user_reaction (comment_no, mb_id),
                                INDEX idx_comment_no (board_no, article_no, comment_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                            'board_groups' => [
                                'data' => [
                                    ['group_id' => 'manage', 'group_name' => '운영 게시판', 'order_num' => 1],
                                    ['group_id' => 'community', 'group_name' => '커뮤니티 게시판', 'order_num' => 2],
                                ],
                                'encrypt' => []
                            ],
                            'board_configs' => [
                                'data' => [
                                    [
                                        'group_no' => 1,
                                        'board_name' => '공지사항',
                                        'board_id' => 'notice',
                                        'read_level' => 0,
                                        'write_level' => 9,
                                        'download_level' => 0,
                                    ],
                                    [
                                        'group_no' => 2,
                                        'board_name' => '자유게시판',
                                        'board_id' => 'free',
                                        'read_level' => 0,
                                        'write_level' => 1,
                                        'download_level' => 1,
                                    ],
                                ],
                                'encrypt' => []
                            ],
                        ],
                        ],
];

return $schema;