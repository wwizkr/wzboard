<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\AdminSettingsHelper;
use Web\Admin\Model\AdminSettingsModel;
use Web\Admin\Service\AdminSettingsService;
use Web\Admin\Helper\AdminMenuHelper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;
use Web\PublicHtml\Helper\MenuHelper;

class SettingsController
{
    protected DependencyContainer $container;
    protected adminSettingsModel $adminSettingsModel;
    protected adminSettingsService $adminSettingsService;
    protected adminMenuHelper $adminMenuHelper;
    protected FormDataMiddleware $formDataMiddleware;
    protected menuHelper $menuHelper;
    protected array $config_domain;
    protected int $cf_id;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->adminSettingsModel = new AdminSettingsModel($container);
        $this->adminSettingsService = new AdminSettingsService($this->adminSettingsModel);
        $this->adminMenuHelper = new AdminMenuHelper($this->container);
        $this->menuHelper = new MenuHelper();
        
        $csrfTokenHandler = new CsrfTokenHandler($container->get('SessionManager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);

        $this->config_domain = $this->container->get('config_domain');
        $this->cf_id = (int)$this->config_domain['cf_id'];
    }

    /**
     * 환경설정 페이지 표시
     *
     * @return array
     */
    public function general(): array
    {
        $anchor = [
            'anc_cf_basic' => '홈페이지 정보',
            'anc_cf_layout' => '레이아웃 설정',
            'anc_cf_member' => '회원 설정',
            'anc_cf_seo' => 'SEO/스크립트 설정',
            'anc_cf_etc' => '기타 설정',
        ];

        $skin = AdminSettingsHelper::getSkin();
        $sns_seo = AdminSettingsHelper::getSnsSeo();

        return [
            'Settings/general',
            [
                'title' => '기본환경 설정',
                'content' => '',
                'config_domain' => $this->config_domain,
                'anchor' => $anchor,
                'skin' => $skin,
                'sns_seo' => $sns_seo,
            ]
        ];
    }
    
    /**
     * 환경설정 업데이트
     *
     * @return array
     */
    public function update(): array
    {
        $cf_id = CommonHelper::pickNumber($_POST['cf_id'] ?? 1, 1);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }
        
        $numericFields = ['cf_max_width'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);
        
        $updateData = $this->adminSettingsService->updateGeneralSettings($cf_id, $data);

        // 캐시 설정.

        return CommonHelper::jsonResponse([
            'result' => $updateData ? 'success' : 'failure',
            'message' => $updateData ? '환경설정을 업데이트 하였습니다' : '환경설정 업데이트에 실패 하였습니다'
        ]);
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

        return [
            'Settings/menus',
            [
                'title' => '메뉴 설정',
                'content' => '',
                'menuDatas' => $menuDatas,
                'menuCategory' => $menuCategory,
            ]
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
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '메뉴 순서를 변경합니다.'
        ]);
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

        error_log("Menu Update Data:".print_r($updateData, true));

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
}