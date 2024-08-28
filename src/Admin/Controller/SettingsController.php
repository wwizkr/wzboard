<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Helper\SettingsHelper;
use Web\PublicHtml\Model\SettingsModel;
use Web\PublicHtml\Service\SettingsService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class SettingsController
{
    protected $container;
    protected $settingsModel;
    protected $settingsService;
    protected $formDataMiddleware;
    protected $configDomain;
    protected $cf_id;

    // 생성자에서 DependencyContainer와 SettingsService를 받아옴
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->settingsModel = new SettingsModel($container);
        $this->settingsService = new SettingsService($this->settingsModel);
        
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);

        $this->configDomain = $this->container->get('config_domain');
        $this->cf_id = $this->configDomain['cf_id'];
    }

    /*
     * 환경설정
     */
    public function general()
    {
        // 컨테이너에서 환경설정을 가져옴
        $configDomain = $this->configDomain;

        // 탭배열
        $anchor = [
            'anc_cf_basic' => '홈페이지 정보',
            'anc_cf_layout' => '레이아웃 설정',
            'anc_cf_member' => '회원 설정',
            'anc_cf_seo' => 'SEO/스크립트 설정',
            'anc_cf_etc' => '기타 설정',
        ];

        // Settings 클래스를 통해 설정값 불러오기
        $skin = SettingsHelper::getSkin();
        $sns_seo = SettingsHelper::getSnsSeo();

        $viewData = [
            'title' => '기본환경 설정',
            'content' => '',
            'config_domain' => $configDomain, // 환경설정 데이터를 viewData에 포함
            'anchor' => $anchor,
            'skin' => $skin,
            'sns_seo' => $sns_seo,
        ];

        return ['Settings/general', $viewData];
    }
    
    /*
     * 환경설정 업데이트
     */
    public function update()
    {
        $cf_id = CommonHelper::pickNumber($_POST['cf_id'],1) ?? 1;

        /*
         * post data 는 formData 배열로 전송 됨.
         * 특정 필드명일 경우 변환 후 $data 변수에 저장
         */
        $formData = $_POST['formData'] ?? null;
        if(empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }
        
        $numericFields = ['cf_max_width'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);
        
        // 데이터베이스 업데이트
        $updated = $this->settingsService->updateGeneralSettings($cf_id, $data);
        if ($updated) {
            //CommonHelper::alertAndRedirect("환경설정을 업데이트 하였습니다.","http://web.wizcash.kr/admin/settings/general");
            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '환경설정을 업데이트 하였습니다'
            ]);
        } else {
            // 업데이트 실패한 경우
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '환경설정 업데이트에 실패 하였습니다'
            ]);
        }
    }

    /*
     * 메뉴설정
     */
    public function menus()
    {
        // 컨테이너에서 메뉴설정을 가져옴
        $menuDatas = $this->container->get('menu_datas') ?? null;

        $viewData = [
            'title' => '메뉴 설정',
            'content' => '',
            'menuDatas' => $menuDatas,
        ];

        return ['Settings/menus', $viewData];
    }

    /*
     * 메뉴로딩
     */
    public function menuLoader()
    {
        $cf_id = $this->cf_id;
        $configDomain = $this->configDomain;

        //error_log("menuLoader - \$_POST: " . print_r($_POST, true));

        $me_code = $_POST['me_code'] ?? null;
        $result = $this->settingsModel->getMenuByCode($cf_id, $me_code);
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '메뉴정보를 받아옵니다.-menuLoader',
            'data' => $result,
        ]);
    }

    public function menuOrder() 
    {
        return CommonHelper::jsonResponse([
            'result' => 'success',
            'message' => '환경설정을 업데이트 하였습니다-menuOrder'
        ]);
    }

    public function menuInsert()
    {
        $cf_id = $this->cf_id;
        $configDomain = $this->configDomain;
        
        $type = $_POST['type'] ?? 'root';
        //formData 생성
        $formData = [];
        $formData['cf_id'] = $cf_id;
        $formData['me_name'] = $_POST['me_name'] ?? '';
        $formData['me_code'] = $_POST['me_code'] ?? '';
        $formData['me_parent'] = $_POST['me_parent'] ?? 0;
        $formData['me_depth']  = $_POST['me_depth'] ?? 1;
        if($formData['me_depth'] == 0) {
            $formData['me_depth'] = 1;
        }

        $numericFields = ['me_parent','me_depth'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $result = $this->settingsService->insertMenuData($type, $data);

        if($result) {
            // MenuController를 이용하여 캐시 및 컨테이너 갱신
            $ownerDomain = $configDomain['cf_domain'];
            $menuController = new \Web\PublicHtml\Controller\MenuController($ownerDomain);

            // 캐시 갱신
            $menuController->clearMenuCache();

            // 새로운 메뉴 데이터를 컨테이너에 설정
            $menuTree = $menuController->getMenuData();
            $this->container->set('menu_datas', $menuTree);

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

    public function menuUpdate()
    {
        $cf_id = $this->cf_id;
        $configDomain = $this->configDomain;

        $no = CommonHelper::pickNumber($_POST['no']) ?? 0;
        $me_code = $_POST['me_code'] ?? '';

        $formData = $_POST['formData'] ?? null;
        if(empty($formData) || !$no || !$me_code) {
            CommonHelper::alertAndBack("입력정보가 비어 있습니다. 잘못된 접속입니다.");
        }
        
        $numericFields = ['me_parent', 'me_depth', 'me_fsize', 'me_fweight', 'me_order', 'me_pc_use', 'me_mo_use', 'me_pa_use'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        // 데이터베이스 업데이트
        $updateData = $this->settingsService->updateMenuData($cf_id, $no, $me_code, $data);

        if ($updateData) {
            // MenuController를 이용하여 캐시 및 컨테이너 갱신
            $ownerDomain = $configDomain['cf_domain'];
            $menuController = new \Web\PublicHtml\Controller\MenuController($ownerDomain);

            // 캐시 갱신
            $menuController->clearMenuCache();

            // 새로운 메뉴 데이터를 컨테이너에 설정
            $menuTree = $menuController->getMenuData();
            $this->container->set('menu_datas', $menuTree);

            return CommonHelper::jsonResponse([
                'result' => 'success',
                'message' => '메뉴정보를 업데이트 하였습니다.',
                'data'=> $updateData,
            ]);
        } else {
            // 업데이트 실패한 경우
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '환경설정 업데이트에 실패 하였습니다'
            ]);
        }
    }
}