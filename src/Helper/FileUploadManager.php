<?php
// 파일 위치: /src/PublicHtml/Helper/FileUploadManager.php
namespace Web\PublicHtml\Helper;

use InvalidArgumentException;
use RuntimeException;

class FileUploadManager
{
    private $filePermission;
    private $allowedExtensions;
    private $directoryPermission;
    private $disAllowedExtensions;

    public function __construct($filePermission = 0644, $allowedExtensions = ['gif', 'png', 'jpg', 'jpeg', 'bmp', 'webp'], $directoryPermission = 0755)
    {
        $this->filePermission = $filePermission;
        $this->allowedExtensions = $allowedExtensions;
        $this->directoryPermission = $directoryPermission;
    }

    private function ensureDirectoryExists($tempFilePath)
    {
        if (empty($tempFilePath)) {
            throw new RuntimeException("Temporary file path must be specified.");
        }

        if (!file_exists($tempFilePath)) {
            if (!mkdir($tempFilePath, $this->directoryPermission, true)) {
                throw new RuntimeException("Failed to create directory: {$tempFilePath}");
            }
        } elseif (!is_writable($tempFilePath)) {
            throw new RuntimeException("Directory is not writable: {$tempFilePath}");
        }
    }

    public function setDisAllowedExtensions(array $extensions)
    {
        $this->disAllowedExtensions = $extensions;
    }

    public function handleFileUploads($uploadPath, $files, $prefix)
    {
        $this->ensureDirectoryExists($uploadPath);
        $result = [];

        foreach ($files as $key => $file) {
            if ($this->isValidUploadedFile($file)) {
                $newFileName = $this->uploadFile($file, $prefix . '_' . $key, $uploadPath);
                if ($newFileName) {
                    $result[$key] = $newFileName;
                }
            }
        }
        return $result;
    }

    public function arrayFiles($filePost)
    {
        $fileArray = [];
        
        // 입력이 배열이 아닌 경우 처리
        if (!is_array($filePost)) {
            error_log("Invalid input: not an array");
            return $fileArray;
        }

        // name이 배열인 경우 (다중 파일 업로드)
        if (isset($filePost['name']) && is_array($filePost['name'])) {
            // 파일 개수만큼 반복
            foreach ($filePost['name'] as $key => $name) {
                // 빈 파일 건너뛰기
                if (empty($name)) {
                    continue;
                }

                $fileArray[$key] = [
                    'name' => $filePost['name'][$key],
                    'full_path' => $filePost['full_path'][$key] ?? '',
                    'type' => $filePost['type'][$key],
                    'tmp_name' => $filePost['tmp_name'][$key],
                    'error' => $filePost['error'][$key],
                    'size' => $filePost['size'][$key]
                ];
            }
        }
        // 단일 파일인 경우
        elseif (isset($filePost['name']) && !is_array($filePost['name'])) {
            $fileArray[$key] = [
                'name' => $filePost['name'],
                'full_path' => $filePost['full_path'] ?? '',
                'type' => $filePost['type'],
                'tmp_name' => $filePost['tmp_name'],
                'error' => $filePost['error'],
                'size' => $filePost['size']
            ];
        }

        return $fileArray;
    }

    private function isValidUploadedFile($file)
    {
        if (!isset($file['tmp_name']) || !$file['tmp_name'] || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // 금지된 확장자 검사 (disAllowedExtensions가 설정된 경우에만)
        if (!empty($this->disAllowedExtensions) && in_array($extension, $this->disAllowedExtensions)) {
            return false;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        return true;
    }

    private function uploadFile($file, $prefix, $tempFilePath)
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $this->generateUniqueFileName($prefix, $extension);
        $destpath = $tempFilePath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destpath)) {
            chmod($destpath, $this->filePermission);
            return $filename;
        } else {
            error_log("Failed to move uploaded file from {$file['tmp_name']} to {$destpath}");
            return false;
        }
    }

    private function generateUniqueFileName($prefix, $extension)
    {
        $randomString = bin2hex(random_bytes(8));
        $timestamp = time();
        $hashedName = md5($randomString . $timestamp . $prefix);
        return $prefix . '_' . substr($hashedName, 0, 16) . '.' . $extension;
    }

    public function deleteOldFile($oldFile, $oldFilePath)
    {
        if (!$oldFile) return;

        $oldFilePath = $oldFilePath . '/' . $oldFile;
        if (file_exists($oldFilePath)) {
            @unlink($oldFilePath);
        }

        // 썸네일 삭제
        $fn = preg_replace("/\.[^\.]+$/i", "", basename($oldFile));
        $thumbs = glob($oldFilePath . '/thumb-' . $fn . '*');
        if (is_array($thumbs)) {
            foreach ($thumbs as $thumb) {
                if (file_exists($thumb)) {
                    @unlink($thumb);
                }
            }
        }
    }

    public function setAllowedExtensions(array $extensions)
    {
        $this->allowedExtensions = $extensions;
    }
}