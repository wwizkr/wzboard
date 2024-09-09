<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\CommonHelper;

class TemplateController
{
    public function getCommentTemplate($vars)
    {
        header('Content-Type: application/json');

        // 현재 URL 경로가 관리자인지 확인
        //$isAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
        $isAdmin = CommonHelper::isAdminRequest();
        
        $skinName = $_GET['skinName'] ?? 'basic';

        // 현재 파일의 디렉토리를 기준으로 src 디렉토리의 루트 경로를 설정
        $basePath = realpath(__DIR__ . '/../../');

        if ($isAdmin) {
            $templatePath = $basePath . '/src/Admin/View/basic/Board/commentTemplate.html';
        } else {
            $templatePath = $basePath . '/src/View/Board/' . $skinName . '/commentTemplate.html';
        }

        if (file_exists($templatePath)) {
            // 파일 내용을 직접 읽어서 반환
            header('Content-Type: text/html');
            readfile($templatePath); // 파일 내용을 출력
        } else {
            header('Content-Type: application/json');
            echo json_encode(['result' => 'failure', 'message' => '템플릿 파일을 찾을 수 없습니다.']);
        }

        exit(); // 스크립트를 명시적으로 종료하여 추가 출력 방지
    }

    public function getArticleTemplate($vars)
    {
        header('Content-Type: application/json');

        // 현재 URL 경로가 관리자인지 확인
        //$isAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
        $isAdmin = CommonHelper::isAdminRequest();
        $skinName = $_GET['skinName'] ?? 'basic';

        // 현재 파일의 디렉토리를 기준으로 src 디렉토리의 루트 경로를 설정
        $basePath = realpath(__DIR__ . '/../../');

        if ($isAdmin) {
            $templatePath = $basePath . '/src/Admin/View/basic/Board/articleTemplate.html';
        } else {
            $templatePath = $basePath . '/src/View/Board/' . $skinName . '/articleTemplate.html';
        }

        if (file_exists($templatePath)) {
            // 파일 내용을 직접 읽어서 반환
            header('Content-Type: text/html');
            readfile($templatePath); // 파일 내용을 출력
        } else {
            header('Content-Type: application/json');
            echo json_encode(['result' => 'failure', 'message' => '템플릿 파일을 찾을 수 없습니다.']);
        }

        exit(); // 스크립트를 명시적으로 종료하여 추가 출력 방지
    }
}