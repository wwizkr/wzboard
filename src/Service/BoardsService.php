<?php
// 파일 위치: src/Service/BoardsService.php

namespace Web\PublicHtml\Service;

use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;

class BoardsService
{
    protected $boardsModel;
    protected $adminBoardsService;
    protected $boardsHelper;
    protected $membersHelper;

    /**
     * 생성자: BoardsService 인스턴스를 생성합니다.
     *
     * @param BoardsModel $boardsModel 게시판 모델 인스턴스
     * @param AdminBoardsService $adminBoardsService 관리자 게시판 서비스 인스턴스
     * @param BoardsHelper $boardsHelper 게시판 관련 헬퍼 인스턴스
     * @param MembersHelper $membersHelper 회원 관련 헬퍼 인스턴스
     */
    public function __construct(
        BoardsModel $boardsModel, 
        AdminBoardsService $adminBoardsService, 
        BoardsHelper $boardsHelper, 
        MembersHelper $membersHelper
    ) {
        $this->boardsModel = $boardsModel;
        $this->adminBoardsService = $adminBoardsService;
        $this->boardsHelper = $boardsHelper;
        $this->membersHelper = $membersHelper;
    }

    public function getArticleListData($board_no, $currentPage, $page_rows, $searchQuery, $filters, $sort)
    {
        return $this->boardsModel->getArticleListData($board_no, $currentPage, $page_rows, $searchQuery, $filters, $sort);
    }

    public function getTotalArticleCount($board_no, $searchQuery, $filters)
    {
        return $this->boardsModel->getTotalArticleCount($board_no, $searchQuery, $filters);
    }
    
    /**
     * 게시판 글을 업데이트합니다.
     *
     * @param string $boardId 게시판 ID
     * @param array $data 게시글 데이터
     * @return mixed 업데이트 결과
     */
    public function writeBoardsUpdate($boardId, $data)
    {
        // BoardsHelper 및 MembersHelper 메서드 사용 예시
        $groupData = $this->boardsHelper->getGroupData();
        $memberLevels = $this->membersHelper->getLevelData();

        // 오늘 날짜 형식 설정
        $dateFolder = date('Ymd'); // 예: 20240828
        $storagePath = "/storage/board/$boardId/$dateFolder";

        // 폴더가 존재하지 않으면 생성
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $storagePath)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . $storagePath, 0777, true);
        }

        // 콘텐츠 처리 로직
        $content = $data['content'][1];

        // 정규식을 사용하여 모든 /tmp/ 경로의 파일들을 찾기
        preg_match_all('/\/storage\/tmp\/[^\s"\']+/', $content, $matches);

        // 찾은 파일들을 새로운 경로로 복사하고 경로를 업데이트
        if (isset($matches[0]) && count($matches[0]) > 0) {
            foreach ($matches[0] as $filePath) {
                $fileName = basename($filePath);
                $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . $storagePath . '/' . $fileName;

                // 로그: 파일 경로 정보 출력
                error_log("Processing file: $filePath");
                error_log("Source path: $sourcePath");
                error_log("Destination path: $destinationPath");

                // 파일이 존재하면 복사 후 경로 변경
                if (file_exists($sourcePath)) {
                    if (copy($sourcePath, $destinationPath)) {
                        error_log("File copied successfully from $sourcePath to $destinationPath");
                        
                        // 복사 성공 시 콘텐츠 내 경로 변경
                        $newFilePath = $storagePath . '/' . $fileName;
                        $contentBeforeReplace = $content; // 변경 전 콘텐츠 백업
                        $content = str_replace($filePath, $newFilePath, $content);

                        // 로그: 콘텐츠 경로 변경 후 로그
                        if ($content !== $contentBeforeReplace) {
                            error_log("Content path replaced: $filePath -> $newFilePath");
                        } else {
                            error_log("Content path replacement failed for: $filePath");
                        }

                        // 원본 파일 삭제
                        if (!unlink($sourcePath)) {
                            error_log("Failed to delete source file: $sourcePath");
                        }
                    } else {
                        error_log("Failed to copy file from $sourcePath to $destinationPath");
                    }
                } else {
                    error_log("Source file does not exist: $sourcePath");
                }
            }
        }

        // 변경된 콘텐츠로 데이터 업데이트
        $data['content'][1] = $content;

        // 실제 게시판 업데이트
        $updateResult = $this->boardsModel->writeBoardsUpdate($boardId, $data);

        if ($updateResult) {
            return [
                'result' => 'success',
                'message' => '게시글이 성공적으로 업데이트되었습니다.'
            ];
        } else {
            return [
                'result' => 'failure',
                'message' => '게시글 업데이트 중 오류가 발생했습니다.'
            ];
        }
    }
}