<?php
namespace Web\PublicHtml\Controller;

class TemplateController
{
    public function getCommentTemplate($vars)
    {
        header('Content-Type: application/json');

        $isAdmin = $_GET['isAdmin'] ?? false;
        $skinName = $_GET['skinName'] ?? 'default';

        // 현재 파일의 디렉토리를 기준으로 src 디렉토리의 루트 경로를 설정
        $basePath = realpath(__DIR__ . '/../../');

        if ($isAdmin === 'true') {
            $templatePath = $basePath . '/src/Admin/View/basic/Board/commentTemplate.html';
        } else {
            $templatePath = $basePath . '/src/View/Boards/' . $skinName . '/commentTemplate.html';
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