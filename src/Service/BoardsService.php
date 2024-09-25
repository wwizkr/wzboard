<?php
// 파일 위치: /src/Service/BoardsService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\ImageHelper;
use Web\Admin\Service\AdminBoardsService;

class BoardsService
{
    protected DependencyContainer $container;
    protected $config_domain;
    protected $boardsModel;
    protected $boardsHelper;
    protected $adminBoardsService;
    protected $membersService;
    protected $formDataMiddleware;
    protected $sessionManager;
    protected $cookieManager;
    protected array $categoryMapping = [];
    
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->boardsModel = $container->get('BoardsModel');
        $this->boardsHelper = $container->get('BoardsHelper');
        $this->adminBoardsService = $container->get('AdminBoardsService');
        $this->membersService = $container->get('MembersService');
        $this->sessionManager = $container->get('SessionManager');
        $this->cookieManager = $container->get('CookieManager');
        $this->formDataMiddleware = $container->get('FormDataMiddleware');
    }

    private function getCategoryMapping($board_no)
    {
        // 게시판 매핑된 개별 카테고리 가져오기 -> 검색어로 전환
        $boardCategory = $this->adminBoardsService->getBoardsCategoryMapping($board_no);
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
        
        $queryString = CommonHelper::getQueryString($articleData['params']);
        $articleList = $this->boardsHelper->processArticleData($boardConfig, $articleData['articleList']);

        $output = ''; // 최종 출력할 HTML

        // articleData와 paginationData를 사용하여 $num 계산
        foreach ($articleList as $index => $article) {
            // $num 계산식
            $num = $paginationData['totalItems'] - (($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage']) - $index;
            $href = '/board/'.$boardConfig['board_id'].'/view/'.$article['no'].'/'.$article['slug'].'?page='.$paginationData['currentPage'].$queryString;

            // 템플릿 파일의 내용을 기사 데이터로 대체
            $articleHtml = str_replace(
                [
                    '{{num}}',
                    '{{href}}',
                    '{{articleNo}}',
                    '{{boardId}}',
                    '{{thumb}}',
                    '{{title}}',
                    '{{slug}}',
                    '{{nickName}}',
                    '{{hit}}',
                    '{{comment}}',
                    '{{date}}'
                ],
                [
                    $num,
                    $href,
                    $article['no'],
                    $boardConfig['board_id'],
                    $article['thumb'],
                    $article['title'],
                    $article['slug'],
                    $article['nickName'],
                    number_format($article['view_count']),
                    $article['comment'],
                    $article['date1']
                ],
                $template
            );

            // 최종 출력에 추가
            $output .= $articleHtml;
        }

        return $output;
    }

    /*
     * 이전글, 다음글
     * getAdjacentData($boardConfig, $articleData, $params['search'], $params['filter'], $params['additionalQueries']);
     */
    public function getAdjacentData($boardConfig, $articleData, $params)
    {
        $this->categoryMapping = $this->getCategoryMapping($boardConfig['no']);
        $processedQueries = CommonHelper::additionalServiceQueries($params['additionalQueries'], 'category', 'category_no', $this->categoryMapping);
        
        $data = $this->boardsModel->getAdjacentData($boardConfig['no'], $articleData['no'], $params['search'], $params['filter'], $params['sort'], $processedQueries);

        return $data;
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
        $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }
        
        // $article_no 가 있다면 실제 게시글이 있는 지 확인
        if ($article_no) {
            $articleData = $this->getArticleDataByNo($boardConfig['group_no'], $article_no);
            if(empty($articleData)) {
                return ['result' => 'failure', 'message' => '게시글 정보를 찾을 수 없습니다. 잘못된 접속입니다.'];
            }
        }

        // 현재 인증된 회원 ID 가져오기
        $memberData = $this->membersService->getMemberDataByNo();

        // POST 데이터는 formData 배열로 전송 됨
        $formData = $_POST['formData'] ?? null;
        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        // 글쓴이 및 비밀번호 설정
        if (!empty($memberData)) {
            $writeName = $memberData['nickName'];
            $writePass = '';
        } else {
            $writeName = $formData['nickName'] ?? '';
            $writePass = $formData['password'] ? CryptoHelper::hashPassword($formData['password']) : '';
        }

        if (empty($memberData) && (!$writeName || !$writePass)) {
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
        $formData['mb_id']    = $memberData['mb_id'] ?? null;
        $formData['group_no'] = $boardConfig['group_no'];
        $formData['board_no'] = $boardConfig['no'];
        $formData['nickName'] = $writeName;
        $formData['password'] = $writePass;
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
        $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }
        
        // $article_no 가 있다면 실제 게시글이 있는 지 확인
        $articleData = [];
        if ($article_no) {
            $articleData = $this->getArticleDataByNo($boardConfig['group_no'], $article_no);
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

        if (!empty($this->getComments($boardConfig['no'], $article_no))) {
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

    /**
     * 게시판 권한 체크 메서드
     *
     * @param string $type 권한 체크 타입 (read, write, download, comment 등)
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 해당 타입의 권한이 있는지 여부
     */
    public function boardPermissionCheck($type, $boardConfig, $articleData, $memberData)
    {
        switch ($type) {
            case 'read':
                return $this->checkViewPermission($boardConfig, $articleData, $memberData);
            case 'write':
                return $this->checkWritePermission($boardConfig, $articleData, $memberData);
            case 'download':
                return $this->checkDownloadPermission($boardConfig, $articleData, $memberData);
            case 'comment':
                return $this->checkCommentPermission($boardConfig, $articleData, $memberData);
            default:
                return false; // 기본적으로 권한 없음
        }
    }

    /**
     * 글 읽기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 읽기 권한이 있는지 여부
     */
    protected function checkViewPermission($boardConfig, $articleData, $memberData)
    {
        if ($this->isOwnArticle($articleData, $memberData)) {
            return true; // 자신의 글이면 조회수 증가 없이 return
        }

        if ($this->isGuestWithReadPermission($boardConfig, $articleData, $memberData)) {
            return true; // 비회원이 읽기 권한을 가지고 있으면 return
        }

        if ($this->isPublicArticle($boardConfig, $articleData)) {
            return $this->processView($articleData); // 공개 글이면 처리
        }

        if ($this->hasReadLevelPermission($boardConfig, $articleData, $memberData)) {
            return $this->processView($articleData, $memberData, $boardConfig); // 권한 있는 회원 처리
        }

        return false;
    }

    /**
     * 글 쓰기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 글 쓰기 권한이 있는지 여부
     */
    protected function checkWritePermission($boardConfig, $articleData, $memberData)
    {
        if (!empty($articleData) && !empty($memberData) && $this->isOwnArticle($articleData, $memberData)) {
            return true; // 자신의 글이면 true
        }

        // 최고관리자가 사용자의 글을 수정할 수 있게 하려면
        if (!empty($articleData) && !empty($memberData) && $memberData['is_super']) {
            return true;
        }
        
        // 최고관리자이면 글 쓰기
        if (empty($articleData) && !empty($memberData) && $memberData['is_super']) {
            return true;
        }

        // 글쓰기 권한 체크
        if (empty($articleData) && $boardConfig['write_level'] === 0) {
            return true;
        }

        if (empty($articleData) && $boardConfig['write_level'] > 0) {
            if (!empty($memberData) && $memberData['member_level'] >= $boardConfig['write_level']) {
                return true;
            }
        }

        // 글 쓰기 권한 체크 로직
        return false;
    }

    /**
     * 파일 다운로드 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 다운로드 권한이 있는지 여부
     */
    protected function checkDownloadPermission($boardConfig, $articleData, $memberData)
    {
        // 파일 다운로드 권한 체크 로직
        if ($this->isOwnArticle($articleData, $memberData)) {
            return true; // 자신의 글이면 다운로드 허용
        }

        return (!empty($memberData) && $memberData['member_level'] >= $boardConfig['download_level']);
    }

    /**
     * 댓글 쓰기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 댓글 쓰기 권한이 있는지 여부
     */
    protected function checkCommentPermission($boardConfig, $articleData, $memberData)
    {
        // 댓글 쓰기 권한 체크 로직
        return (!empty($memberData) && $memberData['member_level'] >= $boardConfig['comment_level']);
    }

    /**
     * 사용자가 자신의 글인지 확인하는 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 사용자가 자신의 글인지 여부
     */
    protected function isOwnArticle($articleData, $memberData)
    {
        // 자신의 글인지 확인
        return !empty($memberData) && $articleData['mb_id'] === $memberData['mb_id'];
    }

    /**
     * 비회원의 읽기 권한을 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 비회원이 읽기 권한이 있는지 여부
     */
    protected function isGuestWithReadPermission($boardConfig, $articleData, $memberData)
    {
        // 비회원의 읽기 권한 체크
        return empty($memberData) && $articleData['mb_id'] === '' && 
               $boardConfig['read_level'] === 0 && 
               $articleData['user_ip'] === CommonHelper::getUserIp();
    }

    /**
     * 게시글이 공개된 상태인지 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @return bool 게시글이 공개된 상태인지 여부
     */
    protected function isPublicArticle($boardConfig, $articleData)
    {
        // 공개된 글인지 확인
        return $boardConfig['read_level'] === 0 && $articleData['read_level'] === 0;
    }

    /**
     * 회원이 읽기 권한을 가지고 있는지 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 회원이 읽기 권한이 있는지 여부
     */
    protected function hasReadLevelPermission($boardConfig, $articleData, $memberData)
    {
        // 회원이 읽기 권한을 가지고 있는지 확인
        return !empty($memberData) && (
            ($articleData['read_level'] > 0 && $memberData['member_level'] >= $articleData['read_level']) ||
            ($boardConfig['read_level'] > 0 && $memberData['member_level'] >= $boardConfig['read_level'])
        );
    }

    /**
     * 조회수 업데이트 및 포인트 처리 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @param array|null $memberData 회원 데이터 배열 (옵션)
     * @param array|null $boardConfig 게시판 설정 배열 (옵션)
     * @return bool 조회수 업데이트 및 포인트 처리 성공 여부
     */
    protected function processView($articleData, $memberData = null, $boardConfig = null)
    {
        // 조회수 업데이트 및 포인트 처리 로직
        if ($memberData !== null && $boardConfig !== null) {
            if ($this->updateArticleMemberPoint($memberData, $boardConfig, $articleData, 'read') === false) {
                return false;
            }
        }
        $this->articleViewCountUpdate($articleData);
        return true;
    }

    /**
     * 게시글 조회수 업데이트 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @return bool 조회수 업데이트 성공 여부
     */
    public function articleViewCountUpdate($articleData)
    {
        return $this->boardsModel->articleViewCountUpdate($articleData);
    }

    /**
     * 회원 포인트 업데이트 메서드 (예시)
     *
     * @param array $articleData 게시글 데이터 배열
     * @return bool 포인트 업데이트 성공 여부
     */
    public function updateArticleMemberPoint($memberData, $boardConfig, $articleData, $type = null)
    {
        if ($type === null || !in_array($type, ['read', 'write', 'comment', 'download'])) {
            return true;
        }
        
        $board_point_field = $type.'_point';
        $cf_point_field = 'cf_board_'.$type.'_point';
        /*
         * 게시글 자체에 포인트가 있는 경우 차후 로직 추가 =>
         */
        $point = 0;
        $board_point = $boardConfig[$board_point_field] ?? 0;
        $cf_point = $this->config_domain[$cf_point_field] ?? 0;
        if ($board_point !== 0) {
            if ($board_point < 0 && $memberData['point'] < ($board_point * -1)) {
                return false;
            }
            $point = $board_point;
        }
        if ($point === 0 && $cf_point !== 0) {
            if ($cf_point < 0 && $memberData['point'] < ($cf_point * -1)) {
                return false;
            }
            $point = $cf_point;
        }

        if ($point !== 0) { // 실제 포인트 업데이트 로직은 아래 주석을 사용하여 구현
            $point_type = $point > 0 ? '적립' : '사용';
            $point_rel_type = '@board';
            $point_rel_id = $boardConfig['board_id'].'-'.$type.'-'.$articleData['no'];
            $data = [
                'cf_id' => ['i', $this->config_domain['cf_id']],
                'mb_id' => ['s', $memberData['mb_id']],
                'point_rel_type' => ['s', $point_rel_type],
                'point_rel_id' => ['s', $point_rel_id],
            ];

            // 먼저 해당 내용에 대한 적립금 내역이 등록되어 있는지 확인
            if ($this->boardsModel->checkArticleMemberPoint($data) === true) {
                return true;
            }
            
            $msg = '';
            if ($type === 'read') {
                $msg = '글 읽기';
            }
            if ($type === 'write') {
                $msg = '글 쓰기';
            }
            if ($type === 'download') {
                $msg = '다운로드';
            }
            if ($type === 'comment') {
                $msg = '댓글 쓰기';
            }
            $point_content = $boardConfig['board_name'].'-'.$articleData['title'].'-'.$msg;
            $data['point'] = $point;
            $data['point_type'] = ['s', $point_type];
            $data['point_content'] = ['s', $point_content];

            return $this->boardsModel->updateArticleMemberPoint($memberData['mb_id'], $memberData['point'], $point, $data);
        }

        return true;
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
        $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardConfig)) {
            return ['result' => 'failure', 'message' => '선택된 게시판 설정 정보가 없습니다.'];
        }

        // comment_no 가 있다면. 실제 댓글이 있는지 확인.
        $comment = [];
        if ($comment_no) {
            $result = $this->getComments($boardConfig['no'], $article_no, $comment_no);
            if ($result['result'] === 'failure') {
                return ['result' => 'failure', 'message' => '댓글 정보가 없습니다.'];
            }
            $comment = $result['data'][0];
        }

        // parent_no 가 있다면 부모 글 정보를 가져옴.
        $parent = [];
        if ($parent_no) {
            $result = $this->getComments($boardConfig['no'], $article_no, $parent_no);
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
        $memberData = $this->membersService->getMemberDataByNo();

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
        $formData['board_no'] = $boardConfig['no'];
        $formData['article_no'] = $article_no;
        $formData['parent_no']  = $parent_no;
        $formData['nickName'] = $memberData['nickName'] ?? "GUEST";
        $formData['content'] = $content;
        $formData['path'] = !empty($parent) ? $parent['path'] : '';
        
        $numericFields = ['board_no', 'article_no', 'parent_no'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        // 실제 게시판 업데이트
        $commentData = $this->boardsModel->commentWriteUpdate($comment_no, $board_id, $data);

        if ($commentData['result'] === 'success' && isset($commentData['comment'])) {
            $processedComment = $this->boardsHelper->processCommentData($boardConfig, [$commentData['comment']]);
            $commentData['comment'] = $processedComment[0];
        }

        return $commentData;
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
        // 게시판 설정 가져오기
        $boardConfig = $this->adminBoardsService->getBoardsConfigByNo($board_no);
        $offset = ($page - 1) * $perPage;
        // 특정 게시글 또는 게시판의 모든 댓글을 가져오는 경우
        $commentData = $this->boardsModel->getComments($board_no, $article_no, $comment_no, $offset, $perPage);
        
        // 댓글 데이터가 없는 경우 바로 빈 배열 반환
        if ($commentData['result'] === 'failure') {
            return $commentData;
        }
        
        $commentList = $this->boardsHelper->processCommentData($boardConfig, $commentData['data']);
        
        // processCommentData가 null을 반환하거나 빈 배열을 반환할 경우를 처리
        if (empty($commentList)) {
            return [
                'result' => 'success',
                'data' => [],
                'message' => '처리된 댓글 데이터가 없습니다.'
            ];
        }
        
        return [
            'result' => 'success',
            'data' => $commentList,
        ];
    }

    public function processedLikeAction($mb_id, $table, $action, $no)
    {
        $result = $this->boardsModel->processedLikeAction($mb_id, $table, $action, $no);

        return $result;
    }
    
    // 사용안함.
    // 개별 댓글 -- 삭제 예정
    public function getCommentDataByNo($board_no, $comment_no)
    {
        return $this->boardsModel->getCommentDataByNo($board_no, $comment_no);
    }
}