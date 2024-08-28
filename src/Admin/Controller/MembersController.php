<?php
// 파일 위치: /src/Admin/Controller/MemberController.php
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class MembersController
{
    protected $container;
    protected $membersModel;
    protected $membersService;
    protected $configDomain;
    protected $cf_id;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->membersModel = new MembersModel($container);
        $this->membersService = new MembersService($this->membersModel);
        $this->configDomain = $this->container->get('config_domain');
        $this->cf_id = $this->configDomain['cf_id'];

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    protected function getLevelData()
    {
        return $this->membersService->getMemberLevelData();
    }

    public function list($vars)
    {
        $page_rows = $this->configDomain['cf_page_rows'];
        $page_nums = $this->configDomain['cf_page_nums'];

        // 현재 페이지 받아오기
        $currentPage = isset($_GET['page']) ? CommonHelper::pickNumber($_GET['page'],1) : 1;
        // 검색 조건과 정렬 조건 받아오기 (쿼리 스트링에서 배열 형태로 받아옴)
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
        $filters = isset($_GET['filter']) ? $_GET['filter'] : [];
        $sort = isset($_GET['sort']) ? $_GET['sort'] : [];


        $memberData = $this->membersService->getMemberListData($currentPage, $page_rows, $searchQuery, $filters, $sort);
        $totalItems = $this->membersService->getTotalMemberCount($searchQuery, $filters);

        // 페이징 데이터 계산
        $paginationData = [
            'totalItems' => $totalItems,
            'currentPage' => $currentPage,
            'totalPages' => ceil($totalItems / $page_rows),
            'itemsPerPage' => $page_rows,
            'pageNums' => $page_nums,
            'searchQuery' => $searchQuery, // 검색 조건을 페이징 URL에 포함
            'filters' => $filters,
            'sort' => $sort,
        ];

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 관리',
            'content' => '',
            'levelData' => $this->getLevelData(),
            'memberData' => $memberData,
            'paginationData' => $paginationData, // 페이징 데이터 추가
            'searchQuery' => $searchQuery, // 검색어를 뷰에 전달
            'filters' => $filters,
            'sort' => $sort,
        ];

        return ['Members/list', $viewData];
    }
}
