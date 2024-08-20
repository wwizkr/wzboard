<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\Admin\Helper\SettingsHelper;
use Web\PublicHtml\Model\SettingsModel;
use Web\PublicHtml\Service\SettingsService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;


class SettingsController
{
    protected $container;
    protected $settingsModel;
    protected $settingsService;

    // 생성자에서 DependencyContainer와 SettingsService를 받아옴
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $settingsModel = new SettingsModel($container); // SettingsModel 인스턴스 생성
        $this->settingsService = new SettingsService($settingsModel); // SettingsService에 SettingsModel 주입
    }

    public function index() //비어있음
    {
        $viewData = [
            'title' => 'Index',
            'content' => 'This is the user list.'
        ];

        return ['Settings/index', $viewData];
    }

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

    public function update()
    {
        $cf_id = SettingsHelper::pickNumber($_POST['cf_id'],1) ?? 1;

        /*
         * post data 는 formData 배열로 전송 됨.
         * 특정 필드명일 경우 변환 후 $data 변수에 저장
         */
        $formData = $_POST['formData'] ?? null;
        if(empty($formData)) {
            CommonHelper::alertAndBack("입력정보가 비어 있습니다. 잘못된 접속입니다.");
        }
        
        $data = [];
        $i = ['cf_max_width']; // $i 배열에는 숫자형으로 처리할 필드
        foreach($formData as $key=>$val) {
            // 만약 값이 배열이라면, '-'로 묶어서 하나의 문자열로 변환
            if (is_array($val)) {
                $val = implode('-', $val);
            }
            
            $value = $val;

            // $key가 $i 배열에 속해 있으면 ['i', $value] 형식으로, 아니면 ['s', $value] 형식으로 저장
            if (in_array($key, $i)) {
                $data[$key] = ['i', $value];
            } else {
                $data[$key] = ['s', $value];
            }
        }

        // 데이터베이스 업데이트
        $updated = $this->settingsService->updateGeneralSettings($cf_id, $data);

        if ($updated) {
            CommonHelper::alertAndRedirect("환경설정을 업데이트 하였습니다.","http://web.wizcash.kr/admin/settings/general");
        } else {
            // 업데이트 실패한 경우
            CommonHelper::alertAndBack("업데이트에 실패하였습니다. 다시 시도해 주세요!.");
        }

        return ['Settings/update', $viewData];
    }
}