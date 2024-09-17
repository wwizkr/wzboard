<?php
// 파일 위치: /src/Service/BoardsService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Core\DependencyContainer;

class BoardsService
{
    protected DependencyContainer $container;
    protected BoardsModel $boardsModel;
    protected BoardsHelper $boardsHelper;
    protected MembersHelper $membersHelper;
    protected FormDataMiddleware $formDataMiddleware;
    protected array $categoryMapping = [];
    
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->boardsModel = $container->get('BoardsModel');
        $this->boardsHelper = $container->get('BoardsHelper');
        $this->membersHelper = $container->get('MembersHelper');
        $this->formDataMiddleware = $container->get('FormDataMiddleware');
    }

    private function getCategoryMapping($board_no)
    {
        // 게시판 매핑된 개별 카테고리 가져오기 -> 검색어로 전환
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
        
        $data = $this->boardsModel->getArticleListData(
            $board_no, 
            $currentPage, 
            $page_rows, 
            $searchQuery, 
            $filters, 
            $sort, 
            $processedQueries
        );
        
        foreach ($data as $key => $articleData) {
            if (isset($articleData['password'])) {
                unset($data[$key]['password']);
            }
        }

        return $data;
    }
    
    public function loadArticleList($boardConfig, $articleData, $paginationData)
    {
        $templatePath = __DIR__ . '../../View/Board/'.$boardConfig['board_skin'].'/articleTemplate.html';
        $template = file_get_contents($templatePath);

        // 템플릿이 제대로 로드되었는지 확인
        if ($template === false) {
            return '템플릿 파일을 찾을 수 없습니다.';
        }

        $output = ''; // 최종 출력할 HTML

        // articleData와 paginationData를 사용하여 $num 계산
        foreach ($articleData as $index => $article) {
            // $num 계산식
            $num = $paginationData['totalItems'] - (($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage']) - $index;

            // 템플릿 파일의 내용을 기사 데이터로 대체
            $articleHtml = str_replace(
                [
                    '{{num}}',
                    '{{articleNo}}',
                    '{{boardId}}',
                    '{{title}}',
                    '{{slug}}',
                    '{{nickName}}',
                    '{{hit}}',
                    '{{comment}}',
                    '{{date}}'],
                [
                    $num,
                    $article['no'],
                    $boardConfig['board_id'],
                    htmlspecialchars($article['title']),
                    htmlspecialchars($article['slug']),
                    htmlspecialchars($article['nickName']),
                    number_format($article['view_count']),
                    number_format($article['comment_count']),
                    htmlspecialchars($article['created_at'])
                ],
                $template
            );

            // 최종 출력에 추가
            $output .= $articleHtml;
        }

        return $output;
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

        // 현재 인증된 회원 ID 가져오기
        $memberData = $this->membersHelper->getMemberDataByNo();

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
        unset($formData['content']);
        $content = CommonHelper::updateStorageImages($content, $storagePath);
        
        $slug = CommonHelper::generateSlug($formData['title']);
        
        // formData에 추가
        $formData['group_no'] = $boardsConfig['group_no'];
        $formData['board_no'] = $boardsConfig['no'];
        $formData['nickName'] = $memberData['nickName'] ?? "GUEST";
        $formData['content'] = $content;
        $formData['slug'] = $slug;
        $formData['user_ip'] = CommonHelper::getUserIp();

        $numericFields = ['group_no', 'board_no'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        // 실제 게시판 업데이트
        return $this->boardsModel->writeBoardsUpdate($article_no, $board_id, $data);
    }

    /*
     * 게시글 삭제
     * @param string $boardId 게시판 ID
     * @param array $data 게시글 데이터
     * @return mixed 업데이트 결과
     */
    public function articleDelete($article_no, $board_id)
    {
        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardsConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }
        
        // $article_no 가 있다면 실제 게시글이 있는 지 확인
        $articleData = [];
        if ($article_no) {
            $articleData = $this->getArticleDataByNo($boardsConfig['group_no'], $article_no);
            if(empty($articleData)) {
                return ['result' => 'failure', 'message' => '게시글 정보를 찾을 수 없습니다. 잘못된 접속입니다.'];
            }
        }

        /*
         * 회원이 작성한 글일 경우, 본인이 아니면 삭제 불가
         * 비회원이 작성한 글일 경우 비밀번호 입력폼으로 보내야 함.
         * 전체관리자인 경우 댓글이 있더라도 삭제 가능
         * 삭제된 글 및 관련 댓글, 첨부파일은 - 별도의 테이블로 보냄 ???
         * 글 삭제 로직은 차후 추가.
         */


        if (!empty($this->getComments($boardsConfig['no'], $article_no))) {
            return ['result' => 'failure', 'message' => '댓글이 있는 게시글을 삭제할 수 없습니다.'];
        }

        return ['result' => 'success', 'message' => '게시글을 삭제하였습니다.'];
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
        /*
        $mb_no = $_SESSION['auth']['mb_no'] ?? null;
        if ($mb_no) {
            $memberData = $this->membersHelper->getMemberDataByNo($mb_no);
        }
        */
        $memberData = $this->membersHelper->getMemberDataByNo();

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
        $offset = ($page - 1) * $perPage;
        // 특정 게시글 또는 게시판의 모든 댓글을 가져오는 경우
        return $this->boardsModel->getComments($board_no, $article_no, $comment_no, $offset, $perPage);
    }
    
    // 사용안함.
    // 개별 댓글 -- 삭제 예정
    public function getCommentDataByNo($board_no, $comment_no)
    {
        return $this->boardsModel->getCommentDataByNo($board_no, $comment_no);
    }
}