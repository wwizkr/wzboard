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

    /**
     * decode JSON input data
     *
     * @return array The decoded JSON data
     */
    public static function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        
        // JSON 파싱에 실패하거나 Content-Type이 application/json이 아닌 경우
        parse_str($input, $data);
        return $data ?: [];
    }

    public static function extractFormData(array $data): array
    {
        $formData = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'formData[') === 0) {
                $formKey = str_replace(['formData[', ']'], '', $key);
                $formData[$formKey] = $value;
            }
        }
        return $formData;
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
     * Editor Script
     */
    public static function getEditorScript($editor = 'tinymce')
    {
        $script = '';
        
        if ($editor === 'tinymce') {
            $script .= '<script src="/assets/js/lib/editor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>'.PHP_EOL;
            $script .= '<script src="/assets/js/lib/editor/tinymce/tinymce.editor.js"></script>'.PHP_EOL;
        }

        return $script;
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

    /*
     * 단일 파라미터를 검사하고 정리하는 메서드
     *
     * 사용자가 입력한 특정 파라미터의 값을 유효성 검사하고,
     * 필요에 따라 정리하여 안전하게 반환합니다.
     *
     * @param string $param_name 검사할 파라미터 이름
     * @param string $expected_type 예상되는 파라미터 유형 (예: 'int', 'string', 'email')
     * @param mixed $default 기본값 (파라미터가 없거나 유효하지 않을 경우 반환할 값)
     * @param mixed $input_value 직접 전달된 값 (GET/POST 외의 값 검증용)
     * @param string $input_type 입력 유형 (예: INPUT_GET, INPUT_POST) 기본값은 null
     * @return mixed 정리된 파라미터 값 또는 기본값
     */
    public static function validateParam($param_name, $expected_type, $default = null, $input_value = null, $input_type = null)
    {
        // 입력 파라미터 가져오기 (예: GET 또는 POST 요청에서)
        if ($input_type !== null) {
            // GET/POST 요청에서 값 가져오기
            $param_value = filter_input($input_type, $param_name);
        } else {
            // 직접 전달된 값 사용
            $param_value = $input_value;
        }

        // 파라미터가 없으면 기본값 반환
        if ($param_value === null) {
            return $default;
        }

        // 예상되는 유형에 따라 파라미터 값을 유효성 검사 및 정리
        switch ($expected_type) {
            case 'int':
                return filter_var($param_value, FILTER_VALIDATE_INT) !== false ? (int)$param_value : $default;
            case 'float':
                return filter_var($param_value, FILTER_VALIDATE_FLOAT) !== false ? (float)$param_value : $default;
            case 'email':
                return filter_var($param_value, FILTER_VALIDATE_EMAIL) !== false ? $param_value : $default;
            case 'url':
                return filter_var($param_value, FILTER_VALIDATE_URL) !== false ? $param_value : $default;
            case 'boolean':
                return filter_var($param_value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null ? (bool)$param_value : $default;
            case 'string':
            default:
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
    public static function validateParams(array $expected_params, array $defaults = [], $input_type = null)
    {
        $cleaned_params = []; // 정리된 파라미터 값을 저장할 배열

        // 각 파라미터에 대해 validateParam 함수를 사용하여 검증 및 정리
        foreach ($expected_params as $param_name => $expected_type) {
            $cleaned_params[$param_name] = self::validateParam(
                $param_name,
                $expected_type,
                isset($defaults[$param_name]) ? $defaults[$param_name] : null,
                $input_type !== null ? null : (isset($defaults[$param_name]) ? $defaults[$param_name] : null),
                $input_type
            );
        }

        return $cleaned_params; // 정리된 파라미터 값 배열 반환
    }

    /*
     * 배열 형태의 파라미터를 검사하고 정리하는 메서드
     *
     * 이 메서드는 사용자가 입력한 배열 형태의 파라미터 값을 유효성 검사하고,
     * 사전에 정의된 허용된 값 목록에 따라 필터링하여 안전한 값을 반환합니다.
     * 파라미터 값이 없거나 유효하지 않으면 기본값을 반환합니다.
     * 배열에 예상되는 입력값이 확실히 있는 경우에만 사용합니다.
     *
     * @param array|null $param_values 입력 값으로 전달된 배열 (기본값은 null)
     * @param array $allowed_values 허용된 값 목록
     * @param array $default 기본값 (파라미터가 없거나 유효하지 않을 경우 반환할 값)
     * @return array 정리된 파라미터 값 배열 또는 기본값
     */
    public static function validateArrayParam(array $allowed_values, $param_values = null, array $default = []): array
    {
        //error_log("Allowed Valus:".print_r($allowed_values, true));
        //error_log("Param Valus:".print_r($param_values, true));

        if ($param_values === null) {
            return $default;
        }
        if (!is_array($param_values)) {
            return $default;
        }
        // Use array_intersect instead of array_intersect_key
        $cleaned_values = array_intersect($allowed_values, $param_values);
        return array_map(function($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }, $cleaned_values);
    }

    /**
     * 리스트 페이지의 sort 를 검증하는 메소드.
     * 
     * @param array $allowedSortFields 허용된 정렬 필드 목록
     * @param array $allowedOrders asc, desc로 고정
     * @return array 리스트 파라미터
     */
    private static function validateSort(array $allowedSortFields, array $inputSort): array
    {
        $validSort = [];
        $allowedOrders = ['asc', 'desc'];

        if (isset($inputSort['field']) && in_array($inputSort['field'], $allowedSortFields)) {
            $validSort['field'] = $inputSort['field'];
        }

        if (isset($inputSort['order']) && in_array(strtolower($inputSort['order']), $allowedOrders)) {
            $validSort['order'] = strtolower($inputSort['order']);
        }

        return $validSort;
    }

    /**
     * 리스트 페이지의 파라미터들을 가져옵니다.
     * 
     * @param array $config 설정 배열 (페이지당 행 수, 페이지 번호 수 등)
     * @param array $allowedFilters 허용된 필터 목록
     * @param array $allowedSortFields 허용된 정렬 필드 목록
     * @param array $additionalParams 추가 파라미터 설정 (키: 파라미터 이름, 값: [타입, 기본값])
     * @return array 리스트 파라미터
     */
    public static function getListParameters(array $config, array $allowedFilters, array $allowedSortFields, array $additionalParams = []): array
    {
        $params = [
            'currentPage' => self::validateParam('page', 'int', 1, null, INPUT_GET),
            'searchQuery' => self::validateParam('search', 'string', '', null, INPUT_GET),
            'filters' => self::validateArrayParam($allowedFilters, $_GET['filter'] ?? []),
            'sort' => self::validateSort($allowedSortFields, $_GET['sort'] ?? []),
            'page_rows' => $config['cf_page_rows'] ?? 20,
            'page_nums' => $config['cf_page_nums'] ?? 10,
            'additionalQueries' => [],
        ];

        // 추가 파라미터 처리
        foreach ($additionalParams as $paramName => $paramConfig) {
            $type = $paramConfig[0] ?? 'string';
            $default = $paramConfig[1] ?? '';
            $allowedValues = $paramConfig[2] ?? null;

            $paramNameWithoutBrackets = rtrim($paramName, '[]');
            $value = $_GET[$paramNameWithoutBrackets] ?? $default;

            if ($type === 'array') {
                if (is_array($value) && ($allowedValues === null || array_diff($value, $allowedValues) === [])) {
                    $params['additionalQueries'][] = [$paramNameWithoutBrackets, $value];
                }
            } else {
                $value = self::validateParam($paramNameWithoutBrackets, $type, $default, null, INPUT_GET);
                if ($allowedValues === null || in_array($value, $allowedValues)) {
                    $params['additionalQueries'][] = [$paramNameWithoutBrackets, $value];
                }
            }
        }

        return $params;
    }

    /**
     * 리스트 페이지의 추가 파라미터 매핑 및 정리 Service
     * 
     * @param array $additionalQueries 허용된 필터 목록
     * @param string $mappingBeforeField 매핑 전 필드명
     * @param string $mappingAfterField 매핑 후 필드명
     * @param array $mappingData 추가 파라미터 설정 (키: 파라미터 이름, 값: [타입, 기본값])
     * @return array 추가 파라미터
     * URL에 파라미터가 있더라도 정상적인 파라미터가 아니면 검색어로 사용하지 않음.
     */
    public static function additionalServiceQueries($additionalQueries, $mappingBeforeField = '', $mappingAfterField = '', array $mappingData = [])
    {
        //error_log("Common additionalQueries:" . print_r($additionalQueries, true));
        //error_log("Common mappingBeforeField:" . print_r($mappingBeforeField, true));
        //error_log("Common mappingAfterField:" . print_r($mappingAfterField, true));
        //error_log("Common mappingData:" . print_r($mappingData, true));
        $processed = [];
        foreach ($additionalQueries as $query) {
            $field = $query[0];
            $value = $query[1];
            
            if (is_array($value)) {
                if ($mappingBeforeField && $mappingAfterField && $field === $mappingBeforeField) {
                    $categoryNumbers = array_filter(array_map(function ($name) use ($mappingData) {
                        return $mappingData[$name] ?? null;
                    }, $value));
                    if (!empty($categoryNumbers)) {
                        $processed[] = [$mappingAfterField, array_values($categoryNumbers)];
                    }
                } else {
                    // 배열이지만 $mappingBeforeField 가 아님.
                    $processed[] = [$field, $value];
                }
            } else {
                // 배열이 아닌 경우
                $processed[] = [$field, $value];
            }
        }

        //error_log("Common processedData:" . print_r($processed, true));

        return $processed;
    }
    
    /**
     * 리스트 페이지의 추가 파라미터 매핑 및 정리 Model
     * 
     * @param array $config 설정 배열 (페이지당 행 수, 페이지 번호 수 등)
     * @param array $additionalQueries 허용된 필터 목록
     * @param array $addWhere 배열 추가
     * @param array $bindValues 배열 추가
     * 쿼리문에 추가할 $addWhere, $bindValues 배열을 추가.
     */
    public static function additionalModelQueries($additionalQueries, &$addWhere, &$bindValues)
    {
        //error_log("AdditionalQueries:".print_r($additionalQueries,true));
        foreach ($additionalQueries as $index => $query) {
            $field = $query[0];
            $value = $query[1];
            
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $placeholders[] = "?";
                    $bindValues[] = $v;
                }
                $addWhere[] = "$field IN (" . implode(',', $placeholders) . ")";
            } else {
                $addWhere[] = "$field = ?";
                $bindValues[] = $value;
            }
        }
    }

    /**
     * 페이지네이션 데이터를 계산합니다.
     * 
     * @param int $totalItems 총 아이템 수
     * @param int $currentPage 현재 페이지
     * @param int $itemsPerPage 페이지당 아이템 수
     * @param int $pageNums 표시할 페이지 번호 수
     * @return array 페이지네이션 데이터
     */
    public static function getPaginationData(int $totalItems, int $currentPage, int $itemsPerPage, int $pageNums): array
    {
        return [
            'totalItems' => $totalItems,
            'currentPage' => $currentPage,
            'totalPages' => ceil($totalItems / $itemsPerPage),
            'itemsPerPage' => $itemsPerPage,
            'pageNums' => $pageNums,
        ];
    }

    // 추가적인 헬퍼 메소드들을 여기에 정의할 수 있음
}