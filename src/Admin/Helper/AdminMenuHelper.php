<?php
// 파일 위치: /src/Admin/Helper/AdminMenuHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Model\AdminSettingsModel;
use Web\Admin\Model\AdminBoardsModel;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

class AdminMenuHelper
{
    private $container;
    private $adminSettingsModel;
    private $adminBoardsModel;
    private $db;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
        $this->adminSettingsModel = new AdminSettingsModel($this->container);
        $this->adminBoardsModel = new AdminBoardsModel($this->container);
    }

    public static function getAdminMenu()
    {
        return [
            'dashboard' => [
                'label' => '대쉬보드',
                'url' => '/admin',
                'icon' => 'bi-speedometer2',
            ],
            'settings' => [
                'label' => '환경설정',
                'url' => '/admin/settings/general',
                'icon' => 'bi-gear',
                'submenu' => [
                    'general' => [
                        'label' => '기본 환경설정',
                        'url' => '/admin/settings/general',
                    ],
                    'menus' => [
                        'label' => '메뉴 설정',
                        'url' => '/admin/settings/menus',
                    ],
                    'template' => [
                        'label' => '메인화면/페이지 관리',
                        'url' => '/admin/template/list',
                    ],
                    'clause' => [
                        'label' => '이용약관 관리',
                        'url' => '/admin/clause/list',
                    ],
                ],
            ],
            'members' => [
                'label' => '회원관리',
                'url' => '/admin/members/all',
                'icon' => 'bi-people',
                'submenu' => [
                    'all' => [
                        'label' => '전체 회원',
                        'url' => '/admin/members/list',
                    ],
                    'add' => [
                        'label' => '회원 등록',
                        'url' => '/admin/members/add',
                    ],
                    'level' => [
                        'label' => '회원 등급관리',
                        'url' => '/admin/members/add',
                    ],
                ],
            ],
            'boards' => [
                'label' => '게시판관리',
                'url' => '/admin/boardadmin/boards',
                'icon' => 'bi-people',
                'submenu' => [
                    'group' => [
                        'label' => '게시판 그룹관리',
                        'url' => '/admin/boardadmin/group',
                    ],
                    'categories' => [
                        'label' => '게시판 카테고리',
                        'url' => '/admin/boardadmin/category',
                    ],
                    'boards' => [
                        'label' => '게시판 관리',
                        'url' => '/admin/boardadmin/boards',
                    ],
                ],
            ],
            'trial' => [
                'label' => '문제 관리',
                'url' => '/admin/trialadmin/configs',
                'icon' => 'bi-people',
                'submenu' => [
                    'prompt' => [
                        'label' => '문제 프롬포트',
                        'url' => '/admin/trialadmin/prompt',
                    ],
                    'subject' => [
                        'label' => '문제 과목 관리',
                        'url' => '/admin/trialadmin/subject',
                    ],
                    'categories' => [
                        'label' => '카테고리 관리',
                        'url' => '/admin/trialadmin/category',
                    ],
                    'list' => [
                        'label' => '문제 관리',
                        'url' => '/admin/trialadmin/list',
                    ],
                ],
            ],
            'reports' => [
                'label' => 'Reports',
                'url' => '/admin/reports',
                'icon' => 'bi-bar-chart',  // Bootstrap Icons 리포트 아이콘
            ],
        ];
    }

    public function setMenuCategory()
    {
        /*
         * 게시판 메뉴 생성
         */
        $boards = [];
        $boardData = $this->adminBoardsModel->getBoardsConfig(null);
        if (!empty($boardData)) {
            foreach($boardData as $key=>$val) {
                $boards[$key]['me_cate2'] = $val['board_id'];
                $boards[$key]['me_name'] = $val['board_name'];
                $boards[$key]['me_title'] = $val['board_name'];
                $boards[$key]['me_link'] = '/board/'.$val['board_id'].'/list';
            }
        }
        
        /*
         * 문제은행 메뉴 생성
         */
        $subject = [];
        $result = $this->db->sqlBindQuery('select', 'trial_subject',[],[]);
        $n = 0;
        if (!empty($result)) {
            foreach($result as $key=>$val) {
                $subject[$n]['me_cate2'] = $val['no'];
                $subject[$n]['me_name'] = $val['subject_name'];
                $subject[$n]['me_title'] = $val['subject_name'];
                $subject[$n]['me_link'] = '/trial/list?subject='.$val['subject_name'];
                $n++;
                // 과목 카테고리
                $param = [];
                $where['subject_no'] = ['i', $val['no']];
                $option = ['order' => 'category asc'];

                $sub = $this->db->sqlBindQuery('select', 'trial_category', $param, $where, $option);
                if (!empty($sub)) {
                    foreach($sub as $subKey => $subVal) {
                        $subject[$n]['me_cate2'] = $subVal['category'];
                        $subject[$n]['me_name'] = $val['subject_name'].' > '.$subVal['category_name'];
                        $subject[$n]['me_title'] = $subVal['category_name'];
                        $subject[$n]['me_link'] = '/trial/list?subject='.$val['subject_name'].'&category='.$subVal['category_name'];
                        $n++;
                    }
                }
                unset($param);
                unset($where);
                unset($option);
            }
        }

        $menuCategory = [
            'boards' => ['title' => '게시판', 'children' => $boards],
            'trial' => ['title' => '문제은행', 'children' => $subject],
            'page' => ['title' => '페이지', 'children' => []],
            'direct' => ['title' => '직접입력', 'children' => []],
        ];

        return $menuCategory;
    }
}