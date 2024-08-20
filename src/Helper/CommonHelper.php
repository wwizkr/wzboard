<?php
// 파일 위치: /src/PublicHtml/Helper/CommonHelper.php

namespace Web\PublicHtml\Helper;

class CommonHelper
{
    /*
     * Alert
     */
    public static function alertAndBack($message)
    {
        self::alert($message, 'history.back();');
    }

    public static function alertAndClose($message)
    {
        self::alert($message, 'window.close();');
    }

    public static function alertAndRedirect($message, $url)
    {
        self::alert($message, 'window.location.href="' . addslashes($url) . '";');
    }

    private static function alert($message, $action)
    {
        echo '<script type="text/javascript">
                alert("' . addslashes($message) . '");
                ' . $action . '
              </script>';
        exit;
    }
    
    // 문자열에서 숫자만 가져옴.
    public static function pickNumber($string,$default=0)
    {
        $number = $default;

        if(!$string) {
            return $number;
        }
        
        $number = preg_replace('/[^0-9]/i','',$string);

        return $number;
    }
    // 추가적인 헬퍼 클래스들을 여기에 정의할 수 있음
}