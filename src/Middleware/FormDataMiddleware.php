<?php
// 파일 위치: src/Middleware/FormDataMiddleware.php

namespace Web\PublicHtml\Middleware;

use Web\PublicHtml\Helper\CryptoHelper;

class FormDataMiddleware
{
    private $csrfTokenHandler;

    /**
     * FormDataMiddleware 생성자.
     * @param CsrfTokenHandler $csrfTokenHandler
     */
    public function __construct(CsrfTokenHandler $csrfTokenHandler)
    {
        $this->csrfTokenHandler = $csrfTokenHandler;
    }

    /**
     * 폼 데이터를 처리하고 CSRF 토큰을 검증.
     */
    public function handle(string $formType, array $formData, array $numericFields = []): array
    {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        // 폼 타입에 따라 적절한 CSRF 토큰 키 선택
        $csrfTokenKey = $formType === 'admin' ? $_ENV['ADMIN_CSRF_TOKEN_KEY'] : $_ENV['USER_CSRF_TOKEN_KEY'];

        // CSRF 토큰 검증
        $this->csrfTokenHandler->validateToken($csrfToken, $csrfTokenKey);

        $data = [];

        foreach ($formData as $key => $val) {
            // 값이 배열이라면, '-'로 묶어서 하나의 문자열로 변환
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
}