<?php
// 파일 위치: /Plugins/adversting/adversting_schema.php

$schema = [
    'schema_group' => '광고상품 관련 테이블',
    'schema_array' => [
                        'adversting_company' => '광고 상품 회사',
                        'adversting_items' => '광고 상품 테이블',
                        'adversting_items_history' => '광고 상품 등록/연장 히스토리',
                      ],
    'schema_content' => [
                        'adversting_company' => "
                            CREATE TABLE IF NOT EXISTS adversting_partners (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                companyName VARCHAR(25) NOT NULL DEFAULT '',
                                siteUrl VARCHAR(255) NOT NULL DEFAULT '',
                                programType VARCHAR(25) NOT NULL DEFAULT '',
                                programItems VARCHAR(255) NOT NULL DEFAULT '',
                                supplyPrice INT NOT NULL DEFAULT 0,
                                marketPrice INT NOT NULL DEFAULT 0,
                                operateUnit INT NOT NULL DEFAULT 10,
                                flowCount VARCHAR(25) DEFAULT NULL,
                                clickCountCheck ENUM('Y','N') NOT NULL DEFAULT 'Y',
                                existsUi ENUM('Y','N') NOT NULL DEFAULT 'N',
                                closeTime VARCHAR(25) NOT NULL DEFAULT '',
                                startTime VARCHAR(25) NOT NULL DEFAULT '',
                                settingItems TEXT,
                                flowAdvice VARCHAR(12) NOT NULL DEFAULT '모바일',
                                keywordType VARCHAR(50) DEFAULT NULL,
                                status TINYINT NOT NULL DEFAULT 0,
                                memo TEXT,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_status (status),
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'adversting_items' => "
                            CREATE TABLE IF NOT EXISTS adversting_items (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_class VARCHAR(25) NOT NULL DEFAULT '',
                                programNo INT NOT NULL DEFAULT 0,
                                programType VARCHAR(25) NOT NULL DEFAULT '',
                                programItem VARCHAR(25) NOT NULL DEFAULT '',
                                storeType VARCHAR(25) NOT NULL DEFAULT '',
                                storeKind VARCHAR(12) NOT NULL DEFAULT '',
                                managerId VARCHAR(25) NOT NULL DEFAULT '',
                                managerLevel TINYINT UNSIGNED NOT NULL DEFAULT 0,
                                sellerId VARCHAR(25) NOT NULL DEFAULT '',
                                storeName VARCHAR(25) NOT NULL DEFAULT '',
                                itemUrl VARCHAR(100) NOT NULL DEFAULT '',
                                itemName VARCHAR(125) NOT NULL DEFAULT '',
                                searchKeyword VARCHAR(55) NOT NULL DEFAULT '',
                                oQuery VARCHAR(55) NOT NULL DEFAULT '',
                                adQuery VARCHAR(55) NOT NULL DEFAULT '',
                                itemCode VARCHAR(25) NOT NULL DEFAULT '',
                                matchCode VARCHAR(25) NOT NULL DEFAULT '',
                                startRanking INT NOT NULL DEFAULT 0,
                                updateRanking INT NOT NULL DEFAULT 0,
                                rankingHistory VARCHAR(255) NOT NULL DEFAULT '',
                                slotCount INT NOT NULL DEFAULT 0,
                                slotPeriod INT NOT NULL DEFAULT 0,
                                flowCount INT NOT NULL DEFAULT 0,
                                status TINYINT NOT NULL DEFAULT 1,
                                start_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                extension_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                close_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                periodHistory TEXT,
                                staffId VARCHAR(25) NOT NULL DEFAULT '',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_cf_class (cf_class),
                                INDEX idx_program(programNo),
                                INDEX idx_status(status)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'adversting_items_history' => "
                            CREATE TABLE IF NOT EXISTS adversting_logs (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_class VARCHAR(25) NOT NULL DEFAULT '',
                                managerId VARCHAR(25) NOT NULL DEFAULT '',
                                sellerId VARCHAR(25) NOT NULL DEFAULT '',
                                programNo INT NOT NULL DEFAULT 0,
                                itemNo INT NOT NULL DEFAULT 0,
                                period INT NOT NULL DEFAULT 0,
                                cost INT NOT NULL DEFAULT 0,
                                orderType TINYINT NOT NULL DEFAULT 1,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX idx_cf_class (cf_class),
                                INDEX idx_program (programNo),
                                INDEX idx_item (itemNo)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'adversting_items_ranking_history' => "
                            CREATE TABLE IF NOT EXISTS adversting_logs (
                                no INT AUTO_INCREMENT PRIMARY KEY,
                                cf_class VARCHAR(25) NOT NULL DEFAULT '',
                                programNo INT NOT NULL DEFAULT 0,
                                itemNo INT NOT NULL DEFAULT 0,
                                managerId VARCHAR(25) NOT NULL DEFAULT '',
                                sellerId VARCHAR(25) NOT NULL DEFAULT '',
                                ranking INT NOT NULL DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX idx_cf_class (cf_class),
                                INDEX idx_item (itemNo)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",

                        'initial_data' => [
                            
                        ],
                        ],
];

return $schema;