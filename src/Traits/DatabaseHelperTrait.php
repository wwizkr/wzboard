<?php
// 파일 위치: /src/Traits/DatabaseHelperTrait.php

namespace Web\PublicHtml\Traits;

trait DatabaseHelperTrait
{
    /**
     * 테이블 이름에 접두사를 추가합니다.
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        $prefix = $_ENV['DB_TABLE_PREFIX'] ?? 'prefix_';

        // 이미 접두사가 있는지 확인
        if (strpos($tableName, $prefix) === 0) {
            return $tableName; // 이미 접두사가 붙어있으면 그대로 반환
        }

        return $prefix . trim($tableName, '`');
    }
}