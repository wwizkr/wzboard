<?php
// 파일 위치: /config/schemas/user_schema.php

$schema = [
    'schema_group' => '회원 관련 테이블',
    'schema_array' => [
                        'members' => '회원 테이블',
                        'members_level' => '회원 레벨 설정 테이블',
                        'initial_data' => '설치 시 게시판별 기본 입력값'
                      ],
    'schema_content' => [
                        'members' => "
                            CREATE TABLE IF NOT EXISTS members (
                                mb_no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                nickName VARCHAR(50) NOT NULL DEFAULT '',
                                password VARCHAR(255) NOT NULL DEFAULT '',
                                phone VARCHAR(20) NOT NULL DEFAULT '',
                                email VARCHAR(100) NOT NULL DEFAULT '',
                                birth VARCHAR(8) DEFAULT NULL,
                                gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
                                age INT NOT NULL DEFAULT 0,
                                profile_picture VARCHAR(255) DEFAULT NULL,
                                greeting TEXT,
                                point INT NOT NULL DEFAULT 0,
                                cash INT NOT NULL DEFAULT 0,
                                login_count INT NOT NULL DEFAULT 0,
                                loc_lat DECIMAL(10, 8) DEFAULT NULL,
                                loc_lng DECIMAL(11, 8) DEFAULT NULL,
                                user_ip VARCHAR(25) DEFAULT NULL,
                                signup_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                                last_login DATETIME DEFAULT CURRENT_TIMESTAMP,
                                location_update_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                                fcm_token VARCHAR(255) DEFAULT NULL,
                                agree_content TEXT,
                                agree_alarm ENUM('Y','N') NOT NULL DEFAULT 'Y',
                                alarm_type VARCHAR(25) DEFAULT NULL,
                                member_level TINYINT UNSIGNED NOT NULL DEFAULT 1,
                                UNIQUE KEY idx_user_id (mb_id),
                                UNIQUE KEY idx_email (email),
                                KEY idx_location (loc_lat, loc_lng),
                                KEY idx_last_login (last_login),
                                KEY idx_fcm_token (fcm_token),
                                KEY idx_member_level (member_level)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'members_level' => "
                            CREATE TABLE IF NOT EXISTS members_level (
                                level_id TINYINT UNSIGNED PRIMARY KEY,
                                cf_id INT UNSIGNED NOT NULL DEFAULT 1,
                                level_name VARCHAR(50) NOT NULL DEFAULT '',
                                min_point INT UNSIGNED NOT NULL DEFAULT 0,
                                min_posts INT UNSIGNED NOT NULL DEFAULT 0,
                                min_comments INT UNSIGNED NOT NULL DEFAULT 0,
                                min_days_since_join INT UNSIGNED NOT NULL DEFAULT 0,
                                min_purchase_amount DECIMAL(10, 2) UNSIGNED NOT NULL DEFAULT 0,
                                auto_level_up TINYINT NOT NULL DEFAULT 1,
                                is_admin TINYINT NOT NULL DEFAULT 0,
                                is_super TINYINT NOT NULL DEFAULT 0,
                                description TEXT,
                                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                            'members' => [
                                'data' => [
                                    [
                                        'cf_id' => 1,
                                        'mb_id' => 'admin',
                                        'email' => 'admin@example.com',
                                        'nickName' => '최고관리자',
                                        'password' => '1234',
                                        'gender' => 'other',
                                        'member_level' => 10,
                                    ],
                                ],
                                'encrypt' => [
                                    'password' => 'password',
                                ]
                            ],
                            'members_level' => [
                                'data' => [
                                    [ 'level_id' => 1, 'level_name' => '신규 회원', 'min_point' => 0, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '새로 가입한 회원' ],
                                    [ 'level_id' => 2, 'level_name' => '일반 회원', 'min_point' => 100, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '활동을 시작한 회원' ],
                                    [ 'level_id' => 3, 'level_name' => '성실 회원', 'min_point' => 500, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '꾸준히 활동하는 회원' ],
                                    [ 'level_id' => 4, 'level_name' => '우수 회원', 'min_point' => 1000, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '활발히 활동하는 회원' ],
                                    [ 'level_id' => 5, 'level_name' => '열심 회원', 'min_point' => 2000, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '매우 활발히 활동하는 회원' ],
                                    [ 'level_id' => 6, 'level_name' => 'VIP 회원', 'min_point' => 5000, 'auto_level_up' => true, 'is_admin' => false, 'is_super_admin' => false, 'description' => '사이트의 주요 회원' ],
                                    [ 'level_id' => 7, 'level_name' => 'VVIP 회원','min_point' => 10000, 'auto_level_up' => false, 'is_admin' => false, 'is_super_admin' => false, 'description' => '사이트의 핵심 회원' ],
                                    [ 'level_id' => 8, 'level_name' => '특별 회원', 'min_point' => 20000, 'auto_level_up' => false, 'is_admin' => false, 'is_super_admin' => false, 'description' => '특별한 공헌을 한 회원' ],
                                    [ 'level_id' => 9, 'level_name' => '일반관리자','min_point' => 50000, 'auto_level_up' => false, 'is_admin' => true, 'is_super_admin' => false, 'description' => '사이트 관리를 보조하는 관리자' ],
                                    [ 'level_id' => 10,'level_name' => '최고관리자','min_point' => 100000, 'auto_level_up' => false, 'is_admin' => true, 'is_super_admin' => true, 'description' => '사이트의 최고 관리자' ]
                                ],
                                'encrypt' => []
                            ],
                        ],
                        ],
];

return $schema;
