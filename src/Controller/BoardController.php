<?php
// 파일 위치: /src/Admin/Controller/BoardController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;

use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CookieManager;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;

use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

use Web\PublicHtml\Helper\ImageHelper;

class BoardController
{
    protected $container;
    protected $sessionManager;
    protected $adminBoardsModel;
    protected $adminBoardsService;
    protected $boardsHelper;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $boardsService;
    protected $boardsModel;
    protected $config_domain;
    protected $formDataMiddleware;
    
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->sessionManager = $this->container->get('SessionManager');
        $this->adminBoardsService = $this->container->get('AdminBoardsService');
        $this->boardsHelper = $this->container->get('BoardsHelper');
        $this->membersService = $this->container->get('MembersService');
        $this->membersHelper = $this->container->get('MembersHelper');
        $this->boardsService = $this->container->get('BoardsService');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
    }

    /**
     * 게시글 목록을 표시합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array 뷰 경로와 뷰 데이터를 포함한 배열
     */
    public function list($vars)
    {
        // 게시판 ID 유효성 검사 및 설정 로드
        $boardId = CommonHelper::validateParam('boardId', 'string', $vars['boardId']) ?? null;
        $boardConfig = $this->adminBoardsService->getBoardsConfig($boardId);
        $viewPath = 'Board/'.$boardConfig['board_skin'].'/list';

        // 게시판 정보가 없으면 리다이렉트
        if (empty($boardConfig)) {
            $message = '게시판 정보를 찾을 수 없습니다.';
            $url = '/';
            CommonHelper::alertAndRedirect($message, $url);
        }

        echo ImageHelper::initialize();

        // 게시판 카테고리 데이터 로드
        $categoryData = $this->adminBoardsService->getBoardsCategoryMapping($boardConfig['no']);
        
        // 게시글 목록 데이터 가져오기
        $articleData = $this->getArticleList($boardConfig);
        
        // 페이지네이션 데이터 계산
        $paginationData = $this->calculatePagination($articleData);
        
        // 게시글 목록 HTML 생성
        $articleHtml = $this->boardsService->loadArticleList($boardConfig, $articleData, $paginationData);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 목록 관리',
            'boardConfig' => $boardConfig,
            'boardId' => $boardId,
            'categoryData' => $categoryData,
            'articleHtml' => $articleHtml,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => $viewPath,
            'viewData' => $viewData,
        ];
    }
    
    /**
     * 게시글 목록 데이터를 가져옵니다.
     * 
     * @param array $boardConfig 게시판 설정 데이터
     * @return array 게시글 목록 데이터 (파라미터, 총 아이템 수, 게시글 목록)
     */
    protected function getArticleList($boardConfig = [])
    {
        // 기본 설정 로드
        $config = [
            'cf_page_rows' => $this->config_domain['cf_page_rows'],
            'cf_page_nums' => $this->config_domain['cf_page_nums']
        ];

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['nickName','title','content'];
        $allowedSortFields = ['no', 'create_at'];
        
        // 추가 파라미터 설정 (예: 카테고리)
        $additionalParams = [
            'category[]' => ['array', [], isset($_GET['category']) ? $_GET['category'] : []],
        ];

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 게시글 수 조회
        $totalItems = $this->boardsService->getTotalArticleCount($boardConfig['no'], $params['search'], $params['filter'], $params['additionalQueries']);
        
        // 게시글 목록 데이터 조회
        $articleList = $this->boardsService->getArticleListData(
            $boardConfig['no'],
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'articleList' => $articleList,
        ];
    }

    /**
     * 페이지네이션 데이터를 계산합니다.
     * 
     * @param array $articleData getArticleList()에서 반환된 데이터
     * @return array 페이지네이션 데이터
     */
    protected function calculatePagination($articleData)
    {
        // 쿼리 문자열 생성
        $queryString = CommonHelper::getQueryString($articleData['params']);
        
        // 페이지네이션 데이터 계산 및 반환
        return CommonHelper::getPaginationData(
            $articleData['totalItems'],
            $articleData['params']['page'],
            $articleData['params']['page_rows'],
            $articleData['params']['page_nums'],
            $queryString
        );
    }

    public function view($vars)
    {
        $board_id = $vars['boardId'];
        $article_no = isset($vars['param']) ? $vars['param'] : 0;

        // 게시판 설정 가져오기
        $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);
        $viewPath = 'Board/'.$boardConfig['board_skin'].'/view';

        if (!$board_id  || empty($boardConfig)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        // 에디터 스크립트
        $editor = $boardConfig['board_editor'] ? $boardConfig['board_editor'] : $this->config_domain['cf_editor'];
        $editorScript = CommonHelper::getEditorScript($editor);

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($boardConfig['group_no'], $article_no);
        }
        if(empty($articleData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '게시글 정보가 없습니다.'
            ]);
        }

        // 현재 인증된 회원 ID 가져오기
        $memberData = $this->membersService->getMemberDataByNo();

        /*
         * 권한 체크
         */
        //$result = $this->boardsService->boardPermissionCheck('read', $boardConfig, $articleData, $memberData); 테스트용
        if (!$this->sessionManager->get('auth') || $this->sessionManager->get('auth')['is_super'] === 0) {
            $ss_no = 'board_view_'.$boardConfig['board_id'].'_'.$article_no;
            if (!$this->sessionManager->get($ss_no)) {
                $result = $this->boardsService->boardPermissionCheck('read', $boardConfig, $articleData, $memberData);
                if ($result === false) {
                    CommonHelper::alertAndBack('글읽기 권한이 없거나 포인트가 부족합니다.');
                }
                $this->sessionManager->set($ss_no, 1);
            }
        }

        // 기본 설정 로드
        $config = [
            'cf_page_rows' => $this->config_domain['cf_page_rows'],
            'cf_page_nums' => $this->config_domain['cf_page_nums']
        ];

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['nickName','title','content'];
        $allowedSortFields = ['no', 'create_at'];
        
        // 추가 파라미터 설정 (예: 카테고리)
        $additionalParams = [
            'category[]' => ['array', [], isset($_GET['category']) ? $_GET['category'] : []],
        ];

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);
        
        // 이전글, 다음글
        $adjacentData = $this->boardsService->getAdjacentData($boardConfig, $articleData, $params);
        $prevData = $adjacentData['prevData'];
        $nextData = $adjacentData['nextData'];

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판  글읽기',
            'board_id' => $board_id,
            'boardConfig' => $boardConfig,
            'editorScript' => $editorScript,
            'articleData' => $articleData,
            'prevData' => $prevData,
            'nextData' => $nextData,
        ];
        
        return [
            'viewPath' => $viewPath,
            'viewData' => $viewData,
        ];
    }

    public function write($vars)
    {
        $board_id = $vars['boardId'];
        $article_no = isset($vars['param']) ? $vars['param'] : 0;

        // 게시판 설정 가져오기
        $boardConfig = $this->adminBoardsService->getBoardsConfig($board_id);
        $viewPath = 'Board/'.$boardConfig['board_skin'].'/write';

        if (!$board_id  || empty($boardConfig)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        $memberData = $this->membersService->getMemberDataByNo();

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($boardConfig['group_no'], $article_no);
        }

        /*
         * 게시판 설정의 글쓰기 레벨에 따라 검증할 것
         */
        
        $result = $this->boardsService->boardPermissionCheck('write', $boardConfig, $articleData, $memberData);
        if ($result === false) {
            CommonHelper::alertAndBack('글쓰기 권한이 없거나 포인트가 부족합니다.');
        }

        // 에디터 스크립트
        $editor = $boardConfig['board_editor'] ? $boardConfig['board_editor'] : $this->config_domain['cf_editor'];
        $editor = 'tinymce';
        $editorScript = CommonHelper::getEditorScript($editor);

        // 게시판 개별 카테고리 가져오기
        $boardsCategory = $this->adminBoardsService->getBoardsCategoryMapping($boardConfig['no']);

        

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 글쓰기',
            'boardId' => $board_id,
            'boardConfig' => $boardConfig,
            'boardsCategory' => $boardsCategory,
            'editorScript' => $editorScript,
            'articleData' => $articleData,
            'memberData' => $memberData,
        ];

        return [
            'viewPath' => $viewPath,
            'viewData' => $viewData,
        ];
    }
    
    /*
     * 게시판의 글을 등록하거나 수정합니다.
     * $article_no 의 존재 여부에 따라 등록 또는 수정입니다.
     *
     */
    public function update()
    {
        // 토큰 검증
        $this->formDataMiddleware->validateToken();

        $board_id = CommonHelper::validateParam('board_id', 'string', '', null, INPUT_POST);
        $article_no = CommonHelper::validateParam('article_no', 'int', 0, null, INPUT_POST);

        if (!$board_id) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        $result = $this->boardsService->writeBoardsUpdate($article_no, $board_id);

        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    /*
     * 게시판의 글을 삭제합니다.
     *
     */
    public function delete($vars)
    {
        $this->formDataMiddleware->validateToken();

        $board_id = $vars['boardId'] ?? null;
        $article_no = $vars['param'] ?? null;

        if (!$board_id || !$article_no) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.',
                'data' => $vars,
            ]);
        }

        $result = $this->boardsService->articleDelete($article_no, $board_id);

        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    // -------------------------------------
    // 게시판 댓글
    // -------------------------------------
    
    /*
     * @param $vars boardId,
     * 댓글만 보기..
     */
    // 댓글을 처리하는 메서드
    public function comment($vars)
    {
        $board_id = $vars['boardId'] ?? null;
        $article_no = $vars['articleNo'] ?? null;  // 게시글 번호가 있을 경우
        
        $data = CommonHelper::getJsonInput();
        $page = CommonHelper::pickNumber($data['page']);
        $perPage = CommonHelper::pickNumber($data['perPage']);
        $board_no = 0;
        if ($board_id) {
            // 게시판 설정 가져오기
            $boardsConfig = $this->adminBoardsService->getBoardsConfig($board_id);

            if (empty($boardsConfig)) {
                return CommonHelper::jsonResponse([
                    'result' => 'failure',
                    'message' => '선택된 게시판 설정 정보가 없습니다.'
                ]);
            }
            $board_no = $boardsConfig['no'];
        }

        $result = $this->boardsService->getComments((int)$board_no, (int)$article_no, null, (int)$page, (int)$perPage);
        return CommonHelper::jsonResponse($result);
    }

    /*
     * 게시판 댓글 등록 / 수정
     * @param string $board_id
     * @param int $article_no
     * @param int $comment_no
     * @param int $parent_no
     */
    public function commentWriteUpdate()
    {
        //토큰 검증
        $this->formDataMiddleware->validateToken();

        $board_id = CommonHelper::validateParam('board_id', 'string', '', null, INPUT_POST);
        $article_no = CommonHelper::validateParam('article_no', 'int', 0, null, INPUT_POST);
        $comment_no = CommonHelper::validateParam('comment_no', 'int', 0, null, INPUT_POST); //수정 시
        $parent_no = CommonHelper::validateParam('parent_no', 'int', 0, null, INPUT_POST); //parent_no 가 됨.

        if(!$board_id && !$article_no) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.'
            ]);
        }

        $result = $this->boardsService->commentWriteUpdate($board_id, $article_no, $comment_no, $parent_no);
        
        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    /*
     * 좋아요, 싫어요
     */
    public function like() {
        $data = CommonHelper::getJsonInput();
        
        $table = CommonHelper::validateParam('table', 'string', '', $data['table'], null);
        $action = CommonHelper::validateParam('action', 'string', '', $data['action'], null);
        $no = CommonHelper::validateParam('no', 'int', '', $data['no'], null);

        if (!$table || !$action || !$no || ($table !== 'articles' && $table !== 'comments')) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.',
                'data' => null
            ]);
        }

        $memberData = $this->membersService->getMemberDataByNo();
        if (empty($memberData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '로그인 후 이용하실 수 있습니다.',
                'data' => null
            ]);
        }

        $result = $this->boardsService->processedLikeAction($memberData['mb_id'], $table, $action, $no);

        return CommonHelper::jsonResponse($result);
    }
}