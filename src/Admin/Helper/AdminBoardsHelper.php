<?php
// src/Admin/Helper/AdminBoardsHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;

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
}