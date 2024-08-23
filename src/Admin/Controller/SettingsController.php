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

    // 생성자에서 DependencyContainer와 SettingsService를 받아옴
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $settingsModel = new SettingsModel($container); // SettingsModel 인스턴스 생성
        $this->settingsService = new SettingsService($settingsModel); // SettingsService에 SettingsModel 주입
        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    /*
     * 환경설정
     */
    public function general()
    {
        // 컨테이너에서 config_domain 배열을 가져옴
        $configDomain = $this->container->get('config_domain');
        $cf_id = $configDomain['cf_id'] ?? 1;

        // 환경설정을 가져옴
        $config_domain = $this->settingsService->getGeneralSettings($cf_id);

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
            'content' => 'This is the general settings.',
            'config_domain' => $config_domain, // 환경설정 데이터를 viewData에 포함
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
            CommonHelper::alertAndBack("입력정보가 비어 있습니다. 잘못된 접속입니다.");
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
        
    }


}