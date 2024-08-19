<?php
// 파일 위치: /src/Admin/Controller/SettingsController.php

namespace Web\Admin\Controller;

use Web\PublicHtml\Model\SettingsModel;
use Web\PublicHtml\Service\SettingsService;
use Web\PublicHtml\Helper\DependencyContainer;

class SettingsController
{
    protected $container;
    protected $settingsModel;
    protected $settingsService;

    // 생성자에서 DependencyContainer와 SettingsService를 받아옴
    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $settingsModel = new SettingsModel($container); // SettingsModel 인스턴스 생성
        $this->settingsService = new SettingsService($settingsModel); // SettingsService에 SettingsModel 주입
    }

    public function index() //비어있음
    {
        $viewData = [
            'title' => 'Index',
            'content' => 'This is the user list.'
        ];

        return ['Settings/index', $viewData];
    }

    public function general()
    {
        // 컨테이너에서 cf_id를 가져옴
        $cf_id = $this->container->get('cf_id');

        // 환경설정을 가져옴
        $config_domain = $this->settingsService->getGeneralSettings($cf_id);

        // 탭배열
        $anchor = [
            'anc_cf_basic' => '홈페이지 정보',
            'anc_cf_layout' => '레이아웃 설정',
            'anc_cf_member' => '회원 설정',
            'anc_cf_seo' => 'SEO/스크립트 설정',
            'anc_cf_etc' => '기타 설정',
        ];

        //기본 스킨 정보 설정 == 스킨 디렉토리 목록을 불러오는 Helper 필요 => 관리자에서만 사용, 관리자 Helper로 등록. 차후 설정.
        $skin = [
            'index' => '메인화면 스킨','홈페이지 메인화면 스킨',
            'header' => '상단 스킨',
            'footer' => '하단 스킨',
            'layout' => '레이아웃 스킨', //이건 거의 필요없을 것 같긴 하다.
            'content' => '내용 스킨', //content에 들어가는 스킨명을 통일, 게시판 또는 차후 쇼핑몰 추가 시 페이지별 스킨 추가 생성 가능(ex=> list,view,cart.... 등으로 skin을 세분화 가능하게 함)
            'assets' => 'css, javascript', //이것도 편의를 위해 미리 생성
        ];
        
        // SNS 목록
        $seo_sns = [
            'naver_blog'=>'네이버 블로그,ex)https://blog.naver.com/myblog',
            'naver_post'=>'네이버 포스트,ex)https://post.naver.com/mypost',
            'naver_know'=>'네이버 지식인,ex)https://kin.naver.com/profile/index.naver?u=XXXXXXXXXXXXXXXXXXXXXXXXX',
            'naver_modoo'=>'네이버 모두홈페이지,ex)https://myhomepage.modoo.at ',
            'sns_utv'=>'유튜브,ex)https://www.youtube.com/@mychannel',
            'sns_facebook'=>'페이스북,ex)https://www.facebook.com/myfacebook',
            'sns_twitter'=>'트위터,ex)https://twitter.com/mytwitter',
            'sns_insta'=>'인스타그램,ex)https://www.instagram.com/myinsta',
            'sns_kakaostory'=>'카카오스토리,ex)https://story.kakao.com/mystory',
            'sns_tistory'=>'티스토리,ex)https://mytistory.tistory.com',
        ];

        $viewData = [
            'title' => '기본환경 설정',
            'content' => 'This is the general settings.',
            'config_domain' => $config_domain, // 환경설정 데이터를 viewData에 포함
            'anchor' => $anchor,
            'skin' => $skin,
            'seo_sns' => $seo_sns,
        ];

        return ['Settings/general', $viewData];
    }
}