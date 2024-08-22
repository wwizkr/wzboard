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

    /*
     * 폼 데이터를 처리하여 형식을 맞추는 메서드
     * 별도로 처리해야 할 입력값이 있을 경우 각 컨트롤러에서 처리해야 함.
     */
    public static function processFormData(array $formData, array $numericFields = []): array
    {
        $data = [];
        
        foreach($formData as $key => $val) {
            // 만약 값이 배열이라면, '-'로 묶어서 하나의 문자열로 변환
            if (is_array($val)) {
                $val = implode('-', $val);
            }
            
            $value = $val;

            // $key가 $numericFields 배열에 속해 있으면 ['i', $value] 형식으로, 아니면 ['s', $value] 형식으로 저장
            if (in_array($key, $numericFields)) {
                $data[$key] = ['i', $value];
            } else {
                $data[$key] = ['s', $value];
            }
        }

        return $data;
    }
    // 추가적인 헬퍼 클래스들을 여기에 정의할 수 있음
}