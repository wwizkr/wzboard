<?php

namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

class AdminBoardsService
{
    protected $container;
    protected $adminBoardsModel;
    protected $adminBoardsHelper;
    protected $config_domain;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminBoardsModel = $this->container->get('AdminBoardsModel');
        $this->adminBoardsHelper = $this->container->get('AdminBoardsHelper');
    }
    
    // ---------------------------
    // 그룹 관리
    // ---------------------------
    public function getBoardsGroup($group_id='', $levelData = [])
    {
        $groupData = $this->adminBoardsModel->getBoardsGroup($group_id);

        $groupList = $this->processedAddLevelSelectData($groupData, $levelData);

        return $groupList;
    }

    /**
     * 게시판 그룹 데이터를 no => group_name 배열로 가공
     *
     */
    public function formatGroupDataArray(array $boardGroup): array
    {
        $boardGroupData = [];
        foreach ($boardGroup as $val) {
            $boardGroupData[$val['no']] = $val['group_name'];
        }
        return $boardGroupData;
    }

    public function insertBoardsGroup($data)
    {
        return $this->adminBoardsModel->insertBoardsGroup($data);
    }

    public function updateBoardsGroup($group_no, $data)
    {
        return $this->adminBoardsModel->updateBoardsGroup($group_no, $data);
    }
    
    // ---------------------------
    // 전체 카테고리 OR 개별 카테고리 정보
    // ---------------------------
    public function getCategoryData($category_no='', $levelData = [])
    {
        $categoryData = $this->adminBoardsModel->getCategoryData($category_no);

        $categoryList = $this->processedAddLevelSelectData($categoryData, $levelData);

        return $categoryList;
    }
    
    // 리스트에 레벨선택 셀렉트 박스를 추가
    private function processedAddLevelSelectData($listData, $levelData)
    {
        $list = [];
        foreach($listData as $key=>$val) {
            $list[$key] = $val;
            $list[$key]['levelSelect'] = [
                'list_level' => CommonHelper::makeSelectBox('listData[list_level]', $levelData , $val['list_level'], 'list_level_'.$key, 'frm_input frm_full', '비회원'),
                'read_level' => CommonHelper::makeSelectBox('listData[read_level]', $levelData , $val['read_level'], 'read_level_'.$key, 'frm_input frm_full', '비회원'),
                'write_level' => CommonHelper::makeSelectBox('listData[write_level]', $levelData , $val['write_level'], 'write_level_'.$key, 'frm_input frm_full', '비회원'),
                'comment_level' => CommonHelper::makeSelectBox('listData[comment_level]', $levelData , $val['comment_level'], 'commnent_level_'.$key, 'frm_input frm_full', '비회원'),
                'download_level' => CommonHelper::makeSelectBox('listData[download_level]', $levelData , $val['download_level'], 'download_level_'.$key, 'frm_input frm_full', '비회원'),
            ];
        }

        return $list;
    }

    public function insertBoardsCategory($data)
    {
        $result = $this->adminBoardsModel->checkBoardsCategoryName($data['category_name']);
        if($result === false) {
            $jsonData = [
                'result' => 'failer',
                'message' => '카테고리명이 중복되었습니다.'
            ];
            return $jsonData;
        }

        return $this->adminBoardsModel->insertBoardsCategory($data);
    }

    public function updateBoardsCategory($category_no, $data)
    {
        return $this->adminBoardsModel->updateBoardsCategory($category_no, $data);
    }

    // ---------------------------
    // 게시판 관리
    // ---------------------------

    public function getBoardsList($levelData = [])
    {
        // 게시판 그룹 배열
        $boardsGroup = $this->getBoardsGroup();
        $boardGroupData = $this->formatGroupDataArray($boardsGroup);

        // 기본 설정 로드
        $config = [
            'cf_page_rows' => isset($_GET['pagenum']) && $_GET['pagenum'] > 0 ? CommonHelper::pickNumber($_GET['pagenum']) : $this->config_domain['cf_page_rows'],
            'cf_page_nums' => $this->config_domain['cf_page_nums']
        ];

        // 허용된 필터와 정렬 필드 정의
        $allowedFilters = ['board_id', 'board_name'];
        $allowedSortFields = ['no', 'signup_date'];

        // 추가 파라미터 설정 'status' => ['string', 'all', ['all', 'active', 'inactive']]
        $additionalParams = [];
        if (isset($_GET['searchData']) && is_array($_GET['searchData'])) {
            foreach($_GET['searchData'] as $key => $val) {
                $type = 'string'; // 기본 타입을 string으로 설정
                $allowed = []; // 기본적으로 빈 배열로 설정

                if ($key === 'member_level') {
                    $allowed = !empty($levelData) ? array_keys($levelData) : [];
                }

                $additionalParams[$key] = [$type, $val, $allowed];
            }
        }

        // 목록 파라미터 가져오기
        $params = CommonHelper::getListParameters($config, $allowedFilters, $allowedSortFields, $additionalParams);

        // 총 게시판 수
        $totalItems = $this->getTotalBoardCount($params['search'], $params['filter'], $params['additionalQueries']);

        // 게시판 목록 데이터 조회
        $boardsData = $this->adminBoardsModel->getBoardListData(
            $params['page'],
            $params['page_rows'],
            $params['search'],
            $params['filter'],
            $params['sort'],
            $params['additionalQueries']
        );

        // 게시판 스킨 목록
        $skinDir = $this->adminBoardsHelper->getBoardSkinDir();
        $boardSkin = [];
        foreach($skinDir as $key => $val) {
            $boardSkin[$val['name']] = $val['name'];
        }

        $boardList = [];
        foreach($boardsData as $key => $val) {
            $boardList[$key] = $val;
            $boardList[$key]['group_name'] = $boardGroupData[$val['group_no']];
            $boardList[$key]['groupSelect'] = CommonHelper::makeSelectBox(
                'listData[group_no]['.$key.']',
                $boardGroupData ?? [],
                $val['group_no'] ?? '',
                'group_no_'.$key,
                'frm_input frm_full',
                '그룹선택'
            );
            $boardList[$key]['skinSelect'] = CommonHelper::makeSelectBox(
                'listData[board_skin]['.$key.']',
                $boardSkin ?? [],
                $val['board_skin'] ?? '',
                'board_skin_'.$key,
                'frm_input frm_full',
                '스킨선택'
            );

            $boardList[$key]['levelSelect'] = [
                'list_level' => CommonHelper::makeSelectBox('listData[list_level]', $levelData , $val['list_level'], 'list_level_'.$key, 'frm_input frm_full', '비회원'),
                'read_level' => CommonHelper::makeSelectBox('listData[read_level]', $levelData , $val['read_level'], 'read_level_'.$key, 'frm_input frm_full', '비회원'),
                'write_level' => CommonHelper::makeSelectBox('listData[write_level]', $levelData , $val['write_level'], 'write_level_'.$key, 'frm_input frm_full', '비회원'),
                'comment_level' => CommonHelper::makeSelectBox('listData[comment_level]', $levelData , $val['comment_level'], 'commnent_level_'.$key, 'frm_input frm_full', '비회원'),
                'download_level' => CommonHelper::makeSelectBox('listData[download_level]', $levelData , $val['download_level'], 'download_level_'.$key, 'frm_input frm_full', '비회원'),
            ];
        }

        return [
            'params' => $params,
            'totalItems' => $totalItems,
            'boardList' => $boardList,
        ];
    }

    public function getTotalBoardCount($searchQuery, $filters, $additionalQueries)
    {
        return $this->adminBoardsModel->getTotalBoardCount($searchQuery, $filters, $additionalQueries);
    }


    // 개별 게시판 설정 정보
    public function getBoardsConfig($board_id='')
    {
        return $this->adminBoardsModel->getBoardsConfig($board_id);
    }
    
    // 개별 게시판 설정 정보
    public function getBoardsConfigByNo(int $board_no)
    {
        return $this->adminBoardsModel->getBoardsConfigByNo($board_no);
    }

    public function insertBoardsConfig($data)
    {
        $category = explode("-",$data['categories'][1]) ?? null;
        unset($data['categories']); //$data에서 categories 제거

        $insert = $this->adminBoardsModel->insertBoardsConfig($data);
        if($insert['ins_id']) {
            if(!empty($category)) {
                $this->adminBoardsModel->updateBoardsCategoryMapping($insert['ins_id'], $data['board_id'][1], $category);
            }
            return $insert['ins_id'];
        } else {
            return false;
        }
    }

    public function updateBoardsConfig($board_no, $data)
    {
        $category = explode("-",$data['categories'][1]) ?? null;
        unset($data['categories']); //$data에서 categories 제거

        $update = $this->adminBoardsModel->updateBoardsConfig($board_no, $data);

        if(!empty($category)) {
            $this->adminBoardsModel->updateBoardsCategoryMapping($board_no, $data['board_id'][1], $category);
        }

        return $update;
    }

    public function getBoardsCategoryMapping($board_no)
    {
        return $this->adminBoardsModel->getBoardsCategoryMapping($board_no);
    }
}