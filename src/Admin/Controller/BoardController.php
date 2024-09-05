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

        // boardsService를 초기화할 때 BoardsHelper 인스턴스를 전달합니다.
        $this->boardsService = new BoardsService(
            $this->boardsModel,
            $this->AdminboardsService,
            $this->boardsHelper,
            $this->membersHelper
        );

        // BoardsHelper에 boardsService를 설정합니다.
        $this->boardsHelper->setBoardsService($this->boardsService);
        
        $this->configDomain = $container->get('config_domain');

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
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
         * $currentPage = $params['currentPage'];
         * $searchQuery = $params['searchQuery'];
         * $filters = $params['filters'];
         * $sort = $params['sort'];
         * $additionalQueries = $params['additionalQueries'];
         */
        // $category 가 있을 경우 카테고리는 카테고리명으로 받게 되므로, category_no를 가져와야 함. 차후 추가

        // 게시판 설정 데이터 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($boardId);

        // 게시판의 카테고리 데이터
        $categoryData = [];

        // 총 게시물 수
        /*
         * $additionalParams 가 있을 경우 해당 배열을 인수에 추가해야 함.
        */
        $totalItems = $this->boardsService->getTotalArticleCount($boardsConfig['no'], $params['searchQuery'], $params['filters'], $params['additionalQueries']);
        $articleData = $this->boardsService->getArticleListData($boardsConfig['no'], $params['currentPage'], $params['page_rows'], $params['searchQuery'], $params['filters'], $params['sort'], $params['additionalQueries']);

        // 페이징 데이터 계산
        $paginationData = CommonHelper::getPaginationData($totalItems, $params['currentPage'], $params['page_rows'], $params['page_nums']);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 목록 관리',
            'boardsConfig' => $boardsConfig,
            'boardId' => $boardId,
            'categoryData' => $categoryData,
            'articleData' => $articleData,
            'paginationData' => $paginationData,
        ];

        return ['Board/list', $viewData];
    }

    public function view($vars)
    {
        $boardId = $vars['boardId'];

        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($boardId);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 글쓰기',
            'boardId' => $boardId,
            'boardsConfig' => $boardsConfig,
        ];

        return ['Board/view', $viewData];
    }

    public function write($vars)
    {
        $boardId = $vars['boardId'];

        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($boardId);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 글쓰기',
            'boardId' => $boardId,
            'boardsConfig' => $boardsConfig,
        ];

        return ['Board/write', $viewData];
    }

    public function update()
    {
        // 디버깅 로그 (개발 환경에서만)
        error_log(print_r($_POST, true));

        $boardId = $_POST['boardId'] ?? null;
        $no = CommonHelper::pickNumber($_POST['no'], 0) ?? 0;

        // 게시판 설정 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($boardId);

        if (!$boardId || empty($boardsConfig)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '선택된 게시판 설정 정보가 없습니다.'
            ]);
        }

        // POST 데이터는 formData 배열로 전송 됨
        $formData = $_POST['formData'] ?? null;
        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        // formData에 추가
        $formData['group_no'] = $boardsConfig['group_no'];
        $formData['board_no'] = $boardsConfig['no'];

        $numericFields = ['group_no'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $result = $this->boardsService->writeBoardsUpdate($boardId, $data);

        return CommonHelper::jsonResponse($result);
    }
}