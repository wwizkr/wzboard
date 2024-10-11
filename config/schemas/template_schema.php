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