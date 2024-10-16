<?php
// 파일 위치: /src/PublicHtml/Helper/FileUploadManager.php
namespace Web\PublicHtml\Helper;

use InvalidArgumentException;
use RuntimeException;

class FileUploadManager
{
    private $tempFilePath;
    private $filePermission;
    private $allowedExtensions;
    private $directoryPermission;

    public function __construct($tempFilePath, $filePermission = 0644, $allowedExtensions = ['gif', 'png', 'jpg', 'jpeg', 'bmp', 'webp'], $directoryPermission = 0755)
    {
        $this->tempFilePath = rtrim($tempFilePath, '/');
        $this->filePermission = $filePermission;
        $this->allowedExtensions = $allowedExtensions;
        $this->directoryPermission = $directoryPermission;
        $this->ensureDirectoryExists();
    }

    private function ensureDirectoryExists()
    {
        if (!file_exists($this->tempFilePath)) {
            if (!mkdir($this->tempFilePath, $this->directoryPermission, true)) {
                throw new RuntimeException("Failed to create directory: {$this->tempFilePath}");
            }
        } elseif (!is_writable($this->tempFilePath)) {
            throw new RuntimeException("Directory is not writable: {$this->tempFilePath}");
        }
    }

    public function handleFileUploads($files, $oldFiles, $position, $deleteFlags = [])
    {
        $this->ensureDirectoryExists();
        $result = [];

        // 파일 배열 재구성
        $files = $this->arrayFiles($files);

        foreach ($files as $key => $file) {
            $oldFile = $oldFiles[$key] ?? '';
            $deleteFlag = $deleteFlags[$key] ?? false;

            if ($this->isValidUploadedFile($file)) {
                $newFileName = $this->uploadFile($file, $position . '_' . $key);
                if ($newFileName) {
                    $result[$key] = $newFileName;
                    $this->deleteOldFile($oldFile);
                } else {
                    $result[$key] = $oldFile;
                }
            } else {
                if ($deleteFlag) {
                    $this->deleteOldFile($oldFile);
                    $result[$key] = '';
                } else {
                    $result[$key] = $oldFile;
                }
            }
        }
        return $result;
    }

    public function arrayFiles($filePost)
    {
        $fileArray = [];

        if (is_array($filePost)) {
            foreach ($filePost as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        if (is_array($subValue)) {
                            foreach ($subValue as $finalKey => $finalValue) {
                                $fileArray[$finalKey][$subKey] = $finalValue;
                            }
                        } else {
                            $fileArray[$key][$subKey] = $subValue;
                        }
                    }
                } else {
                    $fileArray[$key] = $value;
                }
            }
        } else {
            $fileArray = $filePost;
        }

        return $fileArray;
    }

    private function isValidUploadedFile($file)
    {
        if (!isset($file['tmp_name']) || !$file['tmp_name'] || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return false;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        return true;
    }

    private function uploadFile($file, $position)
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 강화된 유일한 파일 이름 생성
        $filename = $this->generateUniqueFileName($position, $extension);
        
        $destpath = $this->tempFilePath . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destpath)) {
            chmod($destpath, $this->filePermission);
            return $filename;
        } else {
            error_log("Failed to move uploaded file from {$file['tmp_name']} to {$destpath}");
            return false;
        }
    }

    private function generateUniqueFileName($position, $extension)
    {
        $randomString = bin2hex(random_bytes(8));
        $timestamp = time();
        $hashedName = md5($randomString . $timestamp . $position);
        return $position . '_' . substr($hashedName, 0, 16) . '.' . $extension;
    }

    public function deleteOldFile($oldFile)
    {
        if (!$oldFile) return;

        $oldFilePath = $this->tempFilePath . '/' . $oldFile;
        if (file_exists($oldFilePath)) {
            @unlink($oldFilePath);
        }

        // 썸네일 삭제
        $fn = preg_replace("/\.[^\.]+$/i", "", basename($oldFile));
        $thumbs = glob($this->tempFilePath . '/thumb-' . $fn . '*');
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