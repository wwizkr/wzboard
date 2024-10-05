<?php
//파일위치 src/Service/AdminTemplateService.php
namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\FileUploadManager;
use InvalidArgumentException;

use Web\PublicHtml\Traits\TemplateItemDataTrait;
use Web\PublicHtml\Traits\BaseTemplateServiceTrait;

class AdminTemplateService
{
    use BaseTemplateServiceTrait;
    use TemplateItemDataTrait;
    
    protected $container;
    protected $config_domain;
    protected $formDataMiddleware;
    protected $adminTemplateModel;
    protected $adminBoardsService;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
        $this->adminTemplateModel = $this->container->get('AdminTemplateModel');
    }

    protected function getContainer()
    {
        return $this->container;
    }

    public function getTemplateList(string $table): array
    {
        $result = $this->adminTemplateModel->getTemplateList($table, $this->config_domain['cf_id']);
        return $result;
    }

    public function getTemplateDataById(string $table, int $ctId = null): array
    {
        $result = $this->adminTemplateModel->getTemplateDataById($table, $ctId, $this->config_domain['cf_id']);
        return $result;
    }

    public function templateUpdate(string $table, string $ct_section_id, int $ctId, int $cgId): array
    {
        $data = [];
        
        // 변수 정리
        $ct_list_width = $_POST['ct_list_width'];
        $ct_list_pc_height = $_POST['ct_list_pc_height'];
        if($_POST['ct_list_pc_height'] != 'auto') {
            $ct_list_pc_height = $_POST['ct_list_pc_height'];
        }
        $ct_list_mo_height = $_POST['ct_list_mo_height'];
        if($_POST['ct_list_mo_height'] != 'auto') {
            $ct_list_mo_height = $_POST['ct_list_mo_height'];
        }
        $ct_list_pc_padding = $_POST['ct_list_pc_padding'];
        $ct_list_mo_padding = $_POST['ct_list_mo_padding'];
        $ct_list_bgcolor = $_POST['ct_list_bgcolor'];
        $ct_list_box_cnt = (int)$_POST['ct_list_box_cnt'] ?? 1;
        $ct_list_box_margin = (int)$_POST['ct_list_box_margin'];
        $ct_list_box_wtype = (int)$_POST['ct_list_box_wtype'];
        $ct_admin_subject =  $_POST['ct_admin_subject'];
        $ct_position = $_POST['ct_position'];
        $ct_postion_sub_array = $_POST['ct_position_sub'] ? explode("::",$_POST['ct_position_sub']) : array();
        if(isset($ct_postion_sub_array[0]) && $ct_postion_sub_array[0] == 'all') {
            $ct_position_sub = $ct_postion_sub_array[0];
            $ct_position_subtype  = '';
        } else {
            $ct_position_sub = $ct_postion_sub_array[1] ?? '';
            $ct_position_subtype = $ct_postion_sub_array[0] ?? '';
        }

        $ct_position_subview  = $_POST['ct_position_subview'] ?? 'Y';
        $ct_subject_view      = !empty($_POST['ct_subject_view'])     ? implode(",",$_POST['ct_subject_view']) : '';
        $ct_subject           = !empty($_POST['ct_subject'])          ? implode(",",$_POST['ct_subject']) : '';
        $ct_subject_color     = !empty($_POST['ct_subject_color'])    ? implode(",",$_POST['ct_subject_color']) : '';
        $ct_subject_size      = !empty($_POST['ct_subject_size'])     ? implode(",",$_POST['ct_subject_size']) : '';
        $ct_msubject_size     = !empty($_POST['ct_msubject_size'])    ? implode(",",$_POST['ct_msubject_size']) : '';
        $ct_subject_pos       = !empty($_POST['ct_subject_pos'])      ? implode(",",$_POST['ct_subject_pos']) : '';
        $ct_subject_more_link = !empty($_POST['ct_subject_more_link'])     ? implode(",",$_POST['ct_subject_more_link']) : '';
        $ct_subject_more_url  = !empty($_POST['ct_subject_more_url'])      ? implode(",",$_POST['ct_subject_more_url']) : '';
        $ct_list_box_bgcolor     = !empty($_POST['ct_list_box_bgcolor']) ? implode(",",$_POST['ct_list_box_bgcolor']) : '';
        $ct_list_box_pc_padding  = !empty($_POST['ct_list_box_pc_padding']) ? implode("@",$_POST['ct_list_box_pc_padding']) : '';
        $ct_list_box_mo_padding  = !empty($_POST['ct_list_box_mo_padding']) ? implode("@",$_POST['ct_list_box_mo_padding']) : '';
        $ct_list_box_border_width  = !empty($_POST['ct_list_box_border_width']) ? implode(",",$_POST['ct_list_box_border_width']) : '';
        $ct_list_box_border_color  = !empty($_POST['ct_list_box_border_color']) ? implode(",",$_POST['ct_list_box_border_color']) : '';
        $ct_list_box_border_round  = !empty($_POST['ct_list_box_border_round']) ? implode(",",$_POST['ct_list_box_border_round']) : '';
        $ct_copytext          = !empty($_POST['ct_copytext'])         ? implode(",",$_POST['ct_copytext']) : '';
        $ct_copytext_color    = !empty($_POST['ct_copytext_color'])   ? implode(",",$_POST['ct_copytext_color']) : '';
        $ct_copytext_size     = !empty($_POST['ct_copytext_size'])    ? implode(",",$_POST['ct_copytext_size']) : '';
        $ct_mcopytext_size    = !empty($_POST['ct_mcopytext_size'])   ? implode(",",$_POST['ct_mcopytext_size']) : '';
        $ct_copytext_pos      = !empty($_POST['ct_copytext_pos'])     ? implode(",",$_POST['ct_copytext_pos']) : '';
        $ct_list_box_width    = !empty($_POST['ct_list_box_width'])   ? implode(",",$_POST['ct_list_box_width']) : '';
        $ct_list_box_itemtype = !empty($_POST['ct_list_itemtype'])    ? implode(",",$_POST['ct_list_itemtype']) : '';
        $ct_list_box_shoptype = !empty($_POST['ct_list_shoptype'])    ? implode(",",$_POST['ct_list_shoptype']) : '';
        $ct_list_box_event    = !empty($_POST['ct_list_box_event'])   ? implode(",",$_POST['ct_list_box_event']) : '';
        $ct_list_box_effect   = !empty($_POST['ct_list_box_effect'])  ? implode(",",$_POST['ct_list_box_effect']) : '';
        $ct_list_box_skin     = !empty($_POST['ct_list_box_skin'])    ? implode(",",$_POST['ct_list_box_skin']) : '';
        $ct_list_box_itemcnt  = !empty($_POST['ct_list_itemcnt'])     ? implode(",",$_POST['ct_list_itemcnt']) : '';
        $ct_list_box_pcstyle  = !empty($_POST['ct_list_box_pcstyle']) ? implode(",",$_POST['ct_list_box_pcstyle']) : '';
        $ct_list_box_mostyle  = !empty($_POST['ct_list_box_mostyle']) ? implode(",",$_POST['ct_list_box_mostyle']) : '';
        $ct_list_box_pccols   = !empty($_POST['ct_list_box_pccols']) ? implode(",",$_POST['ct_list_box_pccols']) : '';
        $ct_list_box_mocols   = !empty($_POST['ct_list_box_mocols']) ? implode(",",$_POST['ct_list_box_mocols']) : '';
        $ct_list_box_items    = !empty($_POST['template_items']) ? implode(":",$_POST['template_items']) : '';

        $uploadManager = new FileUploadManager(WZ_STORAGE_PATH . '/template/'.$this->config_domain['cf_id'].'/'.$table, 0644, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        // 배경 이미지 처리
        $ct_list_bgimage = '';
        if (isset($_FILES['ct_list_bgimage']) && !empty($_FILES['ct_list_bgimage']['name'])) {
            $bgImageResult = $uploadManager->handleFileUploads(
                $_FILES['ct_list_bgimage'],
                $ct_list_old_bgimage ?? '',
                $ct_position,
                [isset($ct_list_bgimage_del) && $ct_list_bgimage_del == '1']
            );
            $ct_list_bgimage = $bgImageResult[0] ?? '';
        }

        // PC 이미지 처리
        $ct_subject_pc_image = '';
        if (isset($_FILES['ct_subject_pc_image']) && !empty($_FILES['ct_subject_pc_image']['name'])) {
            $pcImageResult = $uploadManager->handleFileUploads(
                $_FILES['ct_subject_pc_image'],
                $subject_pc_old_image ?? [],
                $ct_position,
                $subject_pc_del_image ?? []
            );
            $ct_subject_pc_image = implode(",", $pcImageResult);
        }

        // 모바일 이미지 처리
        $ct_subject_mo_image = '';
        if (isset($_FILES['ct_subject_mo_image']) && !empty($_FILES['ct_subject_mo_image']['name'])) {
            $mobileImageResult = $uploadManager->handleFileUploads(
                $_FILES['ct_subject_mo_image'],
                $subject_mobile_old_image ?? [],
                $ct_position,
                $subject_mobile_del_image ?? []
            );
            $ct_subject_mo_image = implode(",", $mobileImageResult);
        }

        // 박스 배경 이미지 처리
        $ct_list_box_bgimage = '';
        if (isset($_FILES['ct_list_box_bgimage']) && !empty($_FILES['ct_list_box_bgimage']['name'])) {
            $boxBgImageResult = $uploadManager->handleFileUploads(
                $_FILES['ct_list_box_bgimage'],
                $ct_list_box_old_bgimage ?? [],
                $ct_position,
                $ct_list_box_bgimage_del ?? []
            );
            $ct_list_box_bgimage = implode(",", $boxBgImageResult);
        }
        
        $param['cf_id'] = ['i', $this->config_domain['cf_id']];
        $param['ct_section_id']           = ['s', $ct_section_id];
        $param['ct_admin_subject']        = ['s', $ct_admin_subject];
        $param['ct_position']             = ['s', $ct_position];
        $param['ct_position_sub']         = ['s', $ct_position_sub];
        $param['ct_position_subtype']     = ['s', $ct_position_subtype];
        $param['ct_position_subview']     = ['s', $ct_position_subview];
        $param['ct_subject_view']         = ['s', $ct_subject_view];
        $param['ct_subject']              = ['s', $ct_subject];
        $param['ct_subject_color']        = ['s', $ct_subject_color];
        $param['ct_subject_size']         = ['s', $ct_subject_size];
        $param['ct_msubject_size']        = ['s', $ct_msubject_size];
        $param['ct_subject_pos']          = ['s', $ct_subject_pos];
        $param['ct_copytext']             = ['s', $ct_copytext];
        $param['ct_copytext_color']       = ['s', $ct_copytext_color];
        $param['ct_copytext_size']        = ['s', $ct_copytext_size];
        $param['ct_mcopytext_size']       = ['s', $ct_mcopytext_size];
        $param['ct_copytext_pos']         = ['s', $ct_copytext_pos];
        $param['ct_subject_pc_image']     = ['s', $ct_subject_pc_image];
        $param['ct_subject_mo_image']     = ['s', $ct_subject_mo_image];
        $param['ct_subject_more_link']    = ['s', $ct_subject_more_link];
        $param['ct_subject_more_url']     = ['s', $ct_subject_more_url];
        $param['ct_list_width']           = ['i', $ct_list_width];
        $param['ct_list_pc_height']       = ['s', $ct_list_pc_height];
        $param['ct_list_mo_height']       = ['s', $ct_list_mo_height];
        $param['ct_list_pc_padding']      = ['s', $ct_list_pc_padding];
        $param['ct_list_mo_padding']      = ['s', $ct_list_mo_padding];
        $param['ct_list_bgcolor']         = ['s', $ct_list_bgcolor];
        $param['ct_list_bgimage']         = ['s', $ct_list_bgimage];
        $param['ct_list_box_cnt']         = ['i', $ct_list_box_cnt];
        $param['ct_list_box_effect']      = ['s', $ct_list_box_effect];
        $param['ct_list_box_margin']      = ['i', $ct_list_box_margin];
        $param['ct_list_box_wtype']       = ['i', $ct_list_box_wtype];
        $param['ct_list_box_width']       = ['s', $ct_list_box_width];
        $param['ct_list_box_pc_padding']  = ['s', $ct_list_box_pc_padding];
        $param['ct_list_box_mo_padding']  = ['s', $ct_list_box_mo_padding];
        $param['ct_list_box_border_width']= ['s', $ct_list_box_border_width];
        $param['ct_list_box_border_color']= ['s', $ct_list_box_border_color];
        $param['ct_list_box_border_round']= ['s', $ct_list_box_border_round];
        $param['ct_list_box_bgcolor']     = ['s', $ct_list_box_bgcolor];
        $param['ct_list_box_bgimage']     = ['s', $ct_list_box_bgimage];
        $param['ct_list_box_itemtype']    = ['s', $ct_list_box_itemtype];
        $param['ct_list_box_shoptype']    = ['s', $ct_list_box_shoptype];
        $param['ct_list_box_skin']        = ['s', $ct_list_box_skin];
        $param['ct_list_box_itemcnt']     = ['s', $ct_list_box_itemcnt];
        $param['ct_list_box_pcstyle']     = ['s', $ct_list_box_pcstyle];
        $param['ct_list_box_mostyle']     = ['s', $ct_list_box_mostyle];
        $param['ct_list_box_pccols']      = ['s', $ct_list_box_pccols];
        $param['ct_list_box_mocols']      = ['s', $ct_list_box_mocols];
        $param['ct_list_box_items']       = ['s', $ct_list_box_items];
        if ($table === 'page' && $cgId) {
            $param['cg_id'] = ['i', $cgId];
        }

        $updated = $this->adminTemplateModel->templateUpdate($table, $ctId, $param);
        
        if ($updated['result'] === 'success') {
            $ct_id = $updated['ins_id'];
            if (isset($_POST['ct_list_itemtype']) && !empty($_POST['ct_list_itemtype'])) {
                foreach($_POST['ct_list_itemtype'] as $key=>$val) {
                    $content = $_POST['content'][$key] ?? null;
                    if ($content) {
                        $storagePath = "/storage/editor";
                        $content = CommonHelper::updateStorageImages($content, $storagePath);
                    }

                    $ciData = [
                        'cf_id' => $this->config_domain['cf_id'],
                        'ct_id' => $ct_id,
                        'ci_box_id' => $key,
                        'ci_type' => $val,
                        'ci_link' => $_POST['item_link'][$key] ?? '',
                        'ci_win' => $_POST['item_win'][$key] ?? '',
                        'ci_content' => $content,
                        'options' => []
                    ];

                    // 기존의 데이터를 DB에서 모두 삭제
                    $this->adminTemplateModel->deleteAllTemplateCiBoxItem($table, $ct_id, $key, $this->config_domain['cf_id']);

                    switch($val) {
                        case 'banner':
                            $data['data']['items'] = [];
                            break;
                        case 'image':
                            $result = $this->processedImageItem($table, $ciData, $key, $uploadManager);
                            break;
                        case 'movie':
                            $data['data']['items'] = [];
                            break;
                        case 'outlogin':
                            $data['data']['items'] = [];
                            break;
                        case 'banner':
                            $data['data']['items'] = [];
                            break;
                        case 'board':
                            $ciData['options'] = isset($_POST['template_items'][$key]) ? explode(",",$_POST['template_items'][$key]) : [];
                            $result = $this->processedBoardItem($table, $ciData);
                            break;
                        case 'boardgroup':
                            // 상품필터 게시판 그룹과 같은 그룹형 아이템
                            $ciData['options'] = isset($_POST['item_group'][$key]) && !empty($_POST['item_group'][$key]) ? implode(",",$_POST['item_group'][$key]) : '';
                            $result = $this->processedGroupItem($table, $ciData);
                            break;
                        case 'file':
                            $data['data']['items'] = [];
                            break;
                        default: //editor
                            $result = $this->processedDefaultItem($table, $ciData);
                            break;
                    }
                }
            }
        }

        return $updated;
    }

    private function processedDefaultItem(string $table, array $ciData): void
    {
        $param = [
            'cf_id' => ['i', $ciData['cf_id']],
            'ct_id' => ['i', $ciData['ct_id']],
            'ci_box_id' => ['i', $ciData['ci_box_id']],
            'ci_type' => ['s', $ciData['ci_type']],
            'ci_pc_item' => ['s', ''],
            'ci_mo_item' => ['s', ''],
            'ci_content' => ['s', $ciData['ci_content']],
        ];

        $this->adminTemplateModel->insertTemplateCiBoxItem($table, $param);
    }
    
    // 상품필터 게시판 그룹과 같은 그룹형 아이템
    private function processedGroupItem(string $table, array $ciData): void
    {
        $param = [
            'cf_id' => ['i', $ciData['cf_id']],
            'ct_id' => ['i', $ciData['ct_id']],
            'ci_box_id' => ['i', $ciData['ci_box_id']],
            'ci_type' => ['s', $ciData['ci_type']],
            'ci_pc_item' => ['s', $ciData['options']],
            'ci_mo_item' => ['s', $ciData['options']],
        ];

        $this->adminTemplateModel->insertTemplateCiBoxItem($table, $param);
    }

    private function processedBoardItem(string $table, array $ciData): void
    {
        $unitData = $ciData['options'];
        
        foreach($unitData as $val) {
            $unit = explode("_", $val);
            $board_id = $unit[2] ?? '';
            $param = [
                'cf_id' => ['i', $ciData['cf_id']],
                'ct_id' => ['i', $ciData['ct_id']],
                'ci_box_id' => ['i', $ciData['ci_box_id']],
                'ci_type' => ['s', $ciData['ci_type']],
                'ci_pc_item' => ['s', $board_id],
                'ci_mo_item' => ['s', $board_id]
            ];
            
            $this->adminTemplateModel->insertTemplateCiBoxItem($table, $param);
        }
    }

    private function processedImageItem(string $table, array $ciData, int $key, FileUploadManager $uploadManager): void
    {
        $imageData = $_POST['image_items'][$key] ?? [];
        
        $pc_old_image = $_POST['pc_old_image'][$key] ?? [];
        $mo_old_image = $_POST['mo_old_image'][$key] ?? [];
        $temp_pc_images = $_FILES['temp_pc_image']['name'][$key] ?? [];
        $temp_mo_images = $_FILES['temp_mo_image']['name'][$key] ?? [];

        foreach ($imageData as $index => $val) {
            $pc_image = $pc_old_image[$index] ?? '';
            $mo_image = $mo_old_image[$index] ?? '';

            // PC 이미지 업로드 처리
            if (!empty($temp_pc_images[$index])) {
                $pc_image_file = [
                    'name' => $_FILES['temp_pc_image']['name'][$key][$index],
                    'type' => $_FILES['temp_pc_image']['type'][$key][$index],
                    'tmp_name' => $_FILES['temp_pc_image']['tmp_name'][$key][$index],
                    'error' => $_FILES['temp_pc_image']['error'][$key][$index],
                    'size' => $_FILES['temp_pc_image']['size'][$key][$index]
                ];
                $pc_upload_result = $uploadManager->handleFileUploads($pc_image_file, '', $ciData['ct_id'] . '_pc_' . $index);
                $pc_image = $pc_upload_result[0] ?? $pc_image;
                if (!empty($pc_upload_result[0]) && !empty($pc_old_image[$index])) {
                    $uploadManager->deleteOldFile($pc_old_image[$index]);
                }
            }

            // 모바일 이미지 업로드 처리
            if (!empty($temp_mo_images[$index])) {
                $mo_image_file = [
                    'name' => $_FILES['temp_mo_image']['name'][$key][$index],
                    'type' => $_FILES['temp_mo_image']['type'][$key][$index],
                    'tmp_name' => $_FILES['temp_mo_image']['tmp_name'][$key][$index],
                    'error' => $_FILES['temp_mo_image']['error'][$key][$index],
                    'size' => $_FILES['temp_mo_image']['size'][$key][$index]
                ];
                $mo_upload_result = $uploadManager->handleFileUploads($mo_image_file, '', $ciData['ct_id'] . '_mo_' . $index);
                $mo_image = $mo_upload_result[0] ?? $mo_image;
                if (!empty($mo_upload_result[0]) && !empty($mo_old_image[$index])) {
                    $uploadManager->deleteOldFile($mo_old_image[$index]);
                }
            }

            if ($pc_image) {
                $param = [
                    'cf_id' => ['i', $ciData['cf_id']],
                    'ct_id' => ['i', $ciData['ct_id']],
                    'ci_box_id' => ['i', $key],
                    'ci_type' => ['s', $ciData['ci_type']],
                    'ci_pc_item' => ['s', $pc_image],
                    'ci_mo_item' => ['s', $mo_image],
                    'ci_link' => ['s', $_POST['item_link'][$key][$index] ?? ''],
                    'ci_new_win' => ['i', $_POST['item_win'][$key][$index] ?? 0],
                ];

                $this->adminTemplateModel->insertTemplateCiBoxItem($table, $param);
            }
        }
    }

    // 여기에 AdminTemplateService에 특화된 추가 메서드들을 구현할 수 있습니다.
}