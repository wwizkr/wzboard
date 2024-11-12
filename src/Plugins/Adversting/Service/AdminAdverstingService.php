<?php
//파일위치 src/Plugins/adversting/Service/AdminTrialService.php

namespace Plugins\adversting\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;

use Plugins\Adversting\Model\AdminAdverstingModel;


class AdminAdverstingService
{
    protected $container;
    protected $config_domain;
    protected $formDataMiddleware;
    protected $authMiddleware;
    protected $membersService;

    protected $adminAdverstingModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->authMiddleware = $this->container->get('AuthMiddleware');
        $this->membersService = $this->container->get('MembersService');
        
        $this->adminAdverstingModel = new AdminAdverstingModel($this->container);
    }

    public function getProgramList()
    {
        $config = []; // 차후 삭제해야 함.

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['companyName'];
        $allowedSortFields = ['no', 'status', 'create_at'];

        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            /*
             * $_GET['searchData'] 는 목록에서 셀렉트박스 검색
             */
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 프로그램 수
        $totalItems = $this->getTotalProgramCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 목록 데이터 조회
        $programData = $this->adminAdverstingModel->getProgramListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );
        
        $programList = [];
        foreach ($programData as $key => $program) {
            $programList[$key] = $program;
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'programList' => $programList,
        ];
    }

    public function getProgramListAll(int $status = 0)
    {
        // 회원 목록 데이터 조회
        $programData = $this->adminAdverstingModel->getProgramListAll($status);
        
        $programList = [];
        foreach ($programData as $key => $program) {
            $programList[$program['no']] = $program;
        }

        return $programList;
    }

    public function getTotalProgramCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->adminAdverstingModel->getTotalProgramCount($searchQuery, $filters, $additionalQueries);
    }


    public function getProgramDataByNo(int $no)
    {
        $result = $this->adminAdverstingModel->getProgramDataByNo($no);

        return $result[0] ?? [];
    }

    public function programUpdate(int $no)
    {
        if ($no) {
            $programData = $this->getProgramDataByNo($no);

            if (empty($programData)) {
                return [
                    'result' => 'failure',
                    'message' => '프로그램 정보를 찾을 수 없습니다.',
                ];
            }
        }

        // 폼데이터 정리
        $formData = isset($_POST['formData']) ? $_POST['formData'] : [];

        $numericFields = ['supplyPrice', 'marketPrice', 'operateUnit'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        $result = $this->adminAdverstingModel->programUpdate((int)$no, $data);

        return $result;
    }

    public function programDelete(int $no)
    {
        $programData = $this->getProgramDataByNo($no);

        if (empty($itemData)) {
            return [
                'result' => 'failure',
                'message' => '상품 정보를 찾을 수 없습니다.',
            ];
        }

        $result = $this->adminAdverstingModel->programDelete($no);
    }
    //---- Program -------- END-----------//

    //---- Item -------- START-----------//

    public function getItemList()
    {
        // 종료일이 지난 상품 종료 처리
        $this->adminAdverstingModel->clearItemListStaus();

        $config = []; // 차후 삭제해야 함.

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['storeName', 'sellerId'];
        $allowedSortFields = ['no', 'create_at'];

        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            /*
             * $_GET['searchData'] 는 목록에서 셀렉트박스 검색
             */
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 프로그램 수
        $totalItems = $this->getTotalItemCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 회원 목록 데이터 조회
        $itemData = $this->adminAdverstingModel->getItemListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );
        
        $itemList = [];
        foreach ($itemData as $key => $item) {
            $itemList[$key] = $item;
            $itemList[$key]['program'] = $this->getProgramDataByNo($item['programNo']);
            $itemList[$key]['manager'] = $this->membersService->getMemberDataById($item['managerId']);
            $itemList[$key]['seller']  = $this->membersService->getMemberDataById($item['sellerId']);
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'itemList' => $itemList,
        ];
    }

    public function getTotalItemCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->adminAdverstingModel->getTotalItemCount($searchQuery, $filters, $additionalQueries);
    }

    public function getItemDataByNo(int $no)
    {
        $result = $this->adminAdverstingModel->getItemDataByNo($no);
        $item = $result[0] ?? [];
        
        if (empty($item)) {
            return $item;
        }

        // 권한 체크 로직 추가할 것
        $authUser = $this->authMiddleware->getAuthUser();
        if (!$authUser['is_super']) {
            $authCfClass = '/'.$authUser['member_data']['cf_class'].'/';
            $itemCfClass = '/'.$item['cf_class'].'/';

            // 권한이 없는 경우 빈 배열 반환
            if (strpos($itemCfClass, $authCfClass) !== 0) {
                return [];
            }
        }

        return $item;
    }

    public function checkSellerId(string $sellerId, int $sellerLevel)
    {
        $result = $this->adminAdverstingModel->checkSellerId($sellerId, $sellerLevel);
        
        $seller = $result[0] ?? [];

        if (empty($seller)) {
            return [
                'result' => 'failure',
                'message' => '판매자 정보를 찾을 수 없습니다.',
                'data' => [],
            ];
        }

        unset($seller['password']);

        return [
            'result' => 'success',
            'message' => '판매자 정보를 확인하였습니다.',
            'data' => $seller,
        ];
    }

    public function searchNaverShopRank(array $wzApiInfo, array $naverApiInfo, array $data, string $mode = null)
    {
        $itemNo = isset($data['itemNo']) ? CommonHelper::pickNumber($data['itemNo']) : 0;
        $storeName = CommonHelper::validateParam('storeName', 'string', '', $data['storeName'], null);
        $itemCode = CommonHelper::validateParam('itemCode', 'string', '', $data['itemCode'], null);
        $matchCode = CommonHelper::validateParam('matchCode', 'string', '', $data['matchCode'], null);
        $searchKeyword = CommonHelper::validateParam('searchKeyword', 'string', '', $data['searchKeyword'], null);
        $oQuery  = CommonHelper::validateParam('oQuery', 'string', '', $data['oQuery'], null);
        $adQuery  = CommonHelper::validateParam('adQuery', 'string', '', $data['adQuery'], null);
        $clientId = $naverApiInfo['clientId'];
        $clientSecret = $naverApiInfo['clientSecret'];
        
        $message = [];
        $processed = true;

        if (!$storeName) {
            $message[] = '쇼핑몰명은 필수입니다.';
            $processed = false;
        }

        if (!$itemCode) {
            $message[] = '상품코드는 필수 입니다.';
            $processed = false;
        }

        if (!$searchKeyword) {
            $message[] = '기본 검색어는 필수 입니다.';
            $processed = false;
        }
        
        if (!$clientId) {
            $message[] = '네이버 클라이언트 ID는 필수입니다.';
            $processed = false;
        }

        if (!$clientSecret) {
            $message[] = '네이버 클라이언트 Secret는 필수입니다.';
            $processed = false;
        }

        if ($processed === false) {
            return [
                'result' => 'failure',
                'message' => implode(",", $message),
                'date' => [],
            ];
        }
        
        $url = $wzApiInfo['url'].'/'.$wzApiInfo['ver'].'/naverShop/searchRank';
        $method = 'POST';
        $data = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'storeName' => $storeName,
            'itemCode' => $itemCode,
            'matchCode' => $matchCode,
            'searchKeyword' => $searchKeyword ,
            'oQuery' => $oQuery,
            'adQuery' => $adQuery,
        ];
        $headers = [];

        $response = CommonHelper::sendCurlRequest($url, $method, $data, $headers);
                
        if ($response['success'] === true) { // 실제 응답값
            $ret = json_decode($response['response'],true);

            if ($mode === 'update' && $ret['data']['rank'] > 0) {
                $itemData = $this->getItemDataByNo($itemNo);

                $rankingHistory = isset($itemData['rankingHistory']) && $itemData['rankingHistory'] ? $itemData['rankingHistory'].'-'.$ret['data']['rank'] : $ret['data']['rank'];
                
                $param = [
                    'updateRanking' => ['i', $ret['data']['rank']],
                    'rankingHistory' => ['s', $rankingHistory],
                ];
                $updated = $this->adminAdverstingModel->itemUpdate((int)$itemData['no'], $param);
                
                $ranking = $ret['data']['rank'];
                $params = [
                    'cf_class' => ['s', $itemData['cf_class']],
                    'programNo' => ['i', $itemData['programNo']],
                    'itemNo' => ['i', $itemData['no']],
                    'managerId' => ['s', $itemData['managerId']],
                    'sellerId' => ['s', $itemData['sellerId']],
                    'ranking' => ['i', $ranking],
                ];
                $rankingInsert = $this->adminAdverstingModel->insertItemRankingHistoryData($params);
            }
            
            $result = [
                'result' => 'success',
                'rank' => $ret['data']['rank'],
            ];
        }

        return $result;
    }

    public function itemUpdate(int $no)
    {
        // 기간 연장 여부 
        $period = CommonHelper::validateparam('period', 'string', null, null, INPUT_POST);

        if ($no) {
            $itemData = $this->getItemDataByNo($no);

            if (empty($itemData)) {
                return [
                    'result' => 'failure',
                    'message' => '광고 상품 정보를 찾을 수 없습니다.',
                ];
            }
        }

        // 폼데이터 정리
        $formData = isset($_POST['formData']) ? $_POST['formData'] : [];

        //start_at, extension_at, close_at Time 설정.
        $formData['start_at'] .= ' 00:00:00';
        $formData['extension_at'] .= ' 00:00:00';
        $formData['close_at'] .= ' 23:59:59';
        
        // 운영기간 history 설정
        $periodHistory = isset($itemData['periodHistory']) && $itemData['periodHistory'] ? $itemData['periodHistory'].'-'.$formData['slotPeriod'] : $formData['slotPeriod'];
        $rankingHistory = '';
        // 등록일 경우 시작순위만.
        if (!$no) {
            $rankingHistory = (string)$formData['startRanking'];
        }
        
        if ($period === 'extend') {
            $rankingHistory = isset($itemData['rankingHistory']) && $itemData['rankingHistory'] ? $itemData['rankingHistory'].'-'.$formData['updateRanking'] : $formData['updateRanking'];
        }

        $authUser = $this->authMiddleware->getAuthUser();
        $seller = $this->membersService->getMemberDataById($formData['sellerId']);
        $cf_class = $authUser['cf_class'].'/'.$seller['mb_no'];

        $numericFields = ['programNo', 'startRanking', 'updateRanking', 'slotCount', 'slotPeriod', 'flowCount', 'status'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);
        $data['periodHistory'] = ['s', $periodHistory];

        if (!$no || $period === 'extend') {
            $data['rankingHistory'] = ['s', $rankingHistory]; // rankingHistory
        }
        
        // 신규 등록의 경우에만 추가
        if (!$no) {
            $data['managerId'] = ['s', $authUser['mb_id']];
            $data['managerLevel'] = ['i', $authUser['member_level']];
            $data['cf_class'] = ['s', $cf_class]; // cf_class 정보
            $data['staffId'] = ['s', $authUser['mb_id']];
        } else { // 업데이트
            unset($data['startRanking']);
        }

        $result = $this->adminAdverstingModel->itemUpdate((int)$no, $data);

        if ($result['itemNo']) {
            $itemData = $this->getItemDataByNo($result['itemNo']);

            //신규 등록 또는 연장일 경우 item history 등록
            if (!$no || $period === 'extend') {
                $cost = 0;
                $orderType = $period === 'extend' ? 2 : 1;
                $params = [
                    'cf_class' => ['s', $itemData['cf_class']],
                    'managerId' => ['s', $itemData['managerId']],
                    'sellerId' => ['s', $itemData['sellerId']],
                    'programNo' => ['i', $itemData['programNo']],
                    'itemNo' => ['i', $itemData['no']],
                    'slotCount' => ['i', $itemData['slotCount']],
                    'period' => ['i', $itemData['slotPeriod']],
                    'cost' => ['i', $cost],
                    'orderType' => ['i', $orderType],
                ];
                
                $historyInsert = $this->adminAdverstingModel->insertItemHistoryData($params);
                unset($params);
                
                $ranking = $period === 'extend' ? $itemData['updateRanking'] : $itemData['startRanking'];
                $params = [
                    'cf_class' => ['s', $itemData['cf_class']],
                    'programNo' => ['i', $itemData['programNo']],
                    'itemNo' => ['i', $itemData['no']],
                    'managerId' => ['s', $itemData['managerId']],
                    'sellerId' => ['s', $itemData['sellerId']],
                    'ranking' => ['i', $ranking],
                ];
                $rankingInsert = $this->adminAdverstingModel->insertItemRankingHistoryData($params);
            }
        }

        return $result;
    }

    public function viewPeriodHistory(array $data)
    {
        $data = CommonHelper::getJsonInput();
        
        $programNo = isset($data['programNo']) ? CommonHelper::pickNumber($data['programNo']) : 0;
        $itemNo = isset($data['itemNo']) ? CommonHelper::pickNumber($data['itemNo']) : 0;

        $list = $this->adminAdverstingModel->viewPeriodHistory((int)$programNo, (int)$itemNo);

        $listData = [];
        foreach($list as $key => $val) {
            $listData[$key] = $val;
            $listData[$key]['item'] = $this->getItemDataByNo($val['itemNo']);
            $listData[$key]['orderType'] = $val['orderType'] == 1 ? '신규등록' : '기간연장';
            $listData[$key]['created_at'] = substr($val['created_at'], 0, 10);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [
                'list' => $listData,
            ],
        ]);
    }

    public function viewRankingHistory(array $data)
    {
        $data = CommonHelper::getJsonInput();
        
        $programNo = isset($data['programNo']) ? CommonHelper::pickNumber($data['programNo']) : 0;
        $itemNo = isset($data['itemNo']) ? CommonHelper::pickNumber($data['itemNo']) : 0;

        $list = $this->adminAdverstingModel->viewRankingHistory((int)$programNo, (int)$itemNo);

        $listData = [];
        foreach($list as $key => $val) {
            $listData[$key] = $val;
            $listData[$key]['item'] = $this->getItemDataByNo($val['itemNo']);
        }

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '',
            'data' => [
                'list' => $listData,
            ],
        ]);
    }

    public function itemDelete(int $no)
    {
        $itemData = $this->getItemDataByNo($no);

        if (empty($itemData)) {
            return [
                'result' => 'failure',
                'message' => '해당 상품에 대한 권한이 없습니다.',
            ];
        }

        $result = $this->adminAdverstingModel->itemDelete($no);
    }

    //---- Item -------- END-----------//

    //---- Period -------- START-----------//

    public function getPeriodList()
    {
        $config = [];
        
        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = [$_ENV['DB_TABLE_PREFIX'].'adversting_items.storeName', $_ENV['DB_TABLE_PREFIX'].'adversting_items.itemName'];
        $allowedSortFields = [$_ENV['DB_TABLE_PREFIX'].'adversting_items_history.no', $_ENV['DB_TABLE_PREFIX'].'adversting_items_history.create_at'];

        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            /*
             * $_GET['searchData'] 는 목록에서 셀렉트박스 검색
             */
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 프로그램 수
        $totalItems = $this->getTotalPeriodCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 목록 데이터 조회
        $periodData = $this->adminAdverstingModel->getPeriodListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );
        
        $periodList = [];
        foreach ($periodData as $key => $item) {
            $periodList[$key] = $item;
            $periodList[$key]['program'] = $this->getProgramDataByNo($item['programNo']);
            $periodList[$key]['manager'] = $this->membersService->getMemberDataById($item['managerId']);
            $periodList[$key]['seller']  = $this->membersService->getMemberDataById($item['sellerId']);
            $periodList[$key]['orderType'] = $item['orderType'] == 1 ? '신규등록' : '기간연장';
            $periodList[$key]['period_at'] = substr($item['period_at'], 0, 10);
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'periodList' => $periodList,
        ];
    }

    public function getTotalPeriodCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->adminAdverstingModel->getTotalPeriodCount($searchQuery, $filters, $additionalQueries);
    }

    //---- Period -------- END-----------//
}