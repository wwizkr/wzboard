<?php
// 파일 위치: /src/Admin/Controller/BoardController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\ImageHelper;

/**
 * 게시판 관련 기능을 처리하는 컨트롤러 클래스
 */

class BoardController
{
    protected $container;
    protected $sessionManager;
    protected $adminBoardsService;
    protected $membersModel;
    protected $membersService;
    protected $boardsService;
    protected $boardsModel;
    protected $config_domain;
    protected $formDataMiddleware;
    protected $boardId;
    protected $boardConfig;
    protected $viewRenderer;
    
    /**
     * BoardController 생성자
     * 
     * @param DependencyContainer $container 의존성 컨테이너
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->initializeServices();
    }
    
    /**
     * 필요한 서비스들을 초기화합니다.
     */
    protected function initializeServices(): void
    {
        $this->sessionManager = $this->container->get('SessionManager');
        $this->adminBoardsService = $this->container->get('AdminBoardsService');
        $this->membersService = $this->container->get('MembersService');
        $this->boardsService = $this->container->get('BoardsService');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->viewRenderer = $this->container->get('ViewRenderer');
    }
    
    /**
     * 게시판 설정을 초기화합니다.
     * 
     * @param array $vars 요청 변수
     */
    protected function initializeBoardConfig(array $vars): void
    {
        $this->boardId = CommonHelper::validateParam('boardId', 'string', $vars['boardId'] ?? null) ?? null;
        $this->boardConfig = $this->adminBoardsService->getBoardsConfig($this->boardId);

        if (empty($this->boardConfig)) {
            $message = '게시판 정보를 찾을 수 없습니다.';
            $url = '/';
            CommonHelper::alertAndRedirect($message, $url);
        }
    }
    
    /**
     * 게시판에 필요한 asset을 설정합니다.
     */
    protected function setAssets(): void
    {
        $this->viewRenderer->addAsset('css', '/assets/css/board/'.$this->boardConfig['board_skin'].'/style.css');
    }

    /**
     * 게시글 목록을 표시합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array 뷰 경로와 뷰 데이터를 포함한 배열
     */
    public function list(array $vars): array
    {
        // 게시판 ID 유효성 검사 및 설정 로드
        $this->initializeBoardConfig($vars);
        $viewPath = 'Board/'.$this->boardConfig['board_skin'].'/list';
        $this->setAssets();

        // 게시판 카테고리 데이터 로드
        $categoryData = $this->adminBoardsService->getBoardsCategoryMapping($this->boardConfig['no']);
        
        // 게시글 목록 데이터 가져오기
        $articleData = $this->boardsService->getArticleList($this->boardConfig);
        
        // 페이지네이션 데이터 계산
        $paginationData = $this->boardsService->calculatePagination($articleData);
        
        // 게시글 목록 HTML 생성
        $articleHtml = $this->boardsService->loadArticleList($this->boardConfig, $articleData, $paginationData);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 목록 관리',
            'boardConfig' => $this->boardConfig,
            'boardId' => $this->boardId,
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
     * 개별 게시글을 표시합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array 뷰 경로와 뷰 데이터를 포함한 배열
     */
    public function view(array $vars): array
    {
        $this->initializeBoardConfig($vars);
        $this->setAssets();
        $article_no = isset($vars['param']) ? $vars['param'] : 0;

        // 게시판 설정 가져오기
        $viewPath = 'Board/'.$this->boardConfig['board_skin'].'/view';

        // 에디터 스크립트
        $editor =$this->boardConfig['board_editor'] ? $this->boardConfig['board_editor'] : $this->config_domain['cf_editor'];
        $editorScript = CommonHelper::getEditorScript($editor);

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($this->boardConfig, $article_no);
        }
        if(empty($articleData)) {
            $message = '게시글 정보가 없습니다.';
            CommonHelper::alertAndBack($message);
        }

        // 리액션
        $articleReaction = [];
        $reactionArray = $this->boardConfig['is_article_reaction'] ? explode(",", $this->boardConfig['is_article_reaction']) : [];
        foreach($reactionArray as $key=>$val) {
            $value = explode(":", $val);
            $articleReaction[$key]['field'] = $value[0];
            $articleReaction[$key]['text'] = $value[1];
        }

        // 현재 인증된 회원 ID 가져오기
        $memberData = $this->membersService->getMemberDataByNo();

        /*
         * 권한 체크
         */
        $is_modify_button = true;
        $is_delete_button = true;
        if (!$this->sessionManager->get('auth') || $this->sessionManager->get('auth')['is_super'] === 0) {
            $ss_no = 'board_view_'.$this->boardConfig['board_id'].'_'.$article_no;
            if (!$this->sessionManager->get($ss_no)) {
                $result = $this->boardsService->boardPermissionCheck('read', $this->boardConfig, $articleData, $memberData);
                if ($result === false) {
                    CommonHelper::alertAndBack('글읽기 권한이 없거나 포인트가 부족합니다.');
                }
                $this->sessionManager->set($ss_no, 1);
            }

            // 글 수정 버튼
            $is_modify_button = $this->boardsService->boardPermissionCheck('modify', $this->boardConfig, $articleData, $memberData);
            $is_delete_button = $this->boardsService->boardPermissionCheck('delete', $this->boardConfig, $articleData, $memberData);
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
        $adjacentData = $this->boardsService->getAdjacentData($this->boardConfig, $articleData, $params);
        $prevData = $adjacentData['prevData'];
        $nextData = $adjacentData['nextData'];

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판  글읽기',
            'board_id' => $this->boardId,
            'boardConfig' => $this->boardConfig,
            'editorScript' => $editorScript,
            'articleData' => $articleData,
            'articleReaction' => $articleReaction,
            'is_modify_button' => $is_modify_button,
            'is_delete_button' => $is_delete_button,
            'prevData' => $prevData,
            'nextData' => $nextData,
        ];
        
        return [
            'viewPath' => $viewPath,
            'viewData' => $viewData,
        ];
    }
    
    /**
     * 게시글 작성 폼을 표시합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array 뷰 경로와 뷰 데이터를 포함한 배열
     */
    public function write(array $vars): array
    {
        $this->initializeBoardConfig($vars);
        $this->setAssets();

        $article_no = isset($vars['param']) ? $vars['param'] : 0;
        $viewPath = 'Board/'.$this->boardConfig['board_skin'].'/write';

        $memberData = $this->membersService->getMemberDataByNo();

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($this->boardConfig['group_no'], $article_no);
        }

        /*
         * 게시판 설정의 글쓰기 레벨에 따라 검증할 것
         */
        
        $result = $this->boardsService->boardPermissionCheck('write', $this->boardConfig, $articleData, $memberData);
        if ($result === false) {
            CommonHelper::alertAndBack('글쓰기 권한이 없거나 포인트가 부족합니다.');
        }

        // 에디터 스크립트
        $editor = $this->boardConfig['board_editor'] ? $this->boardConfig['board_editor'] : $this->config_domain['cf_editor'];
        $editorScript = CommonHelper::getEditorScript($editor);

        // 게시판 개별 카테고리 가져오기
        $boardsCategory = $this->adminBoardsService->getBoardsCategoryMapping($this->boardConfig['no']);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 글쓰기',
            'boardId' => $this->boardId,
            'boardConfig' => $this->boardConfig,
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
    public function update(array $vars): array
    {
        $this->initializeBoardConfig($vars);

        // 토큰 검증
        $this->formDataMiddleware->validateToken();
        $article_no = CommonHelper::validateParam('article_no', 'int', 0, null, INPUT_POST);

        $result = $this->boardsService->writeBoardsUpdate($article_no, $this->boardId);

        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    /*
     * 게시판의 글을 삭제합니다.
     *
     */
    public function delete(array $vars): array
    {
        $this->initializeBoardConfig($vars);

        $this->formDataMiddleware->validateToken();

        $article_no = $vars['param'] ?? null;

        if (!$this->boardId || !$article_no) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.',
                'data' => $vars,
            ]);
        }

        $result = $this->boardsService->articleDelete($article_no, $this->boardId);

        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    /**
     * 댓글 목록을 가져옵니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array JSON 응답 데이터
     */
    public function comment(array $vars): array
    {
        $this->initializeBoardConfig($vars);
        
        $article_no = $vars['articleNo'] ?? null;  // 게시글 번호가 있을 경우
        
        $data = CommonHelper::getJsonInput();
        $page = CommonHelper::pickNumber($data['page']);
        $perPage = CommonHelper::pickNumber($data['perPage']);
        $board_no = $this->boardConfig['no'];

        $result = $this->boardsService->getComments($this->boardConfig, (int)$article_no, null, (int)$page, (int)$perPage);
        return CommonHelper::jsonResponse($result);
    }

    /**
     * 댓글을 등록하거나 수정합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array JSON 응답 데이터
     */
    public function commentWriteUpdate(array $vars): array
    {
        $this->initializeBoardConfig($vars);

        //토큰 검증
        $this->formDataMiddleware->validateToken();

        $article_no = CommonHelper::validateParam('article_no', 'int', 0, null, INPUT_POST);
        $comment_no = CommonHelper::validateParam('comment_no', 'int', 0, null, INPUT_POST); //수정 시
        $parent_no = CommonHelper::validateParam('parent_no', 'int', 0, null, INPUT_POST); //parent_no 가 됨.

        if(!$article_no) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.'
            ]);
        }

        $result = $this->boardsService->commentWriteUpdate($this->boardConfig, $article_no, $comment_no, $parent_no);
        
        // 결과를 JSON 응답으로 반환
        return CommonHelper::jsonResponse($result);
    }

    /**
     * 게시글 또는 댓글에 대한 좋아요/싫어요 처리를 합니다.
     * 
     * @param array $vars 라우팅에서 전달된 변수들
     * @return array JSON 응답 데이터
     */
    public function like(array $vars): array
    {
        $this->initializeBoardConfig($vars);

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