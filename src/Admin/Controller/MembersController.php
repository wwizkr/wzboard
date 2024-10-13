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

//use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class MembersController
{
    protected $container;
    protected $membersService;
    protected $config_domain;
    protected $cf_id;
    protected $formDataMiddleware;
    protected $componentsViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->cf_id = $this->config_domain['cf_id'];
        $this->membersService = $this->container->get('MembersService');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    protected function getLevelData()
    {
        return $this->membersService->getMemberLevelData();
    }

    public function list($vars)
    {
        $memberData = $this->membersService->getMemberList();
        
        $params = $memberData['params'];
        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $memberData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 관리',
            'content' => '',
            'levelData' => $this->getLevelData(),
            'memberData' => $memberData['memberList'],
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => 'Members/list',
            'viewData' => $viewData,
        ];
    }

    public function memberListModify()
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }

    public function memberListDelete()
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }
}
