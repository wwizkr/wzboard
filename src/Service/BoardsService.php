<?php
// 파일 위치: src/Service/BoardsService.php

namespace Web\PublicHtml\Service;

use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class BoardsService
{
    protected $boardsModel;
    protected $adminBoardsService;
    protected $boardsHelper;
    protected $membersHelper;
    protected $categoryMapping;
    protected $formDataMiddleware;

    /**
     * 생성자: BoardsService 인스턴스를 생성합니다.
     *
     * @param BoardsModel $boardsModel 게시판 모델 인스턴스
     * @param AdminBoardsService $adminBoardsService 관리자 게시판 서비스 인스턴스
     * @param BoardsHelper $boardsHelper 게시판 관련 헬퍼 인스턴스
     * @param MembersHelper $membersHelper 회원 관련 헬퍼 인스턴스
     * @param int|null $board_no 게시판 번호 (선택적)
     */
    public function __construct(
        BoardsModel $boardsModel, 
        AdminBoardsService $adminBoardsService, 
        BoardsHelper $boardsHelper, 
        MembersHelper $membersHelper,
        FormDataMiddleware $formDataMiddleware
    ) {
        $this->boardsModel = $boardsModel;
        $this->adminBoardsService = $adminBoardsService;
        $this->boardsHelper = $boardsHelper;
        $this->membersHelper = $membersHelper;
        $this->categoryMapping = [];
        $this->formDataMiddleware = $formDataMiddleware;
    }

    private function getCategoryMapping($board_no)
    {
        // 게시판 개별 카테고리 가져오기
        $boardCategory = $this->boardsHelper->getBoardsCategoryMapping($board_no);
        $mapCategory = [];
        foreach($boardCategory as $key=>$val) {
            $mapCategory[$val['category_name']] = $val['no'];
        }

        return $mapCategory;
    }

    public function getTotalArticleCount($board_no, $searchQuery, $filters, $additionalQueries)
    {   
        $this->categoryMapping = $this->getCategoryMapping($board_no);
        $processedQueries = CommonHelper::additionalServiceQueries($additionalQueries, 'category', 'category_no', $this->categoryMapping);
        return $this->boardsModel->getTotalArticleCount($board_no, $searchQuery, $filters, $processedQueries);
    }

    public function getArticleListData($board_no, $currentPage, $page_rows, $searchQuery, $filters, $sort, $additionalQueries)
    {
        $this->categoryMapping = $this->getCategoryMapping($board_no);
        $processedQueries = CommonHelper::additionalServiceQueries($additionalQueries, 'category', 'category_no', $this->categoryMapping);
        return $this->boardsModel->getArticleListData(
            $board_no, 
            $currentPage, 
            $page_rows, 
            $searchQuery, 
            $filters, 
            $sort, 
            $processedQueries
        );
    }
    
    /**
     * 게시판 글을 업데이트합니다.
     *
     * @param string $boardId 게시판 ID
     * @param array $data 게시글 데이터
     * @return mixed 업데이트 결과
     */
    public function writeBoardsUpdate($article_no, $board_id)
    {
        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardsConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }
        
        // $article_no 가 있다면 실제 게시글이 있는 지 확인
        if ($article_no) {
            $articleData = $this->getArticleDataByNo($boardsConfig['group_no'], $article_no);
            if(empty($articleData)) {
                return ['result' => 'failure', 'message' => '게시글 정보를 찾을 수 없습니다. 잘못된 접속입니다.'];
            }
        }

        // 현재 인증된 회원 ID 가져오기 :: HELPER 로 사용예정
        $mb_no = $_SESSION['auth']['mb_no'] ?? null;
        $memberData = $this->membersHelper->getMemberDataByNo($mb_no);

        // POST 데이터는 formData 배열로 전송 됨
        $formData = $_POST['formData'] ?? null;
        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        // 이미지 저장 디렉토리
        $storagePath = "/storage/board/$board_id/";
        
        $content = $formData['content'];
        $content = CommonHelper::updateStorageImages($content, $storagePath);
        
        // formData에 추가
        $formData['group_no'] = $boardsConfig['group_no'];
        $formData['board_no'] = $boardsConfig['no'];
        $formData['nickName'] = $memberData['nickName'] ?? "GUEST";
        $fromData['content'] = $content;

        $numericFields = ['group_no', 'board_no'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        // 실제 게시판 업데이트
        return $this->boardsModel->writeBoardsUpdate($article_no, $board_id, $data);
    }

    /*
     * 게시글 읽기
     *
     * @param int $board_no
     * @param int $article_no
     */
    public function getArticleDataByNo($board_no, $article_no)
    {
        $result = $this->boardsModel->getArticleDataByNo($board_no, $article_no);
        
        // HTML로 변환
        $result['content'] = htmlspecialchars_decode($result['content']);

        $articleData = $result;

        return $articleData;
    }

    /*
     * 게시판 카테고리
     *
     * @param int $board_no
     */
    public function getBoardsCategoryData($board_no)
    {
        $result = $this->boardsModel->getBoardsCategoryData($board_no);

        $categoryData = $result;

        return $categoryData;
    }
    
    // 개별 댓글 -- 삭제 예정
    public function getCommentDataByNo($board_no, $comment_no)
    {
        return $this->boardsModel->getCommentDataByNo($board_no, $comment_no);
    }

    public function commentWriteUpdate($board_id, $article_no, $comment_no, $parent_no)
    {
        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardsConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }

        // comment_no 가 있다면. 실제 댓글이 있는지 확인.
        $comment = [];
        if ($comment_no) {
            $result = $this->getComments($boardsConfig['no'], $article_no, $comment_no);
            if ($result['result'] === 'failure') {
                return ['result' => 'failure', 'message' => '댓글 정보가 없습니다.'];
            }
            $comment = $result['data'][0];
        }

        // parent_no 가 있다면 부모 글 정보를 가져옴.
        $parent = [];
        if ($parent_no) {
            $result = $this->getComments($boardsConfig['no'], $article_no, $parent_no);
            if ($result['result'] === 'failure') {
                return ['result' => 'failure', 'message' => '댓글 정보가 없습니다.'];
            }
            $parent = $result['data'][0];
        }

        // 현재 인증된 회원 ID 가져오기
        $mb_no = $_SESSION['auth']['mb_no'] ?? null;
        $memberData = $this->membersHelper->getMemberDataByNo($mb_no);
        

        // POST 데이터는 formData 배열로 전송 됨
        $formData = $_POST['formData'] ?? null;
        if (empty($formData)) {
            return ['result' => 'failure', 'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'];
        }

        // 이미지 저장 디렉토리
        $storagePath = "/storage/board/$board_id/";
        
        $content = $formData['content'];
        $content = CommonHelper::updateStorageImages($content, $storagePath);

        // formData에 추가
        $formData['board_no'] = $boardsConfig['no'];
        $formData['article_no'] = $article_no;
        $formData['parent_no']  = $parent_no;
        $formData['nickName'] = $memberData['nickName'] ?? "GUEST";
        $formData['content'] = $content;
        $formData['path'] = !empty($parent) ? $parent['path'] : '';
        
        $numericFields = ['board_no', 'article_no', 'parent_no'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        // 실제 게시판 업데이트
        return $this->boardsModel->commentWriteUpdate($comment_no, $board_id, $data);
    }

    /**
     * 댓글을 가져오는 서비스 메서드
     * @param int $board_no 게시판 번호
     * @param int|null $article_no 게시글 번호 (없을 경우 전체 댓글 가져오기)
     * @param int|null $comment_no 댓글 번호 (특정 댓글만 가져오기)
     * @param int $page 현재 페이지 번호
     * @param int $perPage 페이지당 댓글 수
     * @return array 댓글 목록
     */
    public function getComments(
        int $board_no, 
        ?int $article_no = null, 
        ?int $comment_no = null, 
        int $page = 1, 
        int $perPage = 10
    ): array {
        error_log("Service Page:".print_r($page, true));
        $offset = ($page - 1) * $perPage;
        error_log("Service offset:".print_r($offset, true));

        // 특정 게시글 또는 게시판의 모든 댓글을 가져오는 경우
        return $this->boardsModel->getComments($board_no, $article_no, $comment_no, $offset, $perPage);
    }
}