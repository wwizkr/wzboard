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
        return $prefix . trim($tableName, '`');
    }
}