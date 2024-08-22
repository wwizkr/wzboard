<?php
// 파일 위치: /src/Admin/Controller/BoardController.php

namespace Web\Admin\Controller;

use Web\Admin\Helper\BoardsHelper;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Model\MembersModel;
use Web\PublicHtml\Service\MembersService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

/*
 * BoardsController가 게시판 관련 모든 처리를 담당하므로 메서드명은 게시판 아이디로 사용할 수 없음.
 * group, groupUpdata, 
*/

class BoardsController
{
    protected $container;
    protected $boardsModel;
    protected $boardsService; 
    protected $membersModel;
    protected $membersService;
    protected $configDomain;
    protected $levelData;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $boardsModel = new BoardsModel($container);
        $this->boardsService = new BoardsService($boardsModel);
        $membersModel = new MembersModel($container);
        $this->membersService = new MembersService($membersModel);
        $this->configDomain = $container->get('config_domain');
        $this->levelData = $this->membersService->getMemberLevelData();
    }

    // ---------------------------
    // 그룹 관리 메서드
    // ---------------------------

    /**
     * 게시판 그룹 목록을 가져와서 화면에 출력.
     */
    public function group()
    {
        // 그룹 목록
        $group = '';
        $groupData = $this->boardsService->getBoardsGroup($group);

        $viewData = [
            'title' => '게시판 그룹 관리',
            'content' => 'This is the general settings.',
            'config_domain' => $this->configDomain,
            'groupData' => $groupData,
            'levelData' => $this->levelData,
        ];

        return ['Boards/group', $viewData];
    }
    
    /**
     * 게시판 그룹 정보를 업데이트.
     * 업데이트할 그룹 ID와 폼 데이터를 받아 처리함.
     */
    public function groupUpdate()
    {
        $action = $_POST['action'] ?? null;
        $group_no = CommonHelper::pickNumber($_POST['group_no'],0) ?? 0;
        $formData = $_POST['formData'] ?? null;
        
        if(empty($formData)) {
            $jsonData = [
                'result' => 'failer',
                'message' => '입력정보가 비어 있습니다.'
            ];
            header('Content-Type: application/json');
            die(json_encode($jsonData));
        }

        $i = ['allow_level','order_num']; // $i 배열에는 숫자형으로 처리할 필드
        $data = CommonHelper::processFormData($formData, $i);

        if($action == 'update') {
            $result = $this->boardsService->updateBoardsGroup($group_no, $data);
        } else {
            $result = $this->boardsService->insertBoardsGroup($data);
        }

        $jsonData = [
            'result' => 'success',
            'message' => '처리하였습니다.'
        ];
        header('Content-Type: application/json');
        die(json_encode($jsonData));
    }

    // ---------------------------
    // 카테고리 관리 메서드
    // ---------------------------

    /**
     * 게시판 카테고리 목록을 가져와서 화면에 출력.
     * 카테고리를 개별적으로 관리. 게시판 설정에 매칭. 동일 카테고리를 여러개의 게시판에서 사용할 수 있음.
     */
    public function category()
    {
        // 카테고리 목록
        $categoryData = $this->boardsService->getBoardsCategory();

        $viewData = [
            'title' => '게시판 카테고리 관리',
            'content' => 'This is the general settings.',
            'config_domain' => $this->configDomain,
            'categoryData' => $categoryData,
            'levelData' => $this->levelData,
        ];

        return ['Boards/category', $viewData];
    }
}
