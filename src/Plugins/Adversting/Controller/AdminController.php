<?php
/**
 * 광고 관리 관리자 컨트롤러
 * 
 */

namespace Plugins\Adversting\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

use Plugins\Adversting\Config;
use Plugins\Adversting\Service\AdminAdverstingService;
use Plugins\Adversting\Model\AdminAdverstingModel;

class AdminController
{
    protected $container;
    protected $config_domain;
    protected $viewRenderer;
    protected $adminViewRenderer;
    protected $componentsViewHelper;

    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $formDataMiddleware;
    protected $authMiddleware;
    
    protected $config;
    protected $adminAdverstingService;
    protected $adminAdverstingModel;

    /**
     * AdminController 생성자
     * 
     * @param DependencyContainer $container 의존성 컨테이너
     */
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->initializeServices();
        $this->viewRenderer = $this->container->get('ViewRenderer');
        $this->adminViewRenderer = $this->container->get('AdminViewRenderer');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    
    protected function initializeServices()
    {
        $this->membersModel = $this->container->get('MembersModel');
        $this->membersService = $this->container->get('MembersService');
        $this->membersHelper = $this->container->get('MembersHelper');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->authMiddleware = $this->container->get('AuthMiddleware');
        
        $this->config = new Config();
        $this->adminAdverstingService = new AdminAdverstingService($this->container);
        $this->adminAdverstingModel = new AdminAdverstingModel($this->container);
    }
    
    protected function setAssets(): void
    {
        $this->adminViewRenderer->addAsset('css', '/assets/js/lib/jquery/jquery-ui.css');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery/jquery-3.7.1.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery/jquery-migrate-3.5.0.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery/jquery-ui.min.js');
    }

    //---- Program -------- START-----------//
    public function programList($vars)
    {
        $this->authMiddleware->checkAdminAuth('r');

        $programData = $this->adminAdverstingService->getProgramList();

        $params = $programData['params'];

        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $programData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].'&'.$queryString;

        $viewData = [
            'title' => '광고 프로그램 관리',
            'totalItems' => $programData['totalItems'],
            'programList' => $programData['programList'],
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/programList',
            'viewData' => $viewData,
        ];
    }

    public function programForm($vars)
    {
        $this->authMiddleware->checkAdminAuth('r');

        $no = $vars['param'] ?? 0;
        
        $programData = $no ? $this->adminAdverstingService->getProgramDataByNo($no) : [];

        $queryString = CommonHelper::getQueryString();

        $viewData = [
            'title' => '광고 프로그램 '.(isset($programData['companyName']) ? $programData['companyName'].' 수정' : '등록'),
            'config_domain' => $this->config_domain,
            'programData' => $programData,
            'queryString' => $queryString,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/programForm',
            'viewData' => $viewData,
        ];
    }

    public function programUpdate($vars)
    {
        $this->authMiddleware->checkAdminAuth('w');

        $no = isset($_POST['programNo']) ? commonHelper::pickNumber($_POST['programNo']) : 0;
        
        $result = $this->adminAdverstingService->programUpdate($no);

        return CommonHelper::jsonResponse([
            'result' => $result['result'],
            'message' => '',
            'data' => $result,
        ]);
    }

    public function programDelete()
    {
        $data = CommonHelper::getJsonInput();
        $no = CommonHelper::validateParam('no', 'int', '', $data['no'], null);
        $activeCode = CommonHelper::validateParam('activeCode', 'string', '', $data['activeCode'], null);

        // 권한 체크
        $this->authMiddleware->checkAdminAuth('d', $activeCode);

        $result = $this->adminAdverstingService->programDelete($no);

        return CommonHelper::jsonResponse($result);
    }

    //---- Program -------- END-----------//
    
    //---- Item -------- START-----------//
    public function itemList()
    {
        $this->authMiddleware->checkAdminAuth('r');

        $authUser = $this->authMiddleware->getAuthUser();
        
        $listData = $this->adminAdverstingService->getitemList();

        $params = $listData['params'];

        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $listData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].'&'.$queryString;

        $viewData = [
            'title' => '광고 상품 관리',
            'config_domain' => $this->config_domain,
            'totalItems' => $listData['totalItems'],
            'itemList' => $listData['itemList'],
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/itemList',
            'viewData' => $viewData,
        ];
    }

    public function itemForm($vars)
    {
        $this->authMiddleware->checkAdminAuth('r');

        $this->setAssets();

        $no = $vars['param'] ?? 0;
        
        $storeConfig = [
            'storeType' => $this->config->getStoreType(),
            'storeKind' => $this->config->getStoreKind(),
        ];

        $programList = $this->adminAdverstingService->getProgramListAll();
        
        $itemData = $no ? $this->adminAdverstingService->getItemDataByNo($no) : [];

        $queryString = CommonHelper::getQueryString();

        $viewData = [
            'title' => '광고 상품 '.(!empty($itemData) ? ' 수정' : '등록'),
            'config_domain' => $this->config_domain,
            'storeConfig' => $storeConfig,
            'programList' => $programList,
            'itemData' => $itemData,
            'queryString' => $queryString,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/itemForm',
            'viewData' => $viewData,
        ];
    }

    public function checkSellerId()
    {
        $data = CommonHelper::getJsonInput();

        $adverstingLevel = $this->config->getAdverstingLevel();

        $sellerId = CommonHelper::validateParam('sellerId', 'string', '', $data['sellerId'], null);

        if (!$sellerId) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '판매자 아이디가 입력되지 않았습니다.',
                'data' => [],
            ]);
        }

        $result = $this->adminAdverstingService->checkSellerId($sellerId, (int)$adverstingLevel['seller']);

        return CommonHelper::jsonResponse($result);
    }

    public function searchNaverShopRank($vars)
    {
        $wzApiInfo = $this->config->getWzApiInfo();
        $naverApiInfo = $this->config->getNaverShopApiInfo();
        $data = CommonHelper::getJsonInput();
        $mode = $vars['param'] ?? '';

        $res = $this->adminAdverstingService->searchNaverShopRank($wzApiInfo, $naverApiInfo, $data, $mode);
        
        if ($res['result'] === 'success') {
            $result = [
                'result' => 'success',
                'message' => '',
                'data' => [
                    'rank' => $res['rank'],
                    'mode' => $mode,
                ],
            ];
        } else {
            $result = [
                'result' => 'failure',
                'message' => '순위검색 오류. 다시 시도해 주세요.',
                'data' => [],
            ];
        }

        return CommonHelper::jsonResponse($result);
    }

    public function itemUpdate()
    {
        $this->authMiddleware->checkAdminAuth('w');

        $no = isset($_POST['itemNo']) ? CommonHelper::pickNumber($_POST['itemNo']) : 0;
        
        $result = $this->adminAdverstingService->itemUpdate($no);

        return CommonHelper::jsonResponse([
            'result' => $result['result'],
            'message' => $no ? '상품을 수정하였습니다.' : '상품을 등록하였습니다.',
            'data' => $result,
        ]);
    }

    public function viewPeriodHistory()
    {
        $data = CommonHelper::getJsonInput();

        $listData = $this->adminAdverstingService->viewPeriodHistory($data);

        return CommonHelper::jsonResponse([
            'result' => $result['result'],
            'message' => '',
            'data' => [
                'list' => $listData,
            ],
        ]);
    }

    public function viewRankingHistory()
    {
        $data = CommonHelper::getJsonInput();

        $listData = $this->adminAdverstingService->viewRankingHistory($data);

        return CommonHelper::jsonResponse([
            'result' => $result['result'],
            'message' => '',
            'data' => [
                'list' => $listData,
            ],
        ]);
    }

    public function itemDelete()
    {
        $data = CommonHelper::getJsonInput();
        $no = CommonHelper::validateParam('no', 'int', '', $data['no'], null);
        $activeCode = CommonHelper::validateParam('activeCode', 'string', '', $data['activeCode'], null);

        // 권한 체크
        $this->authMiddleware->checkAdminAuth('d', $activeCode);

        $result = $this->adminAdverstingService->itemDelete($no);

        return CommonHelper::jsonResponse($result);
    }
    //---- Item -------- END-----------//

    //---- Period -------- START-----------//

    public function periodList()
    {
        $this->authMiddleware->checkAdminAuth('r');

        $periodData = $this->adminAdverstingService->getPeriodList();

        $params = $periodData['params'];

        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $periodData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].'&'.$queryString;

        $viewData = [
            'title' => '광고 상품 집행 목록',
            'totalItems' => $periodData['totalItems'],
            'periodList' => $periodData['periodList'],
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => WZ_SRC_PATH.'/Plugins/Adversting/View/admin/periodList',
            'viewData' => $viewData,
        ];
    }

    //---- Period -------- END-----------//
}