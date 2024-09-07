<?php
// 파일 위치: /src/Admin/Controller/BoardController.php
// 생성된 게시판 목록, 설정 수정, 글 쓰기 삭제 등 개별 게시판 관리 콘트롤러
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\Admin\Helper\AdminBoardsHelper;
use Web\PublicHtml\Helper\BoardsHelper;
use Web\PublicHtml\Helper\MembersHelper;
use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class BoardController
{
    protected $container;
    protected $boardsHelper;
    protected $AdminboardsModel;
    protected $AdminboardsService;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $boardsService;
    protected $boardsModel;
    protected $configDomain;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->AdminboardsModel = new AdminBoardsModel($container);
        $this->AdminboardsService = new AdminBoardsService($this->AdminboardsModel);
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->boardsModel = new BoardsModel($container);

        // BoardsHelper 인스턴스를 먼저 생성합니다.
        $this->boardsHelper = new BoardsHelper($this->AdminboardsService);

        // MembersHelper 인스턴스를 생성할 때 MembersService를 전달합니다.
        $this->membersHelper = new MembersHelper($this->membersService);
        
        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);

        // boardsService를 초기화할 때 BoardsHelper 인스턴스를 전달합니다.
        $this->boardsService = new BoardsService(
            $this->boardsModel,
            $this->AdminboardsService,
            $this->boardsHelper,
            $this->membersHelper,
            $this->formDataMiddleware
        );

        // BoardsHelper에 boardsService를 설정합니다.
        $this->boardsHelper->setBoardsService($this->boardsService);
        $this->configDomain = $container->get('config_domain');
    }

    public function list($vars) // 게시글 목록 작업
    {
        $boardId = $vars['boardId'] ?? null;
    
        $config = [
            'cf_page_rows' => $this->configDomain['cf_page_rows'],
            'cf_page_nums' => $this->configDomain['cf_page_nums']
        ];

        $allowedFilters = ['nickName']; // 검색어와 매칭시킬 필드
        $allowedSortFields = ['no', 'create_at']; // 정렬에 사용할 필드
        
        // 추가 검색에 사용할 필드 및 값
        // array 사용의 경우 OR 검색으로 여러개의 검색 결과
        // string 사용의 경우 단일 검색
        $additionalParams = [
            'category[]' => ['array', [], isset($_GET['category']) ? $_GET['category'] : []],
            //'status' => ['string', 'all', ['all', 'active', 'inactive']] // 단일 검색 추가 예시
        ];
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        /*
         * $params // 결과 사용
         * $params['currentPage'];
         * $params['searchQuery'];
         * $params['filters'];
         * $params['sort']['order'];
         * $params['sort']['field'];
         * $params['additionalQueries'];
         */

        // 게시판 설정 데이터 가져오기
        $boardConfig = $this->boardsHelper->getBoardsConfig($boardId);

        // 게시판의 카테고리 데이터
        $categoryData = [];

        // 총 게시물 수
        /*
         * $additionalParams 가 있을 경우 해당 배열을 인수에 추가해야 함.
        */
        $totalItems = $this->boardsService->getTotalArticleCount($boardConfig['no'], $params['searchQuery'], $params['filters'], $params['additionalQueries']);
        $articleData = $this->boardsService->getArticleListData(
            $boardConfig['no'],
            $params['currentPage'],
            $params['page_rows'],
            $params['searchQuery'],
            $params['filters'],
            $params['sort'],
            $params['additionalQueries']
        );

        // 페이징 데이터 계산
        $paginationData = CommonHelper::getPaginationData($totalItems, $params['currentPage'], $params['page_rows'], $params['page_nums']);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 목록 관리',
            'boardConfig' => $boardConfig,
            'boardId' => $boardId,
            'categoryData' => $categoryData,
            'articleData' => $articleData,
            'paginationData' => $paginationData,
        ];

        return ['Board/list', $viewData];
    }

    public function view($vars)
    {
        $board_id = $vars['boardId'];
        $article_no = isset($vars['param']) ? $vars['param'] : 0;

        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardsConfig)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        // 현재 인증된 회원 ID 가져오기
        $mb_no = $_SESSION['auth']['mb_no'] ?? null;
        $memberData = $this->membersHelper->getMemberDataByNo($mb_no);
        /*
         * 게시판 설정의 글쓰기 레벨에 따라 검증할 것
         * 관리자는 필요없음.
         */

        // 에디터 스크립트
        $editor = $boardsConfig['board_editor'] ? $boardsConfig['board_editor'] : $this->configDomain['cf_editor'];
        $editor = 'tinymce';
        $editorScript = CommonHelper::getEditorScript($editor);

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($boardsConfig['group_no'], $article_no);
        }
        if(empty($articleData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '게시글 정보가 없습니다.'
            ]);
        }

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판  글읽기',
            'board_id' => $board_id,
            'boardsConfig' => $boardsConfig,
            'editorScript' => $editorScript,
            'articleData' => $articleData,
        ];

        return ['Board/view', $viewData];
    }

    public function write($vars)
    {
        $board_id = $vars['boardId'];
        $article_no = isset($vars['param']) ? $vars['param'] : 0;

        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

        if (!$board_id  || empty($boardsConfig)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        // 현재 인증된 회원 ID 가져오기
        $mb_no = $_SESSION['auth']['mb_no'] ?? null;
        $memberData = $this->membersHelper->getMemberDataByNo($mb_no);
        /*
         * 게시판 설정의 글쓰기 레벨에 따라 검증할 것
         */


        // 에디터 스크립트
        $editor = $boardsConfig['board_editor'] ? $boardsConfig['board_editor'] : $this->configDomain['cf_editor'];
        $editor = 'tinymce';
        $editorScript = CommonHelper::getEditorScript($editor);

        // 게시판 개별 카테고리 가져오기
        $boardsCategory = $this->boardsHelper->getBoardsCategoryMapping($boardsConfig['no']);

        // 글 정보
        $articleData = [];
        if($article_no) {
            $articleData = $this->boardsService->getArticleDataByNo($boardsConfig['group_no'], $article_no);
        }

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 글쓰기',
            'boardId' => $board_id,
            'boardsConfig' => $boardsConfig,
            'boardsCategory' => $boardsCategory,
            'editorScript' => $editorScript,
            'articleData' => $articleData,
            'memberData' => $memberData,
        ];

        return ['Board/write', $viewData];
    }
    
    /*
     * 게시판의 글을 등록하거나 수정합니다.
     * $article_no 의 존재 여부에 따라 등록 또는 수정입니다.
     *
     */
    public function update()
    {
        // 토큰 검증
        $this->formDataMiddleware->validateToken('admin');

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
        $page = CommonHelper::validateParam('page', 'int', 1, $data['page']);
        $perPage = CommonHelper::validateParam('perPage', 'int', 10, $data['perPage']);

        $board_no = 0;

        if ($board_id) {
            // 게시판 설정 가져오기
            $boardsConfig = $this->boardsHelper->getBoardsConfig($board_id);

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
        $this->formDataMiddleware->validateToken('admin');

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
}