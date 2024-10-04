<?php
// 파일 위치: /src/Traits/BaseTemplateServiceTrait.php

namespace Web\PublicHtml\Traits;

use InvalidArgumentException;

trait BaseTemplateServiceTrait
{
    abstract protected function getContainer();

    // Template Skin Directory Management methods
    public function getTemplateSkinDir(string $itemType): array
    {
        try {
            $template_path = $this->getTemplatePath($itemType);
        } catch (InvalidArgumentException $e) {
            error_log($e->getMessage());
            return [];
        }

        $result = [];
        if (!is_dir($template_path)) {
            error_log("Template directory does not exist: $template_path");
            return $result;
        }

        return $itemType === 'file' ? $this->getTemplateFiles($template_path) : $this->getTemplateDirs($template_path);
    }

    protected function getTemplatePath(string $itemType): string
    {
        $templateConfig = $this->getContainer()->get('ConfigProvider')->get('template');
        $allowedTypes = array_keys($templateConfig['template_items'] ?? []);
        if (!in_array($itemType, $allowedTypes)) {
            throw new InvalidArgumentException("Invalid item type: $itemType");
        }
        return WZ_SRC_PATH . '/View/Templates/' . $itemType;
    }

    protected function getTemplateDirs(string $path): array
    {
        $sub_dirs = $this->getSubDirectories($path);
        $result = [];
        foreach ($sub_dirs as $dir) {
            $result[] = [
                'name' => $dir,
                'desc' => $this->getSkinDescription($path . '/' . $dir)
            ];
        }
        return $result;
    }

    protected function getTemplateFiles(string $path): array
    {
        $files = array_filter(scandir($path), function($file) use ($path) {
            return !is_dir($path . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });

        $result = [];
        foreach ($files as $file) {
            $result[] = [
                'name' => $file,
                'desc' => $this->getFileDescription($path . '/' . $file)
            ];
        }
        return $result;
    }

    protected function getSubDirectories(string $path): array
    {
        return array_filter(scandir($path), function($dir) use ($path) {
            return $dir !== '.' && $dir !== '..' && is_dir($path . '/' . $dir);
        });
    }

    protected function getSkinDescription(string $path): string
    {
        $desc_file = $path . '/description.txt';
        if (file_exists($desc_file) && is_readable($desc_file)) {
            return file_get_contents($desc_file);
        }
        return '설명 없음';
    }

    protected function getFileDescription(string $filePath): string
    {
        // 파일의 첫 몇 줄을 읽어 설명을 추출하는 로직을 구현할 수 있습니다.
        // 예를 들어, 파일 상단의 주석을 파싱하는 등의 방법을 사용할 수 있습니다.
        // 여기서는 간단히 파일 이름을 반환합니다.
        return basename($filePath);
    }
}