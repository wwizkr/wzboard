<?php
// 파일 위치: /home/web/public_html/src/Helper/CryptoHelper.php

namespace Web\PublicHtml\Helper;

class CryptoHelper
{
    /**
     * 비밀번호를 해시화합니다.
     *
     * @param string $password 해시화할 비밀번호
     * @return string 해시화된 비밀번호
     */
    public static function hashPassword(string $password): string
    {
        $algo = self::getPasswordHashAlgo();
        $options = self::getPasswordHashOptions();
        
        return password_hash($password, $algo, $options);
    }

    /**
     * 환경 변수에서 비밀번호 해싱 알고리즘을 가져옵니다.
     *
     * @return int|string
     */
    private static function getPasswordHashAlgo()
    {
        $algo = $_ENV['PASSWORD_HASH_ALGO'] ?? 'PASSWORD_DEFAULT';
        
        switch ($algo) {
            case 'PASSWORD_ARGON2I':
                return defined('PASSWORD_ARGON2I') ? PASSWORD_ARGON2I : PASSWORD_DEFAULT;
            case 'PASSWORD_ARGON2ID':
                return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
            case 'PASSWORD_BCRYPT':
                return PASSWORD_BCRYPT;
            case 'PASSWORD_DEFAULT':
                return PASSWORD_DEFAULT;
            default:
                return PASSWORD_DEFAULT;
        }
    }

    /**
     * 환경 변수에서 비밀번호 해싱 옵션을 가져옵니다.
     *
     * @return array
     */
    private static function getPasswordHashOptions(): array
    {
        $algo = self::getPasswordHashAlgo();
        $cost = (int)($_ENV['PASSWORD_HASH_COST'] ?? 12);

        if ($algo === PASSWORD_BCRYPT || $algo === PASSWORD_DEFAULT) {
            return ['cost' => max(4, min(31, $cost))];  // bcrypt의 cost는 4에서 31 사이여야 합니다
        }

        // Argon2i 또는 Argon2id의 경우 기본값 사용
        return [
            'memory_cost' => 65536, // 64MB
            'time_cost' => 4,
            'threads' => 1
        ];
    }

    /**
     * 비밀번호가 해시와 일치하는지 확인합니다.
     *
     * @param string $password 확인할 비밀번호
     * @param string $hash 저장된 해시
     * @return bool 비밀번호 일치 여부
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 데이터를 암호화합니다.
     *
     * @param string $data 암호화할 데이터
     * @return string 암호화된 데이터
     */
    public static function encrypt(string $data): string
    {
        $encryption_key = base64_decode($_ENV['ENCRYPT_KEY']);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * 암호화된 데이터를 복호화합니다.
     *
     * @param string $data 복호화할 데이터
     * @return string 복호화된 데이터
     */
    public static function decrypt(string $data): string
    {
        $encryption_key = base64_decode($_ENV['ENCRYPT_KEY']);
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    /**
     * 데이터를 JSON으로 변환한 후 암호화합니다.
     *
     * @param array $data 암호화할 데이터 배열
     * @return string 암호화된 데이터
     */
    public static function encryptJson(array $data): string
    {
        $jsonData = json_encode($data);
        return self::encrypt($jsonData);
    }

    /**
     * 암호화된 데이터를 복호화한 후 JSON을 디코드합니다.
     *
     * @param string $encryptedData 복호화할 암호화된 데이터
     * @return array 복호화된 데이터 배열
     */
    public static function decryptJson(string $encryptedData): array
    {
        $jsonData = self::decrypt($encryptedData);
        return json_decode($jsonData, true);
    }

    /**
     * JWT 토큰을 생성합니다.
     *
     * @param array $payload JWT 페이로드
     * @return string 생성된 JWT 토큰
     */
    public static function generateJwtToken(array $payload): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + ($_ENV['JWT_TTL'] * 60); // JWT_TTL은 분 단위
        $payload = json_encode($payload);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $_ENV['JWT_SECRET'], true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * JWT 토큰을 검증합니다.
     *
     * @param string $token 검증할 JWT 토큰
     * @return array|false 검증 성공 시 페이로드, 실패 시 false
     */
    public static function verifyJwtToken(string $token)
    {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $_ENV['JWT_SECRET'], true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if ($base64UrlSignature !== $signatureProvided) {
            return false;
        }

        $payloadObj = json_decode($payload, true);
        if (isset($payloadObj['exp']) && $payloadObj['exp'] < time()) {
            return false;
        }

        return $payloadObj;
    }
}