<?php
//파일위치 src/Admin/Service/AdminBannerService.php
namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\FileUploadManager;
use Web\Admin\Model\AdminBannerModel;
use InvalidArgumentException;

class AdminBannerService
{
    protected $container;
    protected $config_domain;
    protected $formDataMiddleware;
    protected $adminBannerModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');

        $this->adminBannerModel = new AdminBannerModel($this->container);
    }
    
    public function getBannerList(string $position = null, int $use = null): array
    {
        $result = $this->adminBannerModel->getBannerList($this->config_domain['cf_id'], $position, $use);
        return $result;
    }

    public function getBannerDataById(int $baId = null): array
    {
        $result = $this->adminBannerModel->getBannerDataById($baId, $this->config_domain['cf_id']);
        return $result;
    }

    public function bannerUpdate(int $baId = null): array
    {
        $formData = $_POST['formData'] ?? null;
        $numericFields = ['ba_new_win', 'ba_order', 'ba_use'];
        $param = $this->formDataMiddleware->handle('admin', $formData, $numericFields);

        $banner = [];
        if ($baId) {
            $banner = $this->getBannerDataById($baId);
            if ($banner['ba_id'] === null) {
                return CommonHelper::jsonResponse([
                    'result' => 'failure',
                    'message' => '배너 정보를 찾을 수 없습니다.',
                    'data' => [],
                ]);
            }
        }

        $uploadManager = new FileUploadManager(WZ_STORAGE_PATH . '/banner/'.$this->config_domain['cf_id'], 0644, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $pc_image = $banner['ba_pc_image'] ?? '';
        $mo_image = $banner['ba_mo_image'] ?? '';
        $bg_image = $banner['ba_bg_image'] ?? '';
        
        // arrayFiles 메소드를 사용하지 않고 직접 파일 배열 재구성
        $files = [];
        if (isset($_FILES['fileData']['name']['banner_image'])) {
            foreach (['pc', 'mo', 'bg'] as $type) {
                if (!empty($_FILES['fileData']['name']['banner_image'][$type])) {
                    $files[$type] = [
                        'name' => $_FILES['fileData']['name']['banner_image'][$type],
                        'type' => $_FILES['fileData']['type']['banner_image'][$type],
                        'tmp_name' => $_FILES['fileData']['tmp_name']['banner_image'][$type],
                        'error' => $_FILES['fileData']['error']['banner_image'][$type],
                        'size' => $_FILES['fileData']['size']['banner_image'][$type],
                    ];
                }
            }
        }

        $oldFiles = [
            'pc' => $pc_image,
            'mo' => $mo_image,
            'bg' => $bg_image,
        ];
        
        $deleteFlags = [
            'pc' => !empty($_POST['banner_del']['pc']),
            'mo' => !empty($_POST['banner_del']['mo']),
            'bg' => !empty($_POST['banner_del']['bg']),
        ];
        
        $position = 'banner';
        
        // 새 파일 업로드 여부 확인
        $newFileUploaded = [
            'pc' => !empty($files['pc']['name']),
            'mo' => !empty($files['mo']['name']),
            'bg' => !empty($files['bg']['name']),
        ];
        
        // 파일 업로드 처리
        $uploadedFiles = $uploadManager->handleFileUploads($files, $oldFiles, $position, $deleteFlags);

        // 결과 처리
        $newFiles = [];
        foreach (['pc', 'mo', 'bg'] as $type) {
            if ($deleteFlags[$type] || $newFileUploaded[$type]) {
                // 삭제 플래그가 설정되었거나 새 파일이 업로드된 경우
                if (!empty($uploadedFiles[$type])) {
                    // 새 파일이 성공적으로 업로드된 경우
                    $newFiles[$type] = $uploadedFiles[$type];
                    // 기존 파일 삭제
                    $uploadManager->deleteOldFile($oldFiles[$type]);
                } else {
                    // 새 파일 업로드 실패 또는 단순 삭제의 경우
                    $newFiles[$type] = '';
                    if ($deleteFlags[$type]) {
                        // 삭제 플래그가 설정된 경우에만 기존 파일 삭제
                        $uploadManager->deleteOldFile($oldFiles[$type]);
                    }
                }
            } else {
                // 변경 없음 (기존 파일 유지)
                $newFiles[$type] = $oldFiles[$type];
            }
        }

        // 결과 사용
        $pc_image = $newFiles['pc'];
        $mo_image = $newFiles['mo'];
        $bg_image = $newFiles['bg'];

        $param['ba_pc_image'] = ['s', $pc_image];
        $param['ba_mo_image'] = ['s', $mo_image];
        $param['ba_bg_image'] = ['s', $bg_image];

        $updated = $this->adminBannerModel->updateBanner($this->config_domain['cf_id'], $baId, $param);

        return $updated;
    }

    public function processedBannerData(array $data, string $style = 'slide'): array
    {
        $bannerData = [];

        $bannerData['pc_image'] = '';
        $bannerData['mo_image'] = '';
        $bannerData['bg_image'] = '';
        $bannerData['pcImage'] = '';
        $bannerData['moImage'] = '';
        if ($data['ba_pc_image'] && file_exists(WZ_STORAGE_PATH.'/banner/'.$data['cf_id'].'/'.$data['ba_pc_image'])) {
            $bannerData['pc_image'] = WZ_STORAGE_DIR.'/banner/'.$data['cf_id'].'/'.$data['ba_pc_image'];
        }
        if ($data['ba_mo_image'] && file_exists(WZ_STORAGE_PATH.'/banner/'.$data['cf_id'].'/'.$data['ba_mo_image'])) {
            $bannerData['mo_image'] = WZ_STORAGE_DIR.'/banner/'.$data['cf_id'].'/'.$data['ba_mo_image'];
        }
        if (!$bannerData['pc_image'] && !$bannerData['mo_image'] && !$data['ba_utv_url']) {
            return $bannerData;
        }
        if (!$bannerData['pc_image'] && $bannerData['mo_image']) {
            $bannerData['pc_image'] = $bannerData['mo_image'];
        }
        if (!$bannerData['mo_image']) {
            $bannerData['mo_image'] = $bannerData['pc_image'];
        }
        if ($bannerData['bg_image'] && file_exists(WZ_STORAGE_PATH.'/banner/'.$data['cf_id'].'/'.$data['ba_bg_image'])) {
            $bannerData['bg_image'] = $bannerData['bg_image'];
        }

        $newWin = $data['ba_new_win'] ? '_blank' : '_self';
        $pcBgcolor = $data['ba_pc_bgcolor'] ? 'style="background-color:'.$data['ba_pc_bgcolor'].'";' : '';
        $moBgcolor = $data['ba_mo_bgcolor'] ? 'style="background-color:'.$data['ba_mo_bgcolor'].'";' : '';
        $bannerData['pcImage'] = '<div class="banner-image" '.$pcBgcolor.'><img src="'.$bannerData['pc_image'].'" style="width:100%;height:auto;" alt="'.$data['ba_title'].'"></div>';
        $bannerData['moImage'] = '<div class="banner-image" '.$pcBgcolor.'><img src="'.$bannerData['mo_image'].'" style="width:100%;height:auto;" alt=""></div>';
        if ($data['ba_link']) {
            $bannerData['pcImage'] = '<div class="banner-image"><a href="'.$data['ba_link'].'" target="'.$newWin.'"><img src="'.$bannerData['pc_image'].'" style="width:100%;height:auto;" alt="'.$data['ba_title'].'"></a></div>';
            $bannerData['moImage'] = '<div class="banner-image"><a href="'.$data['ba_link'].'" target="'.$newWin.'"><img src="'.$bannerData['mo_image'].'" style="width:100%;height:auto;" alt=""></a></div>';
        }
        $bannerData['utvUrl'] = $data['ba_utv_url'];
        $bannerData['swiperSlide'] = $style === 'slide' ? 'swiper-slide' : '';

        return $bannerData;
    }
}