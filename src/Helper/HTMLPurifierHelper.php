<?php
// HTMLPurifierHelper.php
namespace Web\PublicHtml\Helper;

//require_once __DIR__ . '/../../vendor/autoload.php';

class HTMLPurifierHelper
{
    /**
     * HTML Purifier를 사용하여 입력을 정리하는 함수
     *
     * @param string $data 사용자 입력 데이터
     * @return string 정리된 데이터
     */
    public static function purify($data)
    {
        $config = \HTMLPurifier_Config::createDefault();

        // 허용할 태그와 속성 설정
        $config->set('HTML.Allowed', 'p,b,a[href|title],i,br,ul,li,strong,em');

        // 허용할 스타일 설정
        $config->set('CSS.AllowedProperties', 'color,background-color,font,font-size,text-decoration');

        // 허용할 URL 스키마 설정
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);

        // 캐시 디렉토리 설정 (성능 최적화를 위해)
        $config->set('Cache.SerializerPath', '/home/web/public_html/storage/cache/html'); // 실제 경로로 수정

        // HTML Purifier 인스턴스 생성
        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($data);
    }
}