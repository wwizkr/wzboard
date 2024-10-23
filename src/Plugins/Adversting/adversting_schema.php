<?php
// 파일 위치: /Plugins/adversting/adversting_schema.php

$schema = [
    'schema_group' => '광고상품 관련 테이블',
    'schema_array' => [
                        'adversting_partners' => '광고 거래처 테이블',
                        'adversting_items' => '광고 상품 테이블',
                        'adversting_logs' => '광고 상품 클릭 테이블',
                      ],
    'schema_content' => [
                        'adversting_partners' => "
                            CREATE TABLE IF NOT EXISTS adversting_partners (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 0,
                                partnerType VARCHAR(25) NOT NULL DEFAULT '',
                                partnerId VARCHAR(50) NOT NULL DEFAULT '',
                                password VARCHAR(255) NOT NULL DEFAULT '',
                                name VARCHAR(50) NOT NULL DEFAULT '',
                                phone VARCHAR(13) NOT NULL DEFAULT '',
                                UNIQUE KEY idx_id (partnerId)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'adversting_items' => "
                            CREATE TABLE IF NOT EXISTS adversting_items (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                advertiserId VARCHAR(100) NOT NULL DEFAULT '',
                                itemCode VARCHAR(12) NOT NULL DEFAULT '',
                                itemType VARCHAR(12) NOT NULL DEFAULT '',
                                mallType VARCHAR(25) NOT NULL DEFAULT '',
                                mallCode VARCHAR(25) NOT NULL DEFAULT '',
                                productCode VARCHAR(16) NOT NULL DEFAULT '',
                                matchCode VARCHAR(16) NOT NULL DEFAULT '',
                                itemName VARCHAR(255) NOT NULL DEFAULT '',
                                originKeyword VARCHAR(25) NOT NULL DEFAULT '',
                                searchKeyword VARCHAR(25) NOT NULL DEFAULT '',
                                adKeyword VARCHAR(25) NOT NULL DEFAULT '',
                                linkType VARCHAR(8) NOT NULL DEFAULT '',
                                linkUrl VARCHAR(255) NOT NULL DEFAULT '',
                                delayDays TINYINT NOT NULL DEFAULT 0,
                                slotCount TINYINT NOT NULL DEFAULT 0,
                                status ENUM('Y','N') NOT NULL DEFAULT 'Y',
                                startDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                                closeDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                                yesterdayHit INT NOT NULL DEFAULT 0,
                                todayHit INT NOT NULL DEFAULT 0,
                                startRanking INT NOT NULL DEFAULT 0,
                                lastestRanking INT NOT NULL DEFAULT 0,
                                year VARCHAR(4) NOT NULL DEFAULT '',
                                month VARCHAR(2) NOT NULL DEFAULT '',
                                number INT NOT NULL DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_itemcode (itemCode),
                                INDEX idx_status(status)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'adversting_logs' => "
                            CREATE TABLE IF NOT EXISTS adversting_logs (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_id INT NOT NULL DEFAULT 0,
                                advertiserId VARCHAR(50) NOT NULL DEFAULT '',
                                affilateId VARCHAR(50) NOT NULL DEFAULT '',
                                memberId VARCHAR(50) NOT NULL DEFAULT '',
                                itemCode VARCHAR(12) NOT NULL DEFAULT '',
                                click_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                UNIQUE KEY idx_itemcode (itemCode, memberId),
                                INDEX idx_click (click_at)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                            
                        ],
                        ],
];

return $schema;