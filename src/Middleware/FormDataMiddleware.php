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
     * CSRF 토큰을 검증합니다.
     * 
     * @param string $formType 폼 타입 ('admin' 또는 'user')
     * @throws \Exception 토큰이 유효하지 않을 경우
     */
    public function validateToken(string $formType): void
    {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $csrfTokenKey = $formType === 'admin' ? $_ENV['ADMIN_CSRF_TOKEN_KEY'] : $_ENV['USER_CSRF_TOKEN_KEY'];
        
        $this->csrfTokenHandler->validateToken($csrfToken, $csrfTokenKey);
    }

    /**
     * 폼 데이터를 처리합니다.
     * 
     * @param array $formData 처리할 폼 데이터
     * @param array $numericFields 숫자로 처리할 필드 목록
     * @return array 처리된 데이터
     */
    public function processFormData(array $formData, array $numericFields = []): array
    {
        $data = [];
        foreach ($formData as $key => $val) {
            if (is_array($val)) {
                $val = implode('-', $val);
            }
            if (in_array($key, $numericFields)) {
                $value = $val ? $val : 0;
                $data[$key] = ['i', $value];
            } else {
                $value = $val ? $val : '';
                $data[$key] = ['s', $value];
            }
        }
        return $data;
    }

    /**
     * 폼 데이터를 처리하고 CSRF 토큰을 검증합니다.
     * 
     * @param string $formType 폼 타입
     * @param array $formData 처리할 폼 데이터
     * @param array $numericFields 숫자로 처리할 필드 목록
     * @return array 처리된 데이터
     */
    public function handle(string $formType, array $formData, array $numericFields = []): array
    {
        $this->validateToken($formType);
        return $this->processFormData($formData, $numericFields);
    }
}