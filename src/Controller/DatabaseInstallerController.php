<?php
namespace Web\PublicHtml\Controller;

use PDO;
use PDOException;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CryptoHelper;

class DatabaseInstallerController
{
    use DatabaseHelperTrait;

    private $db;
    private $config;
    private $schema;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->config = $container->get('config');
    }

    /**
     * 스키마 설치를 실행합니다.
     * 지정된 스키마 파일을 로드하고 각 테이블을 처리합니다.
     */
    public function install($vars = [])
    {
        $schemaName = $_GET['schema'] ?? 'default';
        $schemaPath = __DIR__ . "/../../config/schemas/{$schemaName}.php";
        
        if (!file_exists($schemaPath)) {
            echo json_encode(["success" => false, "message" => "스키마 파일을 찾을 수 없습니다: {$schemaName}"]);
            return;
        }

        $this->schema = require $schemaPath;
        $results = [];

        foreach ($this->schema['schema_content'] as $tableName => $createTableQuery) {
            if ($tableName === 'initial_data') continue;

            $prefixedTableName = $this->getTableName($tableName);
            $createTableQuery = preg_replace('/CREATE TABLE .*? \(/i', "CREATE TABLE `{$prefixedTableName}` (", $createTableQuery);
            
            try {
                $this->processTable($prefixedTableName, $createTableQuery, $tableName);
                $results[] = "테이블 {$prefixedTableName} 처리 완료";
            } catch (PDOException $e) {
                $results[] = "테이블 {$prefixedTableName} 처리 중 오류 발생: " . $e->getMessage();
                error_log("SQL Error: " . $e->getMessage());
            }
        }

        echo json_encode(["success" => true, "results" => $results]);
    }

    /**
     * 테이블을 생성하거나 수정합니다.
     * 테이블이 존재하지 않으면 생성하고, 존재하면 변경사항을 적용합니다.
     */
    private function processTable($prefixedTableName, $createTableQuery, $originalTableName)
    {
        $stmt = $this->db->query("SHOW TABLES LIKE '{$prefixedTableName}'");
        $tableExists = $stmt->rowCount() > 0;

        if (!$tableExists) {
            $this->db->exec($createTableQuery);
            $this->insertInitialData($originalTableName);
        } else {
            $this->alterExistingTable($prefixedTableName, $createTableQuery);
        }
    }

    /**
     * 기존 테이블을 수정합니다.
     * 테이블 정의를 파싱하고 필드와 제약조건을 처리합니다.
     */
    private function alterExistingTable($tableName, $createTableQuery)
    {
        preg_match('/\(([\s\S]*)\)/s', $createTableQuery, $matches);
        $tableDefinition = $matches[1];
        $components = $this->parseTableDefinition($tableDefinition);

        foreach ($components['fields'] as $fieldName => $fieldDef) {
            $this->processField($tableName, $fieldName, $fieldDef);
        }

        foreach ($components['constraints'] as $constraintDef) {
            $this->processConstraint($tableName, $constraintDef);
        }
    }
    
    /**
     * 테이블 정의를 파싱합니다.
     * CREATE TABLE 문에서 필드와 제약조건을 추출합니다.
     */
    private function parseTableDefinition($tableDefinition)
    {
        $lines = explode("\n", $tableDefinition);
        $fields = [];
        $constraints = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // 제약 조건 확인
            if (preg_match('/^(PRIMARY KEY|UNIQUE( KEY)?|CONSTRAINT|FOREIGN KEY|KEY|INDEX)/i', $line)) {
                $constraints[] = $line;
            } else {
                // 필드 정의
                if (preg_match('/^`?(\w+)`?/i', $line, $matches)) {
                    $fieldName = $matches[1];
                    $fields[$fieldName] = $line;
                }
            }
        }

        return ['fields' => $fields, 'constraints' => $constraints];
    }
    
    /**
     * 개별 필드를 처리합니다.
     * 필드가 존재하지 않으면 추가하고, 존재하면 필요시 수정합니다.
     */
    private function processField($tableName, $fieldName, $fieldDef)
    {
        try {
            error_log("처리 중인 필드: {$tableName}.{$fieldName}");
            error_log("원본 필드 정의: " . $fieldDef);

            // 필드 정의에서 마지막 콤마 제거
            $fieldDef = rtrim($fieldDef, ',');

            $stmt = $this->db->query("SHOW COLUMNS FROM `{$tableName}` LIKE '{$fieldName}'");
            $columnExists = $stmt->rowCount() > 0;

            // 필드 정의를 세부적으로 파싱
            preg_match('/^(\w+)\s+(\w+(?:\([^\)]+\))?)\s*((?:NOT NULL|NULL)?)?\s*(DEFAULT\s+[^,]+)?\s*(AUTO_INCREMENT)?\s*(PRIMARY KEY)?\s*(COMMENT\s+\'(?:[^\'\\\\]|\\\\.)*\')?/i', $fieldDef, $matches);

            $cleanFieldName = $matches[1];
            $dataType = $matches[2];
            $nullability = $matches[3] ?? '';
            $defaultValue = $matches[4] ?? '';
            $autoIncrement = $matches[5] ?? '';
            $primaryKey = $matches[6] ?? '';
            $comment = $matches[7] ?? '';

            // COMMENT 값 추출
            $commentValue = '';
            if ($comment && preg_match('/COMMENT\s+\'((?:[^\'\\\\]|\\\\.)*)\'/i', $comment, $commentMatches)) {
                $commentValue = $commentMatches[1];
            }

            // SQL 문 구성 (COMMENT 제외)
            $sqlParts = array_filter([
                $dataType,
                $nullability,
                $defaultValue,
                $autoIncrement
            ]);
            $cleanFieldType = implode(' ', $sqlParts);

            // SQL 문 준비
            if (!$columnExists) {
                $sql = "ALTER TABLE `{$tableName}` ADD COLUMN `{$cleanFieldName}` {$cleanFieldType}";
            } else {
                $sql = "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$cleanFieldName}` {$cleanFieldType}";
            }

            // COMMENT가 있으면 SQL에 추가
            if ($commentValue) {
                $sql .= " COMMENT :comment";
            }

            error_log("준비된 SQL: " . $sql);

            // PDO prepared statement 사용
            $stmt = $this->db->getPdoInstance()->prepare($sql);
            
            // COMMENT 바인딩 (있는 경우에만)
            if ($commentValue) {
                $stmt->bindParam(':comment', $commentValue, PDO::PARAM_STR);
            }

            // SQL 실행
            $result = $stmt->execute();

            if ($result) {
                error_log("컬럼 처리 완료: {$tableName}.{$fieldName}");
            } else {
                error_log("컬럼 처리 실패: {$tableName}.{$fieldName}");
                error_log("SQL 오류 정보: " . print_r($stmt->errorInfo(), true));
            }

            // PRIMARY KEY 처리 (필요한 경우)
            if ($primaryKey) {
                $pkSql = "ALTER TABLE `{$tableName}` DROP PRIMARY KEY, ADD PRIMARY KEY (`{$cleanFieldName}`)";
                error_log("PRIMARY KEY 설정 SQL: " . $pkSql);
                $this->db->exec($pkSql);
                error_log("PRIMARY KEY 설정 완료: {$tableName}.{$fieldName}");
            }

        } catch (PDOException $e) {
            error_log("필드 처리 중 오류 발생: {$tableName}.{$fieldName} - " . $e->getMessage());
            error_log("스택 트레이스: " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    /**
     * 제약조건을 처리합니다.
     * 제약조건의 유형을 식별하고 적절한 처리 메서드를 호출합니다.
     */
    private function processConstraint($tableName, $constraintDef)
    {
        if (preg_match('/PRIMARY KEY\s*\(([^)]+)\)/i', $constraintDef, $matches)) {
            $this->processPrimaryKey($tableName, $matches[1]);
        } elseif (preg_match('/UNIQUE\s+(?:KEY\s+)?`?(\w+)`?\s*\(([^)]+)\)/i', $constraintDef, $matches)) {
            $this->processUniqueKey($tableName, $matches[1], $matches[2]);
        } elseif (preg_match('/FOREIGN KEY\s+`?(\w+)`?\s*\(([^)]+)\)\s+REFERENCES\s+`?(\w+)`?\s*\(([^)]+)\)/i', $constraintDef, $matches)) {
            $this->processForeignKey($tableName, $matches[1], $matches[2], $matches[3], $matches[4]);
        } elseif (preg_match('/(?:INDEX|KEY)\s+`?(\w+)`?\s*\(([^)]+)\)/i', $constraintDef, $matches)) {
            $this->processIndex($tableName, $matches[1], $matches[2]);
        }
    }
    
    /**
     * 기본 키를 처리합니다.
     * 기존 기본 키를 삭제하고 새로운 기본 키를 추가합니다.
     */
    private function processPrimaryKey($tableName, $columns)
    {
        $columns = $this->formatColumnList($columns);
        $stmt = $this->db->query("SHOW KEYS FROM `{$tableName}` WHERE Key_name = 'PRIMARY'");
        $hasPrimaryKey = $stmt->rowCount() > 0;

        if ($hasPrimaryKey) {
            // 기존 PRIMARY KEY 삭제 후 새로 생성
            $this->db->exec("ALTER TABLE `{$tableName}` DROP PRIMARY KEY, ADD PRIMARY KEY ({$columns})");
        } else {
            // 새로운 PRIMARY KEY 추가
            $this->db->exec("ALTER TABLE `{$tableName}` ADD PRIMARY KEY ({$columns})");
        }
    }
    
    /**
     * 고유 키를 처리합니다.
     * 기존 고유 키를 삭제하고 새로운 고유 키를 추가합니다.
     */
    private function processUniqueKey($tableName, $keyName, $columns)
    {
        $columns = $this->formatColumnList($columns);
        $stmt = $this->db->query("SHOW KEYS FROM `{$tableName}` WHERE Key_name = '{$keyName}'");
        $keyExists = $stmt->rowCount() > 0;

        if ($keyExists) {
            // 기존 UNIQUE KEY 삭제 후 새로 생성
            $this->db->exec("ALTER TABLE `{$tableName}` DROP INDEX `{$keyName}`, ADD UNIQUE KEY `{$keyName}` ({$columns})");
        } else {
            // 새로운 UNIQUE KEY 추가
            $this->db->exec("ALTER TABLE `{$tableName}` ADD UNIQUE KEY `{$keyName}` ({$columns})");
        }
    }
    
     /**
     * 외래 키를 처리합니다.
     * 기존 외래 키를 삭제하고 새로운 외래 키를 추가합니다.
     */
    private function processForeignKey($tableName, $constraintName, $columns, $refTable, $refColumns)
    {
        $columns = $this->formatColumnList($columns);
        $refColumns = $this->formatColumnList($refColumns);
        
        // 기존 외래 키 제약 조건 확인
        $stmt = $this->db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                                  WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
                                  AND TABLE_NAME = '{$tableName}' 
                                  AND CONSTRAINT_NAME = '{$constraintName}'");
        $constraintExists = $stmt->rowCount() > 0;

        if ($constraintExists) {
            // 기존 FOREIGN KEY 삭제 후 새로 생성
            $this->db->exec("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraintName}`");
        }
        
        // 새로운 FOREIGN KEY 추가
        $this->db->exec("ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$constraintName}` 
                         FOREIGN KEY ({$columns}) REFERENCES `{$refTable}` ({$refColumns})");
    }
    
    /**
     * 인덱스를 처리합니다.
     * 기존 인덱스를 삭제하고 새로운 인덱스를 추가합니다.
     */
    private function processIndex($tableName, $indexName, $columns)
    {
        $columns = $this->formatColumnList($columns);
        $stmt = $this->db->query("SHOW INDEX FROM `{$tableName}` WHERE Key_name = '{$indexName}'");
        $indexExists = $stmt->rowCount() > 0;

        if ($indexExists) {
            // 기존 INDEX 삭제 후 새로 생성
            $this->db->exec("ALTER TABLE `{$tableName}` DROP INDEX `{$indexName}`, ADD INDEX `{$indexName}` ({$columns})");
        } else {
            // 새로운 INDEX 추가
            $this->db->exec("ALTER TABLE `{$tableName}` ADD INDEX `{$indexName}` ({$columns})");
        }
    }
    
    /**
     * 컬럼 리스트를 포맷팅합니다.
     * 컬럼 이름에 백틱을 추가하고 쉼표로 구분합니다.
     */
    private function formatColumnList($columns)
    {
        // 컬럼 리스트를 정리하고 포맷팅
        return implode(', ', array_map(function($col) {
            return '`' . trim($col) . '`';
        }, explode(',', $columns)));
    }

    /**
     * 필드 정의를 추출합니다.
     * 필드 이름을 제외한 나머지 정의를 반환합니다.
     */
    private function getFieldDefinition($fieldDef)
    {
        preg_match('/^\s*`?\w+`?\s+(.*)$/', $fieldDef, $matches);
        return trim($matches[1]);
    }

    /**
     * 현재 필드 정의를 가져옵니다.
     * 데이터베이스의 현재 필드 정의를 문자열로 반환합니다.
     */
    private function getCurrentFieldDefinition($column)
    {
        $def = $column['Type'];
        if ($column['Null'] === 'NO') {
            $def .= ' NOT NULL';
        }
        if ($column['Default'] !== null) {
            $def .= " DEFAULT '" . $column['Default'] . "'";
        }
        if ($column['Extra'] !== '') {
            $def .= ' ' . strtoupper($column['Extra']);
        }
        return $def;
    }

    /**
     * 초기 데이터를 가져옵니다.
     * 스키마에서 지정된 테이블의 초기 데이터를 반환합니다.
     */
    private function getInitialData($tableName)
    {
        return $this->schema['schema_content']['initial_data'][$tableName] ?? null;
    }

    /**
     * 초기 데이터를 삽입합니다.
     * 테이블에 초기 데이터를 삽입하고 결과를 로깅합니다.
     */
    private function insertInitialData($tableName)
    {
        $initialData = $this->getInitialData($tableName);
        if ($initialData === null) {
            error_log("테이블 {$tableName}의 초기 데이터가 없습니다.");
            return;
        }

        $prefixedTableName = $this->getTableName($tableName);
        $data = $initialData['data'] ?? [];
        $encryptConfig = $initialData['encrypt'] ?? [];
        $insertedCount = 0;
        $errorCount = 0;

        if (empty($data)) {
            error_log("테이블 {$tableName}의 초기 데이터가 비어 있습니다.");
            return;
        }

        $tableSchema = $this->getTableSchema($prefixedTableName);

        foreach ($data as $index => $row) {
            try {
                $preparedRow = $this->prepareRowData($row, $tableSchema, $encryptConfig);
                
                error_log("Inserting data for table {$prefixedTableName}: " . print_r($preparedRow, true));

                $result = $this->db->sqlBindQuery('insert', $prefixedTableName, $preparedRow);

                if ($result['result'] === 'success') {
                    $insertedCount++;
                    error_log("테이블 {$prefixedTableName}의 {$index}번째 행 삽입 성공. Insert ID: " . $result['insertId']);
                } else {
                    $errorCount++;
                    error_log("테이블 {$prefixedTableName}의 {$index}번째 행 삽입 실패: " . $result['message']);
                }
            } catch (\Exception $e) {
                $errorCount++;
                error_log("테이블 {$prefixedTableName}의 {$index}번째 행 처리 중 예외 발생: " . $e->getMessage());
            }
        }

        $this->logInsertionResult($prefixedTableName, $insertedCount, $errorCount);
    }
    
    /**
     * 테이블 스키마를 가져옵니다.
     * 데이터베이스에서 테이블의 현재 스키마 정보를 조회합니다.
     */
    private function getTableSchema($tableName)
    {
        $stmt = $this->db->getPdoInstance()->query("DESCRIBE `{$tableName}`");
        $schema = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $schema[$row['Field']] = $row['Type'];
        }
        return $schema;
    }
    
    /**
     * 행 데이터를 준비합니다.
     * 스키마에 맞게 데이터 타입을 변환하고 필요시 암호화합니다.
     */
    private function prepareRowData($row, $tableSchema, $encryptConfig)
    {
        $preparedRow = [];
        foreach ($row as $key => $value) {
            if (isset($encryptConfig[$key])) {
                switch ($encryptConfig[$key]) {
                    case 'password':
                        $value = CryptoHelper::hashPassword($value);
                        break;
                    case 'encrypt':
                        $value = CryptoHelper::encrypt($value);
                        break;
                    default:
                        error_log("Unknown encryption type for field: $key");
                        break;
                }
            }
            $fieldType = $tableSchema[$key] ?? '';
            $preparedRow[$key] = $this->castValueToType($value, $fieldType);
        }
        return $preparedRow;
    }
    
     /**
     * 값을 지정된 타입으로 변환합니다.
     * 필드 타입에 따라 값을 적절한 PHP 타입으로 캐스팅합니다.
     */
    private function castValueToType($value, $fieldType)
    {
        if (strpos($fieldType, 'int') !== false) {
            return (int)$value;
        } elseif (strpos($fieldType, 'float') !== false || strpos($fieldType, 'double') !== false || strpos($fieldType, 'decimal') !== false) {
            return (float)$value;
        } elseif (strpos($fieldType, 'datetime') !== false) {
            return date('Y-m-d H:i:s', strtotime($value));
        } elseif (strpos($fieldType, 'date') !== false) {
            return date('Y-m-d', strtotime($value));
        } elseif (strpos($fieldType, 'time') !== false) {
            return date('H:i:s', strtotime($value));
        } elseif (strpos($fieldType, 'bool') !== false) {
            return $value ? 1 : 0;
        } else {
            return (string)$value;
        }
    }
    
     /**
     * 삽입 결과를 로깅합니다.
     * 데이터 삽입 결과를 로그에 기록합니다.
     */
    private function logInsertionResult($tableName, $insertedCount, $errorCount)
    {
        if ($errorCount > 0) {
            error_log("테이블 {$tableName}에 {$insertedCount}개의 초기 데이터 삽입 완료, {$errorCount}개의 오류 발생");
        } else {
            error_log("테이블 {$tableName}에 {$insertedCount}개의 초기 데이터 삽입 완료");
        }

        if ($insertedCount === 0) {
            error_log("테이블 {$tableName}에 초기 데이터 삽입 실패: 모든 삽입 시도가 실패했습니다.");
        }
    }
}