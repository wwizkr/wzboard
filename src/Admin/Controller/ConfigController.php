<?php
// 파일 위치: /src/Admin/Controller/ConfigController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Service\AdminConfigService;
use Web\Admin\Helper\AdminConfigHelper;
use Web\Admin\Helper\AdminCommonHelper;
use Web\Admin\Helper\AdminMenuHelper;
use Web\PublicHtml\Helper\MenuHelper;

class ConfigController
{
    protected DependencyContainer $container;
    protected $adminConfigService;
    protected $adminMenuHelper;
    protected $formDataMiddleware;
    protected $menuHelper;
    protected $config_domain;
    protected $configProvider;
    protected $membersService;
    protected int $cf_id;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->adminConfigService = new AdminConfigService($this->container);
        $this->adminMenuHelper = new AdminMenuHelper($this->container);
        $this->menuHelper = new MenuHelper();
        
        $this->formDataMiddleware = $container->get('FormDataMiddleware');

        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->cf_id = (int)$this->config_domain['cf_id'];
        $this->configProvider = $this->container->get('ConfigProvider');
        $this->membersService = $this->container->get('MembersService');
    }

    /**
     * 기본(도매인) 환경설정 페이지 표시
     *
     * @return array
     */
    public function configDomain(): array
    {
        $anchor = [
            'anc_cf_basic' => '홈페이지 정보',
            'anc_cf_layout' => '레이아웃 설정',
            'anc_cf_member' => '회원 설정',
            'anc_cf_sns' => 'SNS 로그인 설정',
            'anc_cf_point' => '적립금 설정',
            'anc_cf_seo' => 'SEO/스크립트 설정',
            'anc_cf_etc' => '기타 설정',
        ];

        $skin = AdminConfigHelper::getSkin();
        $widget = AdminConfigHelper::getWidgetSkin($this->configProvider->get('widget')['positions']);
        $image = [
            'noImage430' => str_replace(WZ_PUBLIC_PATH, "", $this->configProvider->get('image')['noImg430']),
            'icoImage' => file_exists(WZ_PUBLIC_PATH.'/common/'.$this->cf_id.'/favicon.ico') ? 'data:image/'.pathinfo($ico_image, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($ico_image)) : '',
            'pcLogo' => file_exists(WZ_PUBLIC_PATH.'/common/'.$this->cf_id.'/pc_logo_image') ? '/storage/common/'.$this->cf_id.'/pc_logo_image' : str_replace(WZ_PUBLIC_PATH, "", $this->configProvider->get('image')['noImg430']),
            'moLogo' => file_exists(WZ_PUBLIC_PATH.'/common/'.$this->cf_id.'/mo_logo_image') ? '/storage/common/'.$this->cf_id.'/mo_logo_image' : str_replace(WZ_PUBLIC_PATH, "", $this->configProvider->get('image')['noImg430']),
            'ogImage' => file_exists(WZ_PUBLIC_PATH.'/common/'.$this->cf_id.'/og_image.jpg') ? '/storage/common/'.$this->cf_id.'/og_image.jpg' : str_replace(WZ_PUBLIC_PATH, "", $this->configProvider->get('image')['noImg430']),
        ];

        $memberLevel = $this->membersService->getMemberLevelData();
        $memberOption = [];
        foreach($memberLevel as $key=>$val) {
            $memberOption[$val['level_id']] = $val['level_name'];
        }
        $memberSelect = AdminCommonHelper::makeSelectBox('formData[cf_register_level]', $memberOption, $this->config_domain['cf_register_level'], 'cf_register_level', 'frm_full');

        $snsLogin = AdminConfigHelper::getSnsLogin();
        $snsSeo = AdminConfigHelper::getSnsSeo();

        $viewData = [
            'title' => '기본환경 설정',
            'content' => '',
            'config_domain' => $this->config_domain,
            'anchor' => $anchor,
            'skin' => $skin,
            'widget' => $widget,
            'image' => $image,
            'memberSelect' => $memberSelect,
            'snsLogin' => $snsLogin,
            'snsSeo' => $snsSeo,
        ];

        return [
            'viewPath' => 'Config/configDomain',
            'viewData' => $viewData,
        ];
    }
    
    /**
     * 기본(도매인) 환경설정 업데이트
     *
     * @return array
     */
    public function configDomainUpdate(): array
    {
        $cf_id = CommonHelper::pickNumber($_POST['cf_id'] ?? 1, 1);
        $formData = $_POST['formData'] ?? null;

        if (empty($formData)) {
            return CommonHelper::jsonResponse([
                'result' => 'failure',
                'message' => '입력정보가 비어 있습니다. 잘못된 접속입니다.'
            ]);
        }

        $layout = isset($formData['cf_layout']) ? $formData['cf_layout'] : 0;
        $formData['cf_left_width'] = isset($formData['left_width'][$layout]) ? $formData['left_width'][$layout] : 0;
        $formData['cf_right_width'] = isset($formData['right_width'][$layout]) ? $formData['right_width'][$layout] : 0;
        $formData['cf_sns_channel_url'] = isset($formData['sns']) && !empty($formData['sns']) ? serialize($formData['sns']) : '';
        $formData['cf_use_naver_ad'] = isset($formData['cf_use_naver_ad']) && $formData['cf_use_naver_ad'] === 'Y' ? 'Y' : 'N';
        
        unset($formData['left_width']);
        unset($formData['right_width']);
        unset($formData['cf_sns']);

        $numericFields = [
            'cf_layout_max_width',
            'cf_content_max_width',
            'cf_layout', 'cf_left_width',
            'cf_right_width',
            'cf_index_wide',
            'cf_mobile_fix_widget',
            'cf_mobile_panel_widget',
            'cf_right_widget',
            'cf_left_widget',
            'cf_pc_page_rows',
            'cf_mo_page_rows',
            'cf_pc_page_nums',
            'cf_mo_page_nums',
            'cf_cert_use',
            'cf_auto_register',
            'cf_use_email_certify',
            'cf_register_level',
            'cf_register_allow',
            'cf_auto_levelup',
            'cf_use_hp',
            'cf_req_hp',
            'cf_use_addr',
            'cf_req_addr',
            'cf_use_recommend',
            'cf_use_point',
            'cf_join_point',
            'cf_login_point',
            'cf_recommend_member_point',
            'cf_recommend_point_type',
            'cf_recommend_order_point',
            'cf_board_read_point',
            'cf_board_write_point',
            'cf_board_comment_point',
            'cf_board_download_point',
            'cf_login_minutes',
            'cf_visit_del',
            'cf_popular_del',
            'cf_social_login_use'
        ];

        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);
        $updateData = $this->adminConfigService->updateConfigDomain($cf_id, $data);

        return CommonHelper::jsonResponse([
            'result' => $updateData ? 'success' : 'failure',
            'message' => $updateData ? '환경설정을 업데이트 하였습니다' : '환경설정 업데이트에 실패 하였습니다'
        ]);
    }
}