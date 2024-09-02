<?php
// src/Admin/Helper/AdminSettings.php

namespace Web\Admin\Helper;

class AdminSettingsHelper
{
    public static function getSkin()
    {
        /*
         * skin 디렉토리
         * 관리자 스킨 Admin/skin-name
         * 기본 : Home, Header, Footer, Layout, Content, partials
         * 별도 스킨 추가 시 /src/View 디렉토리내에 디렉토리 추가 (ex: ShopList/basic)
         * $skin_dir 변수에 디렉토리명 => 디렉토리 설명으로 추가 (ex: ShopList => 쇼핑몰 상품 목록)
         */
        $skin_dir = [
            'Home' => '메인화면 스킨, 메인화면 스킨에 대한 설명',
            'Header' => '상단 스킨, 상단 스킨에 대한 설명',
            'Footer' => '하단 스킨, 하단 스킨에 대한 설명',
            'Layout' => '레이아웃 스킨, 레이아웃 스킨에 대한 설명',
            'Content' => '내용 스킨, 내용 스킨에 대한 설명',
            'partials' => '기본 스킨, 기본 스킨에 대한 설명',
            'Admin' => '관리자 스킨, 관리자 스킨에 대한 설명',
        ];

        $base_view_path = __DIR__ . '/../../View';
        $admin_view_path = __DIR__ . '/../../Admin/View';

        $result = [];

        foreach ($skin_dir as $key => $val) {
            $path = ($key === 'Admin') ? $admin_view_path : $base_view_path . '/' . $key;
            $ex = explode(",",$val);

            if (is_dir($path)) {
                $sub_dirs = array_filter(scandir($path), function($dir) use ($path) {
                    return $dir !== '.' && $dir !== '..' && is_dir($path . '/' . $dir);
                });
                
                $result[] = [
                    'name' => strtolower($key),
                    'title' => $ex[0],
                    'desc' => $ex[1],
                    'skin' => array_values($sub_dirs)
                ];
            }
        }

        return $result;
    }

    public static function getSnsSeo()
    {
        return [
            'naver_blog' => '네이버 블로그,ex)https://blog.naver.com/myblog',
            'naver_post' => '네이버 포스트,ex)https://post.naver.com/mypost',
            'naver_know' => '네이버 지식인,ex)https://kin.naver.com/profile/index.naver?u=XXXXXXXXXXXXXXXXXXXXXXXXX',
            'naver_modoo' => '네이버 모두홈페이지,ex)https://myhomepage.modoo.at',
            'sns_utv' => '유튜브,ex)https://www.youtube.com/@mychannel',
            'sns_facebook' => '페이스북,ex)https://www.facebook.com/myfacebook',
            'sns_twitter' => '트위터,ex)https://twitter.com/mytwitter',
            'sns_insta' => '인스타그램,ex)https://www.instagram.com/myinsta',
            'sns_kakaostory' => '카카오스토리,ex)https://story.kakao.com/mystory',
            'sns_tistory' => '티스토리,ex)https://mytistory.tistory.com',
        ];
    }
}