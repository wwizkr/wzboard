<?php
// 파일 위치: /src/Admin/Controller/ConfigController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

use Web\Admin\Service\AdminConfigService;
use Web\Admin\Helper\ConfigHelper;

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
            'anc_cf_seo' => 'SEO/스크립트 설정',
            'anc_cf_etc' => '기타 설정',
        ];

        $skin = ConfigHelper::getSkin();
        $sns_seo = ConfigHelper::getSnsSeo();

        $viewData = [
            'title' => '기본환경 설정',
            'content' => '',
            'config_domain' => $this->config_domain,
            'anchor' => $anchor,
            'skin' => $skin,
            'sns_seo' => $sns_seo,
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
        
        unset($formData['left_width']);
        unset($formData['right_width']);

        $numericFields = ['cf_layout_max_width', 'cf_content_max_width', 'cf_layout', 'cf_index_wide', 'cf_left_width', 'cf_right_width'];
        $data = $this->formDataMiddleware->handle('admin', $formData, $numericFields);
        
        $updateData = $this->adminConfigService->updateConfigDomain($cf_id, $data);

        // 캐시 설정.

        return CommonHelper::jsonResponse([
            'result' => $updateData ? 'success' : 'failure',
            'message' => $updateData ? '환경설정을 업데이트 하였습니다' : '환경설정 업데이트에 실패 하였습니다'
        ]);
    }
}