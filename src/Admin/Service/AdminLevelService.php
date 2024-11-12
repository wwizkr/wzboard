<?php
//파일위치 src/Service/AdminSettingService.php

namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\Admin\Model\AdminLevelModel;
use Web\Admin\Helper\AdminCommonHelper;

class AdminLevelService
{
    protected $container;
    protected $config_domain;
    protected $adminLevelModel;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminLevelModel = new AdminLevelModel($this->container);

        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
    }

    /**
     * 회원 레벨 데이터를 가져옵니다.
     *
     * @return array 회원 레벨 목록
     */
    public function getMemberLevelData($level = false, $level_use = 1, $sort='ASC')
    {
        $levelData = [];
        $result = $this->adminLevelModel->getMemberLevelData($level, $level_use, $sort);
        if (count($result) > 1) {
            foreach($result as $key=>$val) {
                $levelData[$val['level_id']] = $val;
            }
        } else {
            $levelData = $result[0] ?? [];
        }

        return $levelData;
    }

    /*
    public function memberLevel()
    {
        $level = $this->getLevelData();
        $level = array_reverse($level);

        // 뷰에 전달할 데이터 구성
        $viewData = [
            'title' => '회원 등급관리',
            'levelData' => $level,
        ];

        return [
            'viewPath' => 'Members/memberLevel',
            'viewData' => $viewData,
        ];
    }
    */

    /**
     * 회원 레벨데이터를 level_id => level_name 배열로 가공
     *
     */
    public function formatLevelDataArray(array $level): array
    {
        $levelData = [];
        foreach ($level as $val) {
            $levelData[$val['level_id']] = $val['level_name'];
        }
        return $levelData;
    }

    public function memberLevelModify()
    {
        $levelData = isset($_POST['level']) && !empty($_POST['level']) ? $_POST['level'] : [];
        
        $result = [];
        foreach($levelData as $key => $val) {
            $level = commonHelper::pickNumber($val);
            if ($level == 10) {
                continue;
            }
            
            $data['level_name'] = ['s', $_POST['level_name'][$level] ?? ''];
            $data['description'] = ['s', $_POST['description'][$level] ?? ''];
            $data['min_point'] = [
                'i', 
                isset($_POST['min_point'][$level]) 
                    ? commonHelper::pickNumber($_POST['min_point'][$level]) 
                    : 0
            ];

            $data['min_posts'] = [
                'i', 
                isset($_POST['min_posts'][$level]) 
                    ? commonHelper::pickNumber($_POST['min_posts'][$level]) 
                    : 0
            ];

            $data['min_comments'] = [
                'i', 
                isset($_POST['min_comments'][$level]) 
                    ? commonHelper::pickNumber($_POST['min_comments'][$level]) 
                    : 0
            ];

            $data['min_login_count'] = [
                'i', 
                isset($_POST['min_login_count'][$level]) 
                    ? commonHelper::pickNumber($_POST['min_login_count'][$level]) 
                    : 0
            ];

            $data['min_days_join'] = [
                'i', 
                isset($_POST['min_days_join'][$level]) 
                    ? commonHelper::pickNumber($_POST['min_days_join'][$level]) 
                    : 0
            ];

            $data['purchase_amount'] = [
                'i', 
                isset($_POST['purchase_amount'][$level]) 
                    ? commonHelper::pickNumber($_POST['purchase_amount'][$level]) 
                    : 0
            ];

            $data['auto_level_up'] = [
                'i', 
                isset($_POST['auto_level_up'][$level]) 
                    ? commonHelper::pickNumber($_POST['auto_level_up'][$level]) 
                    : 0
            ];

            $data['level_up_point'] = [
                'i', 
                isset($_POST['level_up_point'][$level]) 
                    ? commonHelper::pickNumber($_POST['level_up_point'][$level]) 
                    : 0
            ];

            $data['level_use'] = [
                'i', 
                isset($_POST['level_use'][$level]) 
                    ? commonHelper::pickNumber($_POST['level_use'][$level]) 
                    : 0
            ];

            $data['is_admin'] = [
                'i', 
                isset($_POST['is_admin'][$level]) 
                    ? commonHelper::pickNumber($_POST['is_admin'][$level]) 
                    : 0
            ];

            $updated = $this->adminLevelModel->memberLevelModify($level, $data);
        }
        
        return;
    }

    public function getAdminAuthData()
    {
        $result = $this->adminLevelModel->getAdminAuthData();

        $data = [];

        foreach($result as $key=>$val) {
            $data[$val['level_id']][] = $val;
        }

        return $data;
    }

    public function memberAuthUpdate()
    {
        $level_id = isset($_POST['level_id']) ? commonHelper::pickNumber($_POST['level_id']) : 0;
        $menuCate = commonHelper::validateParam('menuCate', 'string', null, null, INPUT_POST);
        $menuCode = isset($_POST['menuCode']) ? $_POST['menuCode'] : [];
        
        $menuAuth = [];
        if (!empty($menuCode)) {
            foreach($menuCode as $key=>$val) {
                $menuAuthArray = isset($_POST['menuAuth'][$key]) ? $_POST['menuAuth'][$key] : [];
                if (!empty($menuAuthArray)) {
                    $menuAuth = implode(",", $menuAuthArray);
                    $this->adminLevelModel->memberAuthUpdate((int)$level_id, $menuCate, $val, $menuAuth);
                }
            }
        }

        return [
            'level_id' => $level_id,
            'menuCate' => $menuCate,
            'menuCode' => $menuCode,
        ];
    }

    public function memberAuthDelete(int $no)
    {
        $menuCode = commonHelper::validateParam('menuCode', 'string', null, null, INPUT_GET);
        return $this->adminLevelModel->memberAuthDelete((int)$no, $menuCode);
    }

    public function memberAuthListUpdate(string $action)
    {
        $itemNo = isset($_POST['itemNo']) ? $_POST['itemNo'] : [];
        
        $menuAuth = [];
        if (!empty($itemNo)) {
            foreach($itemNo as $key=>$val) {
                $authData = $this->adminLevelModel->getAdminAuthDataByNo((int)$val);
                if (empty($authData)) {
                    continue;
                }
                $menuAuthArray = isset($_POST['menuAuth'][$key]) ? $_POST['menuAuth'][$key] : [];
                if (!empty($menuAuthArray)) {
                    $menuAuth = implode(",", $menuAuthArray);

                    if ($action === 'update') {
                        $this->adminLevelModel->memberAuthUpdate((int)$authData['level_id'], $authData['menuCate'], $authData['menuCode'], $menuAuth);
                    }

                    if ($action === 'delete') {
                        $this->adminLevelModel->memberAuthDelete((int)$authData['no'], $authData['menuCode']);
                    }
                }
            }
        }

        return [
            'authData' => $authData,
        ];
    }

    public function processedAdminAction($action, $level, $activeCode)
    {
        $result = $this->adminLevelModel->getAdminActionAuthData($level, $activeCode);

        if (empty($result)) {
            return false;
        }

        $allowedAction = $result['menuAuth'] ? explode(",", $result['menuAuth']) : [];
        return in_array($action, $allowedAction);
    }
}