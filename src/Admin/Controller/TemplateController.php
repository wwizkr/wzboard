<?php
// 파일 위치: /src/Admin/Controller/TemplateController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
//use Web\Admin\Service\AdminTemplateService;
//use Web\Admin\Model\AdminTemplateModel;

class TemplateController
{
    protected DependencyContainer $container;
    protected $formDataMiddleware;
    protected $menuHelper;
    protected $config_domain;
    protected $configProvider;
    protected $adminTemplateService;
    protected $adminTemplateModel;
    protected $adminViewRenderer;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->configProvider = $this->container->get('ConfigProvider');

        $this->adminTemplateService = $this->container->get('AdminTemplateService');
        $this->adminTemplateModel = $this->container->get('AdminTemplateModel');
        $this->adminViewRenderer = $this->container->get('AdminViewRenderer');
    }

    protected function setAssets(): void
    {
        $this->adminViewRenderer->addAsset('css', '/assets/js/lib/editor/tinymce/tinymce.custom.css');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/editor/tinymce/tinymce.min.js');
        $this->adminViewRenderer->addAsset('js', '/assets/js/lib/editor/tinymce/tinymce.editor.js');
    }

    /**
     * 페이지 템플릿 목록
     */
    public function templateList(array $vars): array
    {
        $table = isset($vars['param']) ? $vars['param'] : 'template';

        $listData = $this->adminTemplateService->getTemplateList($table);

        $viewData = [
            'title' => '메인화면/페이지관리',
            'listData' => $listData,
        ];

        return [
            'viewPath' => 'Settings/templateList',
            'viewData' => $viewData,
        ];
    }

    /**
     * 페이지 템플릿 등록 폼
     */
    public function templateForm(array $vars): array
    {
        $this->setAssets();

        $table = isset($vars['param']) ? $vars['param'] : 'template';
        $ctId = CommonHelper::validateParam('ct_id', 'int', 0, '', INPUT_GET);

        $templateData = $this->adminTemplateService->getTemplateDataById($table, (int)$ctId);
        /*
         * 기본 배열
         *
         */
        $baseConfig = [
            'ct_id' => $templateData['ct_id'] ?? '',
            'ct_section_id' => $templateData['ct_section_id'] ?? '',
            'ct_position' => $templateData['ct_position'] ?? '',
            'ct_position_sub' => $templateData['ct_position_sub'] ?? '',
            'ct_position_subtype' => $templateData['ct_position_subtype'] ?? '',
            'boxSubjectView' => !empty($templateData['ct_subject_view']) ? explode(",", $templateData['ct_subject_view']) : [],
            'boxSubject' => !empty($templateData['ct_subject']) ? explode(",", $templateData['ct_subject']) : [],
            'boxSubjectColor' => !empty($templateData['ct_subject_color']) ? explode(",", $templateData['ct_subject_color']) : [],
            'boxSubjectSize' => !empty($templateData['ct_subject_size']) ? explode(",", $templateData['ct_subject_size']) : [],
            'boxmSubjectSize' => !empty($templateData['ct_msubject_size']) ? explode(",", $templateData['ct_msubject_size']) : [],
            'boxSubjectPos' => !empty($templateData['ct_subject_pos']) ? explode(",", $templateData['ct_subject_pos']) : [],
            'boxCopytext' => !empty($templateData['ct_copytext']) ? explode(",", $templateData['ct_copytext']) : [],
            'boxCopytextColor' => !empty($templateData['ct_copytext_color']) ? explode(",", $templateData['ct_copytext_color']) : [],
            'boxCopytextSize' => !empty($templateData['ct_copytext_size']) ? explode(",", $templateData['ct_copytext_size']) : [],
            'boxmCopytextSize' => !empty($templateData['ct_mcopytext_size']) ? explode(",", $templateData['ct_mcopytext_size']) : [],
            'boxCopytextPos' => !empty($templateData['ct_copytext_pos']) ? explode(",", $templateData['ct_copytext_pos']) : [],
            'boxBgColor' => !empty($templateData['ct_list_box_bgcolor']) ? explode(",", $templateData['ct_list_box_bgcolor']) : [],
            'boxBgImage' => !empty($templateData['ct_list_box_bgimage']) ? explode(",", $templateData['ct_list_box_bgimage']) : [],
            'boxPcPadding' => !empty($templateData['ct_list_box_pc_padding']) ? explode(",", $templateData['ct_list_box_pc_padding']) : [],
            'boxMoPadding' => !empty($templateData['ct_list_box_mo_padding']) ? explode(",", $templateData['ct_list_box_mo_padding']) : [],
            'boxBorderWidth' => !empty($templateData['ct_list_box_border_width']) ? explode(",", $templateData['ct_list_box_border_width']) : [],
            'boxBorderColor' => !empty($templateData['ct_list_box_border_color']) ? explode(",", $templateData['ct_list_box_border_color']) : [],
            'boxBorderRound' => !empty($templateData['ct_list_box_border_round']) ? explode(",", $templateData['ct_list_box_border_round']) : [],
            'boxSubjectPcImage' => !empty($templateData['ct_subject_pc_image']) ? explode(",", $templateData['ct_subject_pc_image']) : [],
            'boxSubjectMobileImage' => !empty($templateData['ct_subject_mo_image']) ? explode(",", $templateData['ct_subject_mo_image']) : [],
            'boxSubjectMoreLink' => !empty($templateData['ct_subject_more_link']) ? explode(",", $templateData['ct_subject_more_link']) : [],
            'boxSubjectMoreUrl' => !empty($templateData['ct_subject_more_url']) ? explode(",", $templateData['ct_subject_more_url']) : [],
            'boxWidth' => !empty($templateData['ct_list_box_width']) ? explode(",", $templateData['ct_list_box_width']) : [],
            'boxItemType' => !empty($templateData['ct_list_box_itemtype']) ? explode(",", $templateData['ct_list_box_itemtype']) : [],
            'boxShopType' => !empty($templateData['ct_list_box_shoptype']) ? explode(",", $templateData['ct_list_box_shoptype']) : [],
            'boxItemCnt' => !empty($templateData['ct_list_box_itemcnt']) ? explode(",", $templateData['ct_list_box_itemcnt']) : [],
            'boxEffect' => !empty($templateData['ct_list_box_effect']) ? explode(",", $templateData['ct_list_box_effect']) : [],
            'boxPcStyle' => !empty($templateData['ct_list_box_pcstyle']) ? explode(",", $templateData['ct_list_box_pcstyle']) : [],
            'boxMoStyle' => !empty($templateData['ct_list_box_mostyle']) ? explode(",", $templateData['ct_list_box_mostyle']) : [],
            'boxPcCols' => !empty($templateData['ct_list_box_pccols']) ? explode(",", $templateData['ct_list_box_pccols']) : [],
            'boxMoCols' => !empty($templateData['ct_list_box_mocols']) ? explode(",", $templateData['ct_list_box_mocols']) : [],
            'boxItems' => !empty($templateData['ct_list_box_items']) ? explode(",", $templateData['ct_list_box_items']) : [],
            'maxWidth' => intval($this->config_domain['cf_max_width'] ?? 0),
            'template_items' => $this->configProvider->get('template')['template_items'] ?? [],
            'noimg' => $this->configProvider->get('image')['noImg100'] ?? '',
            'ruleStr' => ['editor', 'outlogin', 'file'],
            'styleStr' => ['outlogin', 'editor', 'movie', 'submenu', 'file'],
            'itemStr' => ['image', 'editor', 'banner', 'board', 'movie', 'event'],
            'listEvent' => $this->configProvider->get('template')['aos_effect'] ?? [],
        ];

        $viewData = [
            'title' => '메인화면/페이지관리',
            'table' => $table,
            'config_domain' => $this->config_domain,
            'templateData' => $templateData,
            'menuData' => $this->container->get('menu_datas'),
            'baseConfig' => json_encode($baseConfig),
            'configProvider' => $this->configProvider,
        ];

        return [
            'viewPath' => 'Settings/templateForm',
            'viewData' => $viewData,
        ];
    }

    /**
     * 템플릿 아이템 가져오기 ajax 요청
     */
    public function templateItem(array $vars)
    {
        $table = CommonHelper::validateParam('table', 'string', '', '', INPUT_POST);
        $itemType = CommonHelper::validateParam('itemtype', 'string', '', '', INPUT_POST);
        $boxId = CommonHelper::validateParam('idx', 'int', 0, '', INPUT_POST); //ci_box_id
        $ctId = CommonHelper::validateParam('ct_id', 'int', 0, '', INPUT_POST); //ct_id
        
        $result = $this->adminTemplateService->getTemplateItemData($table, $itemType, $boxId, $ctId);

        return CommonHelper::jsonResponse($result);
    }

    /**
     * 템플릿 등록, 수정
     *
     */
    public function templateUpdate()
    {
        $table = isset($_POST['table']) ? $_POST['table'] : '';
        $ctId = CommonHelper::validateParam('ct_id', 'int', 0, '', INPUT_POST); //ct_id
        $cgId = CommonHelper::validateParam('cg_id', 'int', 0, '', INPUT_POST); //cg_id

        $ct_section_id = CommonHelper::validateParam('ct_section_id', 'string', '', '', INPUT_POST);

        if ($table !== 'template' && $table !== 'page' || !$ct_section_id) {
            $message = '템플릿 정보가 잘못되었습니다.';
            CommonHelper::alertAndBack($message);
        }
        
        $section = $this->adminTemplateModel->getTemplateDataBySectionId($table, $ct_section_id, (int)$this->config_domain['cf_id']);
        if (!$ctId && !empty($section)) {
            $message = '템플릿 아이디는 중복될 수 없습니다.';
            CommonHelper::alertAndBack($message);
        }

        if ($ctId && empty($section)) {
            $message = '템플릿 정보가 잘못되었습니다.';
            CommonHelper::alertAndBack($message);
        }

        $result = $this->adminTemplateService->templateUpdate($table, $ct_section_id, (int)$ctId, (int)$cgId);

        $message = '처리하였습니다.';
        $url = '/admin/template/templateForm/'.$table.'?ct_id='.$result['ins_id'];
        CommonHelper::alertAndRedirect($message, $url);
    }
}