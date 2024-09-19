<?php
// /src/Helper/ImageHelper.php

namespace Web\PublicHtml\Helper;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    protected static $imageDirectory;
    protected static $imageManager;

    /**
     * 이미지 헬퍼 초기화
     * 
     * @param string $subDirectory 저장할 하위 디렉토리
     */
    public static function initialize($subDirectory = '')
    {
        // 동적으로 디렉토리 설정
        self::$imageDirectory = __DIR__ . '/../../public/storage/' . $subDirectory;
        
        // 디렉토리가 없으면 생성
        if (!is_dir(self::$imageDirectory)) {
            if (!mkdir(self::$imageDirectory, 0777, true)) {
                die('Failed to create image directories...');
            }
        }

        // ImageManager 초기화
        self::$imageManager = new ImageManager(new Driver());
    }

    /**
     * 이미지 리사이즈
     * 
     * @param string $inputPath 원본 이미지 경로
     * @param string $outputFilename 저장할 파일 이름
     * @param int $width 리사이즈할 너비
     * @param int $height 리사이즈할 높이
     * @return bool
     */
    public static function resizeImage(string $inputPath, string $outputFilename, int $width, int $height): bool
    {
        try {
            $outputPath = self::$imageDirectory . '/' . $outputFilename;
            $image = self::$imageManager->read($inputPath);
            $image->resize($width, $height);
            $image->save($outputPath);
            return true;
        } catch (\Exception $e) {
            // 오류 처리
            return false;
        }
    }

    /**
     * 이미지 썸네일 생성
     * 
     * @param string $inputPath 원본 이미지 경로
     * @param string $outputFilename 저장할 썸네일 파일 이름
     * @param int $width 썸네일 너비
     * @param int $height 썸네일 높이
     * @return bool
     */
    public static function createThumbnail(string $inputPath, string $outputFilename, int $width, int $height): bool
    {
        return self::resizeImage($inputPath, $outputFilename, $width, $height);
    }

    /**
     * 이미지에 워터마크 추가
     * 
     * @param string $inputPath 원본 이미지 경로
     * @param string $watermarkPath 워터마크 이미지 경로
     * @param string $outputFilename 저장할 파일 이름
     * @return bool
     */
    public static function addWatermark(string $inputPath, string $watermarkPath, string $outputFilename): bool
    {
        try {
            $outputPath = self::$imageDirectory . '/' . $outputFilename;
            $image = self::$imageManager->read($inputPath);
            $watermark = self::$imageManager->read($watermarkPath);
            $image->place($watermark, 'bottom-right', 10, 10);
            $image->save($outputPath);
            return true;
        } catch (\Exception $e) {
            // 오류 처리
            return false;
        }
    }

    /**
     * WebP 포맷으로 변환
     * 
     * @param string $inputPath 원본 이미지 경로
     * @param string $outputFilename 저장할 WebP 파일 이름
     * @param int $quality 압축 품질 (0-100)
     * @return bool
     */
    public static function convertToWebp(string $inputPath, string $outputFilename, int $quality = 90): bool
    {
        try {
            $outputPath = self::$imageDirectory . '/' . $outputFilename;
            $image = self::$imageManager->read($inputPath);
            $image->toWebp($quality)->save($outputPath);
            return true;
        } catch (\Exception $e) {
            // 오류 처리
            return false;
        }
    }
}