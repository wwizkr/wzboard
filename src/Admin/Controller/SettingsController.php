<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;

use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminCommonHelper;
use Web\Admin\Model\AdminSettingsModel;
use Web\Admin\Service\AdminSettingsService;
use Web\Admin\Helper\AdminMenuHelper;
use Web\PublicHtml\Helper\MenuHelper;

class SettingsController
{
    protected DependencyContainer $container;
    protected $adminSettingsModel;
    protected $adminSettingsService;
    protected $adminMenuHelper;
    protected $formDataMiddleware;
    protected $menuHelper;
    protected $config_domain;
    protected $cf_id;
    protected $configProvider;
    protected $componentsViewHelper;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->cf_id = (int)$this->config_domain['cf_id'];
        $this->configProvider = $this->container->get('ConfigProvider');

        $this->adminSettingsModel = new AdminSettingsModel($this->container);
        $this->adminSettingsService = new AdminSettingsService($this->container);
        $this->adminMenuHelper = new AdminMenuHelper($this->container);
        $this->menuHelper = new MenuHelper();
        
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->componentsViewHelper = $this->container->get('ComponentsViewHelper');
    }

    /**
     * 메뉴설정 페이지 표시
     *
     * @return array
     */
    public function menus(): array
    {
        $menuDatas = $this->container->get('menu_datas') ?? null;
        $menuCategory = $this->adminMenuHelper->setMenuCategory();
        
        $viewData = [
            'title' => '메뉴 설정',
            'content' => '',
            'menuDatas' => $menuDatas,
            'menuCategory' => $menuCategory,
        ];

        return [
            'viewPath' => 'Settings/menus',
            'viewData' => $viewData,
        ];
    }

    /**
     * 메뉴 정보 로딩
     *
     * @return array
     */
    public function menuLoader(): array
    {
        $data = CommonHelper::getJsonInput();
        $me_code = CommonHelper::validateParam('me_code', 'string', '', $data['me_code']);

        $result = $this->adminSettingsModel->getMenuByCode($this->cf_id, $me_code);

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '메뉴정보를 받아옵니다.-menuLoader',
            'data' => $result,
        ]);
    }

    /**
     * 메뉴 순서 변경
     *
     * @return array
     */
    public function menuOrder(): array
    {
        $data = CommonHelper::getJsonInput();

        $menuData = $data['menuData'];

        $result = $this->adminSettingsService->updateMenuOrder($menuData);
        
        if ($result === true) {
            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '메뉴 순서를 변경하였습니다.',
                'data' => $menuData,
            ]);
        } else {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '메뉴 순서 변경에 실패하였습니다.',
                'data' => $menuData,
            ]);
        }
    }

    /**
     * 메뉴 추가
     *
     * @return array
     */
    public function menuInsert(): array
    {
        $data = CommonHelper::getJsonInput();
        
        $type = isset($data['type']) ? $data['type'] : 'root';
        $me_name = CommonHelper::validateParam('me_name', 'string', '', $data['me_name']);
        $me_code = CommonHelper::validateParam('me_code', 'string', '', $data['me_code']);
        $me_parent = CommonHelper::validateParam('me_parent', 'int', 0, $data['me_parent']);
        $me_depth = CommonHelper::validateParam('me_depth', 'int', 1, $data['me_depth']);

        $formData = [
            'cf_id' => $this->cf_id,
            'me_name' => $me_name,
            'me_code' => $me_code,
            'me_parent' => $me_parent,
            'me_depth' => $me_depth ?? 1,
        ];

        $numericFields = ['me_parent', 'me_depth'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $result = $this->adminSettingsService->insertMenuData($type, $data);

        if ($result) {
            $this->updateMenuCache();
            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '메뉴를 등록하였습니다.',
                'data' => $result,
            ]);
        } else {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '메뉴 등록에 실패하였습니다.',
            ]);
        }
    }

    /**
     * 메뉴 업데이트
     *
     * @return array
     */
    public function menuUpdate(): array
    {
        $no = CommonHelper::pickNumber($_POST['no'] ?? 0);
        $me_code = CommonHelper::validateParam('me_code', 'string', '', $_POST['me_code']);

        $formData = $_POST['formData'] ?? null;

        if (empty($formData) || !$no || !$me_code) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }
        
        $numericFields = ['me_parent', 'me_depth', 'me_fsize', 'me_fweight', 'me_order', 'me_pc_use', 'me_mo_use', 'me_pa_use'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $updateData = $this->adminSettingsService->updateMenuData($this->cf_id, $no, $me_code, $data);

        if ($updateData) {
            $this->updateMenuCache();
            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '메뉴정보를 업데이트 하였습니다.',
                'data' => $updateData,
            ]);
        } else {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '환경설정 업데이트에 실패 하였습니다'
            ]);
        }
    }

    /**
     * 메뉴 삭제
     */
    public function menuDelete()
    {
        $data = CommonHelper::getJsonInput();

        if (!$data['cf_id'] || !$data['me_code']) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.'
            ]);
        }

        $result = $this->adminSettingsService->menuDelete($data);

        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '메뉴를 삭제하였습니다.'
        ]);
    }

    /**
     * 메뉴 캐시 및 컨테이너 갱신
     */
    private function updateMenuCache(): void
    {
        $ownerDomain = $this->config_domain['cf_domain'];
        //$menuController = new MenuController($ownerDomain);

        $this->menuHelper->clearMenuCache();

        $menuTree = $this->menuHelper->getMenuTree();
        $this->container->set('menu_datas', $menuTree);
    }

    // ----------------------------------------
    // 이용 약관
    // ----------------------------------------

    public function clauseList()
    {
        $clauseType = $this->configProvider->get('clauseType');

        $clauseData = $this->adminSettingsService->getClauseList();

        $params = $clauseData['params'];

        // pagination
        $queryString = CommonHelper::getQueryString($params);
        $paginationData = CommonHelper::getPaginationData(
            $clauseData['totalItems'],
            $params['page'],
            $params['page_rows'],
            $params['page_nums'],
            $queryString
        );

        $pagination = $this->componentsViewHelper->renderComponent('pagination', $paginationData);

        $searchSelectBox = [
            'pagenum' => CommonHelper::makeSelectBox(
                'pagenum',
                CommonHelper::pagingOption(),
                (string)(CommonHelper::pickNumber($_GET['pagenum'] ?? 0)),
                'pagenum',
                'frm_input list-search-item'
            ),
            'ct_page_type' => CommonHelper::makeSelectBox(
                'searchData[ct_page_type]',
                $clauseType ?? [],
                $_GET['searchData']['ct_page_type'] ?? '',
                'ct_page_type',
                'frm_input list-search-item',
                '페이지분류'
            )
        ];

        // 목록 쿼리스트링
        $queryString = '?page='.$params['page'].$queryString;

        $viewData = [
            'title' => '이용약관 관리',
            'totalItems' => $clauseData['totalItems'],
            'clauseType' => $clauseType,
            'clauseList' => $clauseData['clauseList'],
            'searchSelectBox' => $searchSelectBox,
            'queryString' => $queryString,
            'paginationData' => $paginationData,
        ];

        return [
            'viewPath' => 'Settings/clauseList',
            'viewData' => $viewData,
        ];
    }

    public function clauseForm($vars)
    {
        
        $ctId = isset($vars['param']) ? CommonHelper::pickNumber($vars['param']) : '';
        
        $clauseItem = $this->adminSettingsService->getClauseDataById((int)$ctId);

        $clauseType = $this->configProvider->get('clauseType');
        $clauseTypeCheckBox = CommonHelper::makeCheckBox(
            'formData[ct_page_type]',
            $clauseType ?? [],
            $clauseItem['ct_page_type'] ? explode(",",$clauseItem['ct_page_type']) : [],
            'ct_page_type',
            '',
            '페이지 분류'
        );

        $clauseKind = $this->configProvider->get('clauseKind');
        $clauseKindSelect = CommonHelper::makeSelectBox(
            'formData[ct_kind]',
            $clauseKind ?? [],
            $clauseItem['ct_kind'] ?? '',
            'ct_kind',
            'frm_input frm_full',
            '약관 분류'
        );

        $clauseUse = [1=>'사용함', 2=>'사용안함'];
        $clauseUseSelect = CommonHelper::makeSelectBox(
            'formData[ct_use]',
            $clauseUse ?? [],
            $clauseItem['ct_use'] ?? '',
            'ct_use',
            'frm_input frm_full',
            '사용 선택'
        );

        // 에디터 스크립트
        $editor =$this->config_domain['cf_editor'] ? $this->config_domain['cf_editor'] : 'tinymce';
        $editorScript = CommonHelper::getEditorScript($editor);

        $viewData = [
            'title' => '이용약관 등록',
            'clauseTypeCheckBox' => $clauseTypeCheckBox,
            'clauseKindSelect' => $clauseKindSelect,
            'clauseUseSelect' => $clauseUseSelect,
            'clauseItem' => $clauseItem,
            'ctId' => $ctId,
            'editorScript' => $editorScript,
        ];

        return [
            'viewPath' => 'Settings/clauseForm',
            'viewData' => $viewData,
        ];
    }

    public function clauseItemUpdate()
    {
        $ctId = CommonHelper::pickNumber($_POST['ctId']) ?? 0;

        $this->formDataMiddleware->validateToken();

        $result = $this->adminSettingsService->clauseItemUpdate($ctId);

        return CommonHelper::jsonResponse($result);
    }

    public function clauseItemDelete()
    {
        //삭제할 고유번호 $no 변수로 전달됨.
        $data = CommonHelper::getJsonInput();
        
        $this->formDataMiddleware->validateToken();

        $ctId = $data['no'] ?? 0;
        
        if (!$ctId) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.'
            ]);
        }

        $clauseItem = $this->adminSettingsService->getClauseDataById((int)$ctId);

        if (!$clauseItem['ct_id'] || (int)$clauseItem['cf_id'] !== (int)$this->config_domain['cf_id']) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '잘못된 접속입니다.'
            ]);
        }

        $result = $this->adminSettingsService->clauseItemDelete($ctId);

        return CommonHelper::jsonResponse($result);
    }
}