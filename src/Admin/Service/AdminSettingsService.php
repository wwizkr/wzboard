<?php
//파일위치 src/Service/AdminSettingService.php

namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Model\AdminSettingsModel;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Helper\MenuHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminCommonHelper;

class AdminSettingsService
{
    protected $container;
    protected $config_domain;
    protected $adminSettingsModel;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminSettingsModel = new AdminSettingsModel($this->container);

        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
    }

    /**
     * 새로운 메뉴 데이터를 삽입합니다.
     *
     * @param string $type 메뉴 유형
     * @param array $data 삽입할 메뉴 데이터
     * @return bool 삽입 성공 여부
     */
    public function insertMenuData($type, array $data)
    {
        $cf_id = $data['cf_id'][1];
        $me_code = $data['me_code'][1];
        $me_depth = $data['me_depth'][1];
        $me_name = $data['me_name'][1];

        // 메뉴 코드 생성
        $result = $this->generateMenuCode($cf_id, $me_code, $me_depth);
        // 메뉴 순서 생성
        $me_order = $this->adminSettingsModel->setMenuOrder($cf_id);

        // 삽입할 메뉴 데이터 배열 생성
        $menuData = [
            'cf_id' => ['i', $cf_id],
            'me_code' => ['s', $result['me_code']],
            'me_parent' => ['i', $result['me_parent']],
            'me_depth' => ['i', $result['me_depth']],
            'me_code' => ['s', $result['me_code']],
            'me_order' => ['i', $me_order],
            'me_name' => ['s', $me_name],
        ];

        // 실제로 메뉴 데이터를 데이터베이스에 삽입하는 로직
        $insert = $this->adminSettingsModel->insertMenu($menuData);

        return $this->adminSettingsModel->getMenuByCode($cf_id, $result['me_code']);
    }

    /**
     * 메뉴 코드를 생성합니다.
     *
     * @param int $cf_id 설정 ID
     * @param string $me_code 기존 메뉴 코드 (없을 수도 있음)
     * @param int $me_depth 메뉴 깊이 (1단계 또는 하위 메뉴)
     * @return string 생성된 메뉴 코드
     */
    private function generateMenuCode($cf_id, $me_code = '', $me_depth = 1)
    {
        if ($me_depth == 1) {
            // 1단계 메뉴 코드 생성
            $code = $this->adminSettingsModel->getMaxMenuCode($cf_id, 1);
            $me_code = $code ? (int)substr($code, 0, WZ_CATEGORY_LENGTH) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
            $depth = $me_depth;
            $parent = 0;
        } else {
            // 하위 메뉴 코드 생성
            $tmp = $this->adminSettingsModel->getMenuByCode($cf_id, $me_code);
            $depth = (int)$tmp['me_depth'] + 1;
            $cut = (int)($tmp['me_depth'] * WZ_CATEGORY_LENGTH);
            $parent = (int)$tmp['no'];

            $code = $this->adminSettingsModel->getMaxSubMenuCode($cf_id, $tmp['me_code'], $depth);
            $me_code = $code ? (int)substr($code, $cut, WZ_CATEGORY_LENGTH) : 0;
            $me_code = sprintf("%03d", $me_code + 1);
            $me_code = $tmp['me_code'] . $me_code;
        }

        $responsData = [
            'me_code' => $me_code,
            'me_depth' => $depth,
            'me_parent' => $parent,
        ];

        return $responsData;
    }

    /**
     * 메뉴 정보를 업데이트 합니다.
     *
     * @param int $cf_id
     * @param int $no
     * @param string $me_code
     * @return boolean
     */
    public function updateMenuData($cf_id, $no, $me_code, $data)
    {
        $cacheHelper = $this->container->get('CacheHelper');
        $result = $this->adminSettingsModel->updateMenuData($cf_id, $no, $me_code, $data);
        
        // 메뉴 수정 후 캐시 초기화
        $cacheKey = 'menu_cache_' . $this->config_domain['cf_domain'];
        $cacheHelper->setCache($cacheKey, null);

        return $this->adminSettingsModel->getMenuByCode($cf_id, $me_code);
    }

    /**
     * 메뉴 삭제
     */
    public function menuDelete($data)
    {
        $cacheHelper = $this->container->get('CacheHelper');
        $numericFields = ['cf_id', 'no'];
        $whereData = $this->formDataMiddleware->processFormData($data, $numericFields);

        $whereData['me_code'] = ['s', $data['me_code'], 'and', 'like_right'];

        $result = $this->adminSettingsModel->menuDelete($whereData);

        if ($result['result'] === 'success') {
            $cacheKey = 'menu_cache_' . $this->config_domain['cf_domain'];
            $cacheHelper->setCache($cacheKey, null);
        }

        return $result;
    }

    /**
     * 메뉴 순서 업데이트
     */
    public function updateMenuOrder($menuData)
    {
        $cacheHelper = $this->container->get('CacheHelper');

        $result = $this->adminSettingsModel->updateMenuOrder($menuData);

        if ($result) {
            $cacheKey = 'menu_cache_' . $this->config_domain['cf_domain'];
            error_log("CacheKey:::".print_r($cacheKey, true));
            $cacheHelper->setCache($cacheKey, null);
        }

        return $result;
    }

    public function getClauseList()
    {
        // 기본 설정 로드
        $config = [
            'cf_page_rows' => isset($_GET['pagenum']) && $_GET['pagenum'] > 0 ? CommonHelper::pickNumber($_GET['pagenum']) : $this->config_domain['cf_page_rows'],
            'cf_page_nums' => $this->config_domain['cf_page_nums']
        ];

        $configProvider = $this->container->get('ConfigProvider');
        $clauseType = $configProvider->get('clauseType');
        $clauseKind = $configProvider->get('clauseKind');

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = [];
        $allowedSortFields = ['ct_id',];

        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                if ($key === 'ct_page_type') {
                    $allowed = !empty($clauseType) ? array_keys($clauseType) : []; // $clauseType이 정의되지 않았을 경우를 대비
                }

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 약관수
        $totalItems = $this->getTotalClauseCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 약관 목록 데이터 조회
        $clauseData = $this->adminSettingsModel->getClauseListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );
        
        $clauseList = [];
        foreach($clauseData as $key => $val) {
            $clauseList[$key] = $val;
            $pageType = $val['ct_page_type'] ? explode(",", $val['ct_page_type']) : [];
            $type = [];
            foreach($pageType as $index => $page) {
                $type[] = $clauseType[$page] ?? '';
            }
            $clauseList[$key]['ct_page_type'] = $type;
            $clauseList[$key]['kindSelect'] = CommonHelper::makeSelectBox('listData[ct_kind]['.$key.']', $clauseKind ?? [], $val['ct_kind'] ?? '', 'ct_kind_'.$key, 'frm_input frm_full', '선택');
            $clauseList[$key]['useSelect'] = CommonHelper::makeSelectBox('listData[ct_use]['.$key.']', [1=>'사용', 2=>'사용안함'], $val['ct_use'] ?? '', 'ct_use_'.$key, 'frm_input frm_full', '선택');
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'clauseList' => $clauseList,
        ];
    }

    public function getTotalClauseCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->adminSettingsModel->getTotalClauseCount($searchQuery, $filters, $additionalQueries);
    }

    public function getClauseDataById(int $ctId = null)
    {
        $result = $this->adminSettingsModel->getClauseDataById($ctId, $this->config_domain['cf_id']);
        return $result;
    }

    public function clauseItemUpdate($ctId)
    {
        if ($ctId) {
            $clause = $this->adminSettingsModel->getClauseDataById($ctId, $this->config_domain['cf_id']);
            if ($clause['ct_id'] === '') {
                return [
                    'result' => 'failure',
                    'message' => '약관정보를 찾을 수 없습니다.',
                    'data' => [],
                ];
            }
        }

        // POST 데이터는 formData 배열로 전송 됨
        $formData = $_POST['formData'] ?? null;
        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력 정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        // 이미지 저장 디렉토리
        $storagePath = "/storage/editor/";
        
        $content = $formData['ct_content'];
        unset($formData['ct_content']);
        $content = CommonHelper::updateStorageImages($content, $storagePath);
        $pageType = isset($formData['ct_page_type']) && $formData['ct_page_type'] ? implode(",", $formData['ct_page_type']) : '';
        unset($formData['ct_page_type']);

        $formData['ct_content'] = $content;
        $formData['ct_page_type'] = $pageType;

        $numericFields = ['ct_kind', 'ct_order', 'ct_use'];
        $data = $this->formDataMiddleware->processFormData($formData, $numericFields);

        return $this->adminSettingsModel->clauseItemUpdate($this->config_domain['cf_id'], $ctId, $data);
    }

    public function clauseItemDelete($ctId)
    {
        return $this->adminSettingsModel->clauseItemDelete($ctId, $this->config_domain['cf_id']);
    }
}