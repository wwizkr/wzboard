<?php
// 파일 위치: src/Service/TemplateService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\CacheHelper;

class TemplateService
{
    protected $container;
    protected $config_domain;
    protected $adminTemplateModel;
    protected $isMobile;

    public function __construct(DependencyContainer $container) {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminTemplateModel = $this->container->get('AdminTemplateModel');
        $this->isMobile = CommonHelper::isMobile();
    }

    public function getHomeTemplateData()
    {
        $templateList = $this->adminTemplateModel->getTemplateList('template', $this->config_domain['cf_id'], 'index', 0);
        $processedTemplates = [];
        foreach ($templateList as $template) {
            $processedTemplate = $this->processTemplate($template, 'template');
            if ($processedTemplate) {
                $processedTemplates[] = $processedTemplate;
            }
        }
        
        return $processedTemplates;
    }

    private function processTemplate($template, $table = 'template')
    {
        // 템플릿 데이터 처리 로직
        $cf_id = $template['cf_id'];
        $ct_id = $template['ct_id'];
        
        $templateData = [
            'cf_id' => $cf_id,
            'ct_id' => $ct_id,
            'ct_position' => $template['ct_position'],
            'ct_section_id' => $template['ct_section_id'],
            'sectionWidth' => $template['ct_list_width'] == 0 ? '' : 'max-layout',
            'sectionHeight' => $this->getTemplateHeight($template),
            'padding' => $this->getTemplatePadding($template),
            'bgcolor' => $template['ct_list_bgcolor'] ? 'background-color: ' . $template['ct_list_bgcolor'] . ';' : '',
            'bgimage' => $this->getTemplateBgImage($template, $table),
            'boxes' => $this->processBoxes($template, $table)
        ];

        return $templateData;
    }

    private function getTemplateHeight($template)
    {
        return $this->isMobile ? 
            ($template['ct_list_mo_height'] == 0 ? 'auto' : $template['ct_list_mo_height']) :
            ($template['ct_list_pc_height'] == 0 ? 'auto' : $template['ct_list_pc_height']);
    }

    private function getTemplatePadding($template)
    {
        $padding = $this->isMobile ? $template['ct_list_mo_padding'] : $template['ct_list_pc_padding'];
        if ($padding) {
            $arr_padding = explode(",", $padding);
            return 'padding: ' . implode(' ', $arr_padding) . ';';
        }
        return '';
    }

    private function getTemplateBgImage($template, $table)
    {
        $bgimage = $template['ct_list_bgimage'] ?? '';
        if ($bgimage && file_exists(WZ_STORAGE_PATH.'/template/' . $this->config_domain['cf_id'] . '/'.$table.'/' . $bgimage)) {
            return $_ENV['APP_URL'] . '/storage/template/' . $this->config_domain['cf_id'] . '/'.$table.'/' . $bgimage;
        }
        return '';
    }

    private function processBoxes($template, $table = 'template')
    {
        $boxes = [];
        $box_cnt = $template['ct_list_box_cnt'];
        for ($box_id = 0; $box_id < $box_cnt; $box_id++) {
            $boxes[] = $this->processBox($template, $box_id, $table);
        }
        return $boxes;
    }

    private function processBox($template, $box_id, $table = 'template')
    {
        $parseString = function($str, $delimiter = ",") {
            if ($str === null || $str === '') {
                return array();
            }
            return explode($delimiter, $str);
        };

        $boxData = [
            'ct_id' => $template['ct_id'],
            'ct_subject_view' => $parseString($template['ct_subject_view']),
            'ct_subject' => $parseString($template['ct_subject']),
            'ct_subject_color' => $parseString($template['ct_subject_color']),
            'ct_subject_size' => $parseString($template['ct_subject_size']),
            'ct_msubject_size' => $parseString($template['ct_msubject_size']),
            'ct_subject_pos' => $parseString($template['ct_subject_pos']),
            'ct_copytext' => $parseString($template['ct_copytext']),
            'ct_copytext_color' => $parseString($template['ct_copytext_color']),
            'ct_copytext_size' => $parseString($template['ct_copytext_size']),
            'ct_mcopytext_size' => $parseString($template['ct_mcopytext_size']),
            'ct_copytext_pos' => $parseString($template['ct_copytext_pos']),
            'ct_subject_pc_image' => $parseString($template['ct_subject_pc_image']),
            'ct_subject_mo_image' => $parseString($template['ct_subject_mo_image']),
            'ct_subject_more_link' => $parseString($template['ct_subject_more_link']),
            'ct_subject_more_url' => $parseString($template['ct_subject_more_url']),
            'ct_list_box_width' => $parseString($template['ct_list_box_width']),
            'ct_list_box_itemtype' => $parseString($template['ct_list_box_itemtype']),
            'ct_list_box_skin' => $parseString($template['ct_list_box_skin']),
            'ct_list_box_itemcnt' => $parseString($template['ct_list_box_itemcnt']),
            'ct_list_box_pcstyle' => $parseString($template['ct_list_box_pcstyle']),
            'ct_list_box_mostyle' => $parseString($template['ct_list_box_mostyle']),
            'ct_list_box_pccols' => $parseString($template['ct_list_box_pccols']),
            'ct_list_box_mocols' => $parseString($template['ct_list_box_mocols']),
            'ct_list_box_items' => $parseString($template['ct_list_box_items'], ":"),
            'ct_list_box_bg_color' => $parseString($template['ct_list_box_bgcolor']),
            'ct_list_box_bg_image' => $parseString($template['ct_list_box_bgimage']),
            'ct_list_box_pc_padding' => $parseString($template['ct_list_box_pc_padding'], "@"),
            'ct_list_box_mo_padding' => $parseString($template['ct_list_box_mo_padding'], "@"),
            'ct_list_box_border_width' => $parseString($template['ct_list_box_border_width']),
            'ct_list_box_border_color' => $parseString($template['ct_list_box_border_color']),
            'ct_list_box_border_round' => $parseString($template['ct_list_box_border_round']),
            'ct_list_box_effect' => $parseString($template['ct_list_box_effect']),
        ];

        $calculateBoxWidth = function() use ($template, $boxData, $box_id) {
            if ($template['ct_list_box_wtype'] === 1) {
                return isset($boxData['ct_list_box_width'][$box_id]) && $boxData['ct_list_box_width'][$box_id] !== '' 
                    ? $boxData['ct_list_box_width'][$box_id] . '%' 
                    : '100%';
            } else {
                return isset($boxData['ct_list_box_width'][$box_id]) && $boxData['ct_list_box_width'][$box_id] !== '' 
                    ? $boxData['ct_list_box_width'][$box_id] . 'px' 
                    : '100%';
            }
        };

        $subjectData = [
            'view' => $boxData['ct_subject_view'][$box_id] ?? '',
            'text' => $boxData['ct_subject'][$box_id] ?? '',
            'color' => $boxData['ct_subject_color'][$box_id] ?? '',
            'size' => $this->isMobile ? ($boxData['ct_msubject_size'][$box_id] ?? '') : ($boxData['ct_subject_size'][$box_id] ?? ''),
            'position' => $boxData['ct_subject_pos'][$box_id] ?? '',
            'image' => $this->getSubjectImage($template, $box_id, $table),
            'more_link' => $boxData['ct_subject_more_link'][$box_id] ?? '',
            'more_url' => $boxData['ct_subject_more_url'][$box_id] ?? '',
        ];

        $copytextData = [
            'text' => $boxData['ct_copytext'][$box_id] ?? '',
            'color' => $boxData['ct_copytext_color'][$box_id] ?? '',
            'size' => $this->isMobile ? ($boxData['ct_mcopytext_size'][$box_id] ?? '') : ($boxData['ct_copytext_size'][$box_id] ?? ''),
            'position' => $boxData['ct_copytext_pos'][$box_id] ?? 'right',
        ];

        return [
            'id' => $boxData['ct_id'],
            'item_dir' => $boxData['ct_list_box_itemtype'][$box_id] ?? '',
            'skin_dir' => $boxData['ct_list_box_skin'][$box_id] ?? '',
            'subject' => $subjectData,
            'copytext' => $copytextData,
            'boxWidth' => $calculateBoxWidth(),
            'itemcnt' => $boxData['ct_list_box_itemcnt'][$box_id] ?? 8,
            'effect' => $this->getBoxEffect($boxData['ct_list_box_effect'], $box_id),
            'style' => $this->isMobile ? ($boxData['ct_list_box_mostyle'][$box_id] ?? '') : ($boxData['ct_list_box_pcstyle'][$box_id] ?? ''),
            'cols' => $this->getBoxCols($boxData, $box_id),
            'display' => $this->getBoxDisplay($boxData, $box_id),
            'items' => $this->getBoxItems($template['ct_id'], $box_id, $table),
            'bg_color' => $boxData['ct_list_box_bg_color'][$box_id] ?? '',
            'bg_image' => $boxData['ct_list_box_bg_image'][$box_id] ?? '',
            'pc_padding' => $boxData['ct_list_box_pc_padding'][$box_id] ?? '',
            'mo_padding' => $boxData['ct_list_box_mo_padding'][$box_id] ?? '',
            'border_width' => $boxData['ct_list_box_border_width'][$box_id] ?? '',
            'border_color' => $boxData['ct_list_box_border_color'][$box_id] ?? '',
            'border_round' => $boxData['ct_list_box_border_round'][$box_id] ?? '',
        ];
    }

    private function getSubjectImage($template, $box_id, $table)
    {
        $subject_image = $this->isMobile && isset($template['ct_subject_mo_image'][$box_id]) 
            ? $template['ct_subject_mo_image'][$box_id] 
            : ($template['ct_subject_pc_image'][$box_id] ?? '');
        if ($subject_image && file_exists(WZ_STORAGE_PATH.'/template/'.$this->config_domain['cf_id'].'/'.$table.'/'.$subject_image)) {
            return '/storage/template/'.$this->config_domain['cf_id'].'/'.$table.'/'.$subject_image;
        }
        return '';
    }

    private function getBoxEffect($template, $box_id)
    {
        return isset($template['ct_list_box_effect'][$box_id]) && $template['ct_list_box_effect'][$box_id] 
               ? 'data-aos="'.$template['ct_list_box_effect'][$box_id].'" data-aos-duration="1000"' 
               : '';
    }

    private function getBoxCols($template, $box_id)
    {
        $cols = $this->isMobile ? ($template['ct_list_box_mocols'][$box_id] ?? '') : ($template['ct_list_box_pccols'][$box_id] ?? '');
        return $cols ?: ($this->isMobile ? 2 : 4);
    }

    private function getBoxDisplay($template, $box_id)
    {
        $style = $this->isMobile ? ($template['ct_list_box_mostyle'][$box_id] ?? '') : ($template['ct_list_box_pcstyle'][$box_id] ?? '');
        return $style === 'none' ? 'style="display:none !important;"' : '';
    }

    private function getBoxItems($ct_id, $box_id, $table)
    {
        return $this->adminTemplateModel->getTemplateCiBoxItem($table, $ct_id, $box_id, $this->config_domain['cf_id']);
    }
}