<?php
// 파일 위치: /src/Admin/Controller/BannerController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Service\AdminBannerService;

class BannerController
{
    protected DependencyContainer $container;
    protected array $config_domain;
    protected $adminViewRenderer;
    protected $adminBannerService;


    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminViewRenderer = $this->container->get('AdminViewRenderer');
        $this->adminBannerService = new AdminBannerService($this->container);
    }

    protected function setAssets(): void
    {
        $this->adminViewRenderer->addAsset('css', '/assets/js/lib/color-picker/jquery.minicolors.css');
        $this->adminViewRenderer->addAsset('css', '/assets/js/lib/jquery-ui.css');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery-3.7.1.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery-migrate-3.5.0.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/jquery-ui.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/color-picker/jquery.minicolors.min.js');
    }

    public function bannerList($vars)
    {
        $viewData = [
            'title' => '배너 목록',
        ];

        return [
            'viewPath' => 'Design/bannerList',
            'viewData' => $viewData,
        ];
    }

    public function bannerForm($vars)
    {
        $this->setAssets();
        $configProvider = $this->container->get('ConfigProvider');

        $baId = isset($vars['param']) ? CommonHelper::pickNumber($vars['param']) : 0;

        $bannerData = $this->adminBannerService->getBannerDataById((int)$baId);
        $bannerData['images'] = [];
        
        $images = ['pc' => $bannerData['ba_pc_image'], 'mo' => $bannerData['ba_mo_image'], 'bg' => $bannerData['ba_bg_image']];
        foreach($images as $key => $val) {
            if ($val && file_exists(WZ_STORAGE_PATH.'/banner/'.$this->config_domain['cf_id'].'/'.$val)) {
                $bannerData['images'][$key]['url'] = '/storage/banner/'.$this->config_domain['cf_id'].'/'.$val;
                $bannerData['images'][$key]['del'] = true;
            } else {
                $bannerData['images'][$key]['url'] = $configProvider->get('image')['noImg430'];
                $bannerData['images'][$key]['del'] = false;
            }
        }

        $viewData = [
            'title' => $baId ? '배너 수정' : '배너 등록',
            'data' => $bannerData,
            'noImage430' => $configProvider->get('image')['noImg430'],
        ];

        return [
            'viewPath' => 'Design/bannerForm',
            'viewData' => $viewData,
        ];
    }

    public function bannerUpdate($vars)
    {
        $baId = CommonHelper::validateParam('ba_id', 'int', 0, '', INPUT_POST); //ba_id

        $result = $this->adminBannerService->bannerUpdate((int)$baId);
        
        $updateData = 1;
        return CommonHelper::jsonResponse([
            'result' => $updateData ? 'success' : 'failure',
            'message' => $updateData ? '환경설정을 업데이트 하였습니다' : '환경설정 업데이트에 실패 하였습니다',
            'data' => ['post' => $_POST, 'file' => $_FILES],
        ]);
    }
}