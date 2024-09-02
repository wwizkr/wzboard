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
    
    // 문자열에서 숫자만 가져옴. "."도 포함하여 소수점 반영
    public static function pickNumber($string,$default=0)
    {
        $number = $default;

        if(!$string) {
            return $number;
        }
        
        $number = preg_replace('/[^0-9.]/i','',$string);

        return $number;
    }

    /*
     * JSON 응답을 생성하고 반환
     */
    public static function jsonResponse(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
        exit;
    }

    /*
     * 단일 파라미터를 검사하고 정리하는 메서드
     *
     * 사용자가 입력한 특정 파라미터의 값을 유효성 검사하고,
     * 필요에 따라 정리하여 안전하게 반환합니다.
     *
     * @param string $param_name 검사할 파라미터 이름
     * @param string $expected_type 예상되는 파라미터 유형 (예: 'int', 'string', 'email')
     * @param mixed $default 기본값 (파라미터가 없거나 유효하지 않을 경우 반환할 값)
     * @param string $input_type 입력 유형 (예: INPUT_GET, INPUT_POST)
     * @return mixed 정리된 파라미터 값 또는 기본값
     */
    public static function validateParam($param_name, $expected_type, $default = null, $input_type = INPUT_GET)
    {
        // 입력 파라미터 가져오기 (예: GET 또는 POST 요청에서)
        $param_value = filter_input($input_type, $param_name);

        // 파라미터가 없으면 기본값 반환
        if ($param_value === null) {
            return $default;
        }

        // 예상되는 유형에 따라 파라미터 값을 유효성 검사 및 정리
        switch ($expected_type) {
            case 'int':
                // 정수형인지 확인하고, 유효하지 않으면 기본값 반환
                return filter_var($param_value, FILTER_VALIDATE_INT) !== false ? (int)$param_value : $default;
            case 'float':
                // 실수형인지 확인하고, 유효하지 않으면 기본값 반환
                return filter_var($param_value, FILTER_VALIDATE_FLOAT) !== false ? (float)$param_value : $default;
            case 'email':
                // 유효한 이메일 형식인지 확인하고, 유효하지 않으면 기본값 반환
                return filter_var($param_value, FILTER_VALIDATE_EMAIL) !== false ? $param_value : $default;
            case 'url':
                // 유효한 URL 형식인지 확인하고, 유효하지 않으면 기본값 반환
                return filter_var($param_value, FILTER_VALIDATE_URL) !== false ? $param_value : $default;
            case 'boolean':
                // 불리언 값으로 유효한지 확인하고, 유효하지 않으면 기본값 반환
                return filter_var($param_value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null ? (bool)$param_value : $default;
            case 'string':
            default:
                // 기본적으로 문자열로 변환하고, HTML 특수 문자를 이스케이프하여 반환
                return htmlspecialchars($param_value, ENT_QUOTES, 'UTF-8');
        }
    }

    /*
     * 여러 파라미터를 검사하고 정리하는 메서드
     *
     * 여러 개의 파라미터를 한번에 검사하고 정리하여, 안전한 값을 반환합니다.
     *
     * @param array $expected_params 검사할 파라미터 이름과 예상되는 유형의 배열 (예: ['page' => 'int', 'email' => 'email'])
     * @param array $defaults 각 파라미터의 기본값 배열 (예: ['page' => 1, 'email' => 'default@example.com'])
     * @param string $input_type 입력 유형 (예: INPUT_GET, INPUT_POST)
     * @return array 정리된 파라미터 값 배열
     */
    public static function validateParams(array $expected_params, array $defaults = [], $input_type = INPUT_GET)
    {
        $cleaned_params = []; // 정리된 파라미터 값을 저장할 배열

        // 각 파라미터에 대해 validateParam 함수를 사용하여 검증 및 정리
        foreach ($expected_params as $param_name => $expected_type) {
            $cleaned_params[$param_name] = self::validateParam(
                $param_name,
                $expected_type,
                isset($defaults[$param_name]) ? $defaults[$param_name] : null,
                $input_type
            );
        }

        return $cleaned_params; // 정리된 파라미터 값 배열 반환
    }

    /*
     * 배열 형태의 파라미터를 검사하고 정리하는 메서드
     *
     * 사용자가 입력한 배열 형태의 파라미터 값을 유효성 검사하고,
     * 허용된 값 목록에 따라 필터링하여 안전하게 반환합니다.
     *
     * @param string $param_name 검사할 배열 형태의 파라미터 이름
     * @param array $allowed_values 허용된 값 목록
     * @param mixed $default 기본값 (파라미터가 없거나 유효하지 않을 경우 반환할 값)
     * @param string $input_type 입력 유형 (예: INPUT_GET, INPUT_POST)
     * @return array 정리된 파라미터 값 배열 또는 기본값
     */
    public static function validateArrayParam($param_name, array $allowed_values, $default = [], $input_type = INPUT_GET)
    {
        // 배열로 된 입력 파라미터 가져오기
        $param_values = filter_input($input_type, $param_name, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
        // 파라미터가 없거나 유효하지 않으면 기본값 반환
        if ($param_values === null) {
            return $default;
        }

        // 허용된 값만 남기기
        $cleaned_values = array_filter($param_values, function ($value) use ($allowed_values) {
            // 문자열로 변환 후 HTML 특수 문자를 이스케이프 처리
            $clean_value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            // 허용된 값 목록에 있는지 확인
            return in_array($clean_value, $allowed_values, true);
        });

        return $cleaned_values; // 정리된 배열 반환
    }

    /**
     * 값의 타입을 확인하여 SQL 바인딩 타입을 반환하는 메서드
     * @param mixed $value 검사할 값
     * @return array SQL 바인딩을 위한 타입 및 값 정보
     */
    public static function getSqlBindType($value): array
    {
        // 값의 타입을 확인하여 적절한 형식 지정
        if (is_int($value)) {
            return ['i', $value]; // 정수형일 경우 'i'
        } elseif (is_string($value)) {
            return ['s', $value]; // 문자열일 경우 's'
        } elseif (is_numeric($value)) {
            return ['i', $value]; // 숫자형 문자열일 경우도 정수로 처리
        } else {
            return ['s', (string)$value]; // 다른 모든 경우 문자열로 처리
        }
    }

    // 추가적인 헬퍼 메소드들을 여기에 정의할 수 있음
}