<?php
// src/Admin/Helper/AdminBoardsHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminBoardsHelper
{
    protected $container;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
    }

    public static function getBoardSkinDir()
    {
        $boards_skin_path = dirname(__DIR__, 2) . '/View/Board';
        $result = [];

        if (is_dir($boards_skin_path)) {
            $sub_dirs = array_filter(scandir($boards_skin_path), function($dir) use ($boards_skin_path) {
                return $dir !== '.' && $dir !== '..' && is_dir($boards_skin_path . '/' . $dir);
            });

            foreach ($sub_dirs as $dir) {
                $result[] = [
                    'name' => $dir,
                    'desc' => self::getSkinDescription($boards_skin_path . '/' . $dir)
                ];
            }
        }

        return $result;
    }

    private static function getSkinDescription($path)
    {
        $desc_file = $path . '/description.txt';
        if (file_exists($desc_file)) {
            return file_get_contents($desc_file);
        }
        return '설명 없음';
    }

    public function getLevelSelectBox($dataType = 'formData')
    {
        $membersService = $this->container->get('MembersService');
        $level = $membersService->getMemberLevelData();
        $levelData = $membersService->formatLevelDataArray($level);
        $levelSelect = [
            'list_level' => CommonHelper::makeSelectBox(
                $dataType.'[list_level]',
                $levelData ,
                '',
                'list_level',
                'frm_input frm_full',
                '비회원'
            ),
            'read_level' => CommonHelper::makeSelectBox(
                $dataType.'[read_level]',
                $levelData ,
                '',
                'read_level',
                'frm_input frm_full',
                '비회원'
            ),
            'write_level' => CommonHelper::makeSelectBox(
                $dataType.'[write_level]',
                $levelData ,
                '',
                'write_level',
                'frm_input frm_full',
                '비회원'
            ),
            'comment_level' => CommonHelper::makeSelectBox(
                $dataType.'[comment_level]',
                $levelData ,
                '',
                'comment_level',
                'frm_input frm_full',
                '비회원'
            ),
            'download_level' => CommonHelper::makeSelectBox(
                $dataType.'[download_level]',
                $levelData ,
                '',
                'download_level',
                'frm_input frm_full',
                '비회원'
            ),
        ];

        return $levelSelect;
    }
}