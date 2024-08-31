<?php
// 파일 위치: /config/schemas/trial_schema.php

$schema = [
    'schema_group' => '문제은행 관련 테이블',
    'schema_array' => [
                        'trial_subject' => '과목 테이블',
                        'trial_category' => '과목별 카테고리 테이블',
                        'trial_question' => '문제 테이블',
                        'trial_jimun' => '문제 지문 테이블',
                        'trial_daily_study' => '일일학습 문제테이블',
                        'trial_daily_statistics' => '일일 통계 테이블',
                        'trial_weekly_statistics' => '주간 통계 테이블',
                        'trial_monthly_statistics' => '월별 통계 테이블',
                      ],
    'schema_content' => [
                        'trial_subject' => "
                            CREATE TABLE IF NOT EXISTS trial_subject (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                subject_name VARCHAR(25) NOT NULL DEFAULT '',
                                subjct_total INT DEFAULT 0,
                                trial_cnt INT DEFAULT 0,
                                order_num INT DEFAULT 0
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'trial_category' => "
                            CREATE TABLE IF NOT EXISTS trial_category (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                category varchar(21) NOT NULL DEFAULT '',
                                subject_no INT NOT NULL DEFAULT 0,
                                category_name VARCHAR(50) NOT NULL DEFAULT '',
                                category_parent INT(11) NOT NULL DEFAULT 0,
                                category_depth TINYINT(4) NOT NULL DEFAULT 1,
                                order_num INT DEFAULT 0,
                                subjct_total INT DEFAULT 0,
                                trial_cnt INT DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_category (category),
                                INDEX idx_subject_no (subject_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        
                        'trial_question' => "
                            CREATE TABLE IF NOT EXISTS trial_question (
                                no INT AUTO_INCREMENT PRIMARY KEY COMMENT '고유 번호',
                                subject_no INT NOT NULL DEFAULT 0 COMMENT '과목 번호',
                                category_no INT NOT NULL DEFAULT 0 COMMENT '카테고리 번호',
                                question_type TINYINT DEFAULT 0 COMMENT '문제 유형',
                                gichul_times TINYINT UNSIGNED DEFAULT 0 COMMENT '기출 회차',
                                gichul_number SMALLINT UNSIGNED DEFAULT 0 COMMENT '기출 번호',
                                question_text VARCHAR(255) NOT NULL DEFAULT '' COMMENT '문제 텍스트',
                                question_jimun TEXT COMMENT '문제 지문',
                                question_explain TEXT COMMENT '문제 설명',
                                total_answer INT DEFAULT 0 COMMENT '총 답변 수',
                                correct_answer INT DEFAULT 0 COMMENT '정답 수',
                                latest_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '최근 풀이시간',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_subject_no (subject_no, category_no),
                                INDEX idx_question (question_type)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ",

                        'trial_jimun' => "
                            CREATE TABLE IF NOT EXISTS trial_jimun (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                question_no INT NOT NULL DEFAULT 0,
                                jimun_text text,
                                correct_type TINYINT DEFAULT 0,
                                choice_count INT DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_question_no (question_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'trial_daily_study' => "
                            CREATE TABLE IF NOT EXISTS trial_daily_study (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                mb_id VARCHAR(50) NOT NULL DEFAULT '',
                                subject_no INT NOT NULL DEFAULT 0,
                                category_no INT NOT NULL DEFAULT 0,
                                question_no INT NOT NULL DEFAULT 0,
                                study_status TINYINT DEFAULT 0,
                                correct_type TINYINT DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx1 (mb_id),
                                INDEX idx2 (category_no, question_no)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                            
                        ],
                        ],
];

return $schema;