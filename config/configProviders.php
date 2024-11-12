<?php
// 파일 위치: /config/configProvider.php

use Web\PublicHtml\Core\DatabaseQuery;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;
use Web\PublicHtml\Helper\ConfigHelper;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

class ConfigProvider
{
    private $config = [];
    private $container;
    private $db;

    public function __construct(DatabaseQuery $db)
    {
        $this->db = $db;
        $this->loadConfigs();
    }

    private function loadConfigs()
    {
        $this->loadDomainConfig();
        $this->config['plugin'] = $this->getPluginDir();
        $this->config['image'] = $this->getImageUrl();
        $this->config['template'] = $this->getTemplateItems();
        $this->config['widget'] = [
            'kinds' => $this->getWidgetKinds(),
            'positions' => $this->getWidgetPositions(),
        ];
        $this->config['clauseType'] = $this->getClauseType();
        $this->config['clauseKind'] = $this->getClauseKind();
        $this->config['memberStatus'] = $this->getMemberStatus();
        $this->config['emailDoamin'] = $this->getEmailDomain();
        // 추가 설정들을 여기에 로드...
    }

    private function loadDomainConfig()
    {
        $host = preg_replace('/^www\./', '', $_SERVER["SERVER_NAME"]);
        $owner_domain = implode(".", array_filter(explode(".", $host)));

        CacheHelper::initialize($owner_domain);

        $configCacheKey = 'config_domain_' . $owner_domain;
        $config_domain_data = CacheHelper::getCache($configCacheKey);

        if ($config_domain_data === null) {
            $query = "SELECT * FROM " . (new class { use DatabaseHelperTrait; })->getTableName('config_domain') . " WHERE cf_domain = :cf_domain";
            $stmt = $this->db->query($query, ['cf_domain' => $owner_domain]);
            $config_domain_data = $this->db->fetch($stmt);

            if ($config_domain_data) {
                $encryptedData = CryptoHelper::encryptJson($config_domain_data);
                CacheHelper::setCache($configCacheKey, $encryptedData);
            } else {
                $config_domain_data = [];
            }
        } else {
            $config_domain_data = CryptoHelper::decryptJson($config_domain_data);
        }

        $this->setDeviceSpecificConfig($config_domain_data);

        ConfigHelper::setConfig('config_domain', $config_domain_data);
    }

    private function setDeviceSpecificConfig(&$config_domain_data)
    {
        $isMobile = isset($_SERVER['HTTP_USER_AGENT']) ? preg_match('/(Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini)/i', $_SERVER['HTTP_USER_AGENT']) : 0;

        if ($isMobile) {
            $config_domain_data['cf_page_rows'] = $config_domain_data['cf_mo_page_rows'];
            $config_domain_data['cf_page_nums'] = $config_domain_data['cf_mo_page_nums'];
            $config_domain_data['device_type'] = 'mo';
        } else {
            $config_domain_data['cf_page_rows'] = $config_domain_data['cf_pc_page_rows'];
            $config_domain_data['cf_page_nums'] = $config_domain_data['cf_pc_page_nums'];
            $config_domain_data['device_type'] = 'pc';
        }
    }

    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    private function getPluginDir()
    {
        return [
            'trial',
        ];
    }

    private function getImageUrl()
    {
        return [
            'noImg100' => '/assets/images/no_image100.jpg',
            'noImg430' => '/assets/images/no_image430.jpg',
            'noProfile' => '/assets/images/no_profile.gif',
        ];
    }

    private function getTemplateItems()
    {
        return [
            'template_position' => [
                'index' => '메인화면',
                'left' => '왼쪽사이드바',
                'right' => '오른쪽사이드바',
                'subtop' => '서브페이지상단',
                'subfoot' => '서브페이지하단',
                'pagetop' => '내용상단',
                'pagefoot' => '내용하단'
            ],
            'template_items' => [
                'banner' => '배너',
                'image' => '이미지',
                'movie' => '동영상',
                'outlogin' => '아웃로그인',
                'board' => '게시판 최신글',
                'boardgroup' => '게시판 그룹',
                'editor' => '에디터직접입력',
                'file' => '파일등록'
            ],
            'page_position' => [
                'content'=>'페이지 중앙',
                'left'=>'페이지 좌측',
                'right'=>'페이지 우측',
            ],
            'page_type' => [
                1=>'전체 레이아웃',
                2=>'2단 좌측 레이아웃',
                3=>'2단 우측 레이아웃',
                4=>'3단 레이아웃'
            ],
            'aos_effect' => [
                'fade-up'=>'아래애서 위로',
                'fade-down'=>'위에서 아래',
                'fade-right'=>'왼쪽에서 오른쪽',
                'fade-left'=>'오른쪽에서 왼쪽',
                'flip-left'=>'왼쪽 뒤집기',
                'flip-right'=>'오른쪽 뒤집기',
                'zoom'=>'줌인'
            ],
        ];
    }

    private function getWidgetKinds()
    {
        return [
            'today' => ['name' => '오늘본상품', 'type' => 'today', 'target' => '_self', 'url' => ''],
            'kakao' => ['name' => '카카오톡상담', 'type' => 'kakao', 'target' => '_blank', 'url' => ''],
            'callcenter' => ['name' => '상담신청폼', 'type' => 'callcenter', 'target' => 'modal', 'url' => ''],
            'banner' => ['name' => '배너형직접등록', 'type' => 'banner', 'target' => '_self', 'url' => '클릭 시 이동할 주소를 입력해 주세요']
        ];
    }

    private function getWidgetPositions()
    {
        return [
            'PC-LEFT' => ['title' => 'PC용 왼쪽', 'field' => 'left_widget'],
            'PC-RIGHT' => ['title' => 'PC용 오른쪽', 'field' => 'right_widget'],
            'MOBILE-FIX' => ['title' => '모바일 하단', 'field' => 'mobile_fix_widget'],
        ];
    }

    private function getClauseType()
    {
        return [
            'site'=>'이용약관',
            'member'=>'회원가입',
            'order'=>'신청서작성',
            'call'=>'상담신청',
        ];
    }

    private function getClauseKind()
    {
        return [
            1 => '필수',
            2 => '선택',
            3 => '안내',
        ];
    }

    private function getMemberStatus()
    {
        return [
        ];
    }

    private function getEmailDomain()
    {
        return [
        ];
    }

    private function getColorSet()
    {
        return [
            'bgColor' => [
                'title' => '기본배경색상',
                'desc' => '홈페이지에 기본 적용되는 배경색상입니다.',
                'default' => 'colorwhite',
            ],
            'basicFontColor' => [
                'title' => '기본 글자색상',
                'desc' => '기본 글자 색상입니다.',
                'default' => 'colorblack',
            ],
            'mainColor' => [
                'title' => '메인색상',
                'desc' => '글자 강조, 버튼 색상 등, 중요 부분에 사용되는 메인색상입니다.',
                'default' => 'colorred',
            ],
            'pcHeaderBgColor' => [
                'title' => 'PC 상단 배경 색상',
                'desc' => 'PC에 사용되는 상단의 배경색상입니다.',
                'default' => 'colorwhite',
            ],
            'pcHeaderFtColor' => [
                'title' => 'PC 상단 글자 색상',
                'desc' => 'PC 상단의 메뉴 등에 사용되는 글자 색상입니다.',
                'default' => 'colorblack',
            ],
            'pcFooterBgColor' => [
                'title' => 'PC 하단 배경 색상',
                'desc' => 'PC에 사용되는 하단의 배경색상입니다.',
                'default' => 'colorwhite',
            ],
            'pcFooterFtColor' => [
                'title' => 'PC 하단 글자 색상',
                'desc' => 'PC 하단의 메뉴 등에 사용되는 글자 색상입니다.',
                'default' => 'colorblack',
            ],
            'moHeaderBgColor' => [
                'title' => '모바일 상단 배경 색상',
                'desc' => '모바일 상단의 배경 색상입니다.',
                'default' => 'colorblack',
            ],
            'moHeaderFtColor' => [
                'title' => '모바일 상단 글자 색상',
                'desc' => '모바일 상단에 사용되는 글자 색상입니다.',
                'default' => 'colorblack',
            ],
            'moMenuBgColor' => [
                'title' => '모바일 메뉴 배경 색상',
                'desc' => '모바일 메뉴 배경 색상입니다.',
                'default' => 'colorblack',
            ],
            'moMenuFtColor' => [
                'title' => '모바일 상단 글자 색상',
                'desc' => '모바일 상단의 메뉴 등에 사용되는 글자 색상입니다.',
                'default' => 'colorblack',
            ],
            'moFooterBgColor' => [
                'title' => '모바일 하단 배경 색상',
                'desc' => 'PC에 사용되는 하단의 배경색상입니다.',
                'default' => 'colorwhite',
            ],
            'moFooterFtColor' => [
                'title' => '모바일 하단 글자 색상',
                'desc' => '모바일 하단의 메뉴 등에 사용되는 글자 색상입니다.',
                'default' => 'colorblack',
            ],
        ];
    }

    public function getDisAllowedFileSet()
    {
        return [
            'php', 'pht', 'phtm', 'htm', 'cgi', 'pl', 'exe', 'jsp', 'asp', 'inc', 'phar', 'js', 'shtml', 'sh', 'py', 'rb', 'ps1', 'dylib', 'so', 'dll', 'docm', 'xlsm', 'pptm', 'jar', 'msi', 'vb', 'vbs', 'wsf', 'reg', 'scr'
        ];
    }
}

function registerConfigs($container)
{
    $container->addFactory('ConfigProvider', function($c) {
        return new ConfigProvider($c->get('db'));
    });
}