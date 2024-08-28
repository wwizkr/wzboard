<?php
// 파일 위치: /src/Admin/Controller/AdminBoardController.php
// 생성된 게시판 목록, 설정 수정, 글 쓰기 삭제 등 개별 게시판 관리 콘트롤러
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\Admin\Helper\BoardsHelper as AdminBoardsHelper; // 관리자 전용 헬퍼
use Web\PublicHtml\Helper\BoardsHelper;
use Web\Admin\Model\AdminBoardsModel;
use Web\Admin\Service\AdminBoardsService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class AdminBoardController
{
    protected $container;
    protected $boardsHelper;
    protected $boardsModel;
    protected $boardsService;
    protected $membersModel;
    protected $membersService;
    protected $configDomain;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->boardsModel = new AdminBoardsModel($container);
        $this->boardsService = new AdminBoardsService($this->boardsModel);
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->boardsHelper = new BoardsHelper($this->boardsService);
        $this->configDomain = $container->get('config_domain');

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    public function list($vars) // 게시글 목록 작업
    {
        // 페이징 관련 변수 설정
        $page_rows = $this->configDomain['cf_page_rows'];
        $page_nums = $this->configDomain['cf_page_nums'];
        $currentPage = isset($_GET['page']) ? CommonHelper::pickNumber($_GET['page'], 1) : 1;
        $boardId = $vars['boardId'];

        // 게시판 목록 데이터 가져오기
        $boardsConfig = $this->boardsHelper->getBoardsConfig($boardId);

        // 총 게시물 수 (예시용, 실제 데이터 가져오는 코드 필요)
        $totalItems = 0; // 여기에 실제 게시물 수를 할당해야 합니다.

        // 페이징 데이터 계산
        $paginationData = [
            'totalItems' => $totalItems,
            'currentPage' => $currentPage,
            'totalPages' => ceil($totalItems / $page_rows),
            'itemsPerPage' => $page_rows,
            'pageNums' => $page_nums,
        ];

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '게시판 목록 관리',
            'boardsConfig' => $boardsConfig,
            'paginationData' => $paginationData,
            'boardId' => $boardId,
        ];

        return ['Board/list', $viewData];
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

        $numericFields = ['group_no'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $result = $this->boardsService->writeBoardsUpdate($boardId, $data);
    }
}