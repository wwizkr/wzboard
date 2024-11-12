<?php
// /src/Core/DatabaseQuery.php

namespace Web\PublicHtml\Core;

use PDO;
use PDOException;
use Exception;
use Web\PublicHtml\Traits\DatabaseHelperTrait;

class DatabaseQuery
{
    use DatabaseHelperTrait;

    private static $instance = null;
    private $pdo;
    private $config;

    /**
     * Database 클래스 생성자
     * @param array $config 데이터베이스 연결 설정
     */
    private function __construct()
    {
        $this->config = [
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD']
        ];
        $this->connect();
    }

    /**
     * 싱글톤 패턴을 구현한 인스턴스 획득 메서드
     * 클래스의 단일 인스턴스를 반환하거나, 없는 경우 새로 생성합니다.
     * 
     * @return self DatabaseQuery 클래스의 인스턴스
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 데이터베이스 연결 메서드
     * @throws Exception 연결 실패 시 예외 발생
     */
    private function connect(): void
    {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $this->config['user'], $this->config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $dsn);
        }
    }

    /**
     * SQL 쿼리 실행 메서드
     * @param string $mode 쿼리 모드 (select, insert, update, delete)
     * @param string $table 대상 테이블 이름
     * @param array $param 쿼리 파라미터
     * @param array $where WHERE 조건
     * @param array $options 추가 옵션 (필드, 정렬, 조인 등)
     * @return array 쿼리 결과
     * @throws Exception 쿼리 실행 실패 시 예외 발생
     */
    public function sqlBindQuery(string $mode, string $table, array $param = [], array $where = [], array $options = []): array
    {
        // 테이블 이름에 접두사 처리
        $table = $this->getTableName($table);

        // 기본 옵션 설정 (PHP 7.1+ 문法 사용)
        $options += [
            'field' => '*',
            'order' => '',
            'rawWhere' => '',
            'addField' => '',
            'addWhere' => '',
            'joins' => [],
            'limit' => '',
            'groupBy' => '',
            'having' => '',
            'rawSql' => null,
            'values' => null
        ];

        // Raw SQL 쿼리 실행 (직접 SQL 문을 실행할 때 사용)
        if ($options['rawSql']) {
            try {
                $stmt = $this->pdo->prepare($options['rawSql']);
                $stmt->execute($options['values'] ?? []);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Raw SQL 쿼리 실패: " . $e->getMessage());
            }
        }

        // 모드와 테이블 이름 확인
        if (!$mode || !$table) {
            throw new Exception('모드와 테이블 이름은 필수입니다');
        }

        $mode = strtolower($mode);
        $sql = '';
        $values = [];
        $sqlSet = [];
        $columns = array_keys($param);

        // 쿼리 파라미터 처리 (수정된 부분)
        foreach ($param as $key => $val) {
            if (is_array($val) && isset($val[0]) && $val[0] === 'r') {
                $sqlSet[] = "$key = {$val[1]}";
            } else {
                $sqlSet[] = "$key = ?";
                if (is_array($val)) {
                    if (isset($val[1])) {
                        $values[] = $val[1];
                    } elseif ($val[0] === 'i') {
                        $values[] = 0;
                    } else {
                        $values[] = '';
                    }
                } else {
                    $values[] = $val;
                }
            }
        }

        // WHERE 조건 처리
        $sqlWhere = '';
        foreach ($where as $key=>$val) {
            $type = $val[0] ?? null;
            $value = $val[1] ?? null;
            $condition = $val[2] ?? 'AND';
            $operator = $val[3] ?? '=';

            $sqlWhere .= $sqlWhere ? " $condition " : ' WHERE ';

            switch (strtolower($operator)) {
                case 'like':
                    $sqlWhere .= "$key LIKE ?";
                    $values[] = "%$value%";
                    break;
                case 'like_left':
                    $sqlWhere .= "$key LIKE ?";
                    $values[] = "%$value";
                    break;
                case 'like_right':
                    $sqlWhere .= "$key LIKE ?";
                    $values[] = "$value%";
                    break;
                case 'in':
                    if (is_array($value)) {
                        $inValues = $value;
                    } elseif (is_string($value)) {
                        $inValues = explode(',', $value);
                    } else {
                        throw new Exception("IN 연산자에 대한 잘못된 값 형식");
                    }
                    $sqlWhere .= "$key IN (" . implode(',', array_fill(0, count($inValues), '?')) . ")";
                    $values = array_merge($values, $inValues);
                    break;
                case 'find_in_set':
                    $sqlWhere .= "FIND_IN_SET(?, $key)";
                    $values[] = $value;
                    break;
                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $sqlWhere .= "$key BETWEEN ? AND ?";
                        $values = array_merge($values, $value);
                    } elseif (is_string($value)) {
                        $betweenValues = explode(',', $value);
                        if (count($betweenValues) === 2) {
                            $sqlWhere .= "$key BETWEEN ? AND ?";
                            $values = array_merge($values, $betweenValues);
                        } else {
                            throw new Exception("BETWEEN 연산자에 대한 잘못된 값 형식");
                        }
                    } else {
                        throw new Exception("BETWEEN 연산자에 대한 잘못된 값 형식");
                    }
                    break;
                default:
                    $sqlWhere .= "$key $operator ?";
                    $values[] = $value;
            }
        }

        if ($options['rawWhere']) {
            $sqlWhere .= $sqlWhere ? " AND {$options['rawWhere']}" : " WHERE {$options['rawWhere']}";
        }

        // 추가 WHERE 조건 처리
        if ($options['addWhere']) {
            $sqlWhere .= $sqlWhere ? " AND {$options['addWhere']}" : " WHERE {$options['addWhere']}";
            $values = array_merge($values, $options['values']); //addWhere Bind 추가
        }

        // JOIN 절 처리
        $joinClause = implode(' ', array_map(function($join) {
            return " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
        }, $options['joins']));

        // LIMIT 절 처리
        $limitClause = '';
        if ($options['limit']) {
            if (is_numeric($options['limit'])) {
                $limitClause = " LIMIT {$options['limit']}";
            } else {
                $limitParts = explode(',', $options['limit']);
                if (count($limitParts) === 2) {
                    $limitClause = " LIMIT {$limitParts[0]}, {$limitParts[1]}";
                } else {
                    $limitClause = " LIMIT {$limitParts[0]}";
                }
            }
        }

        // 쿼리 모드에 따른 SQL 문 생성
        switch ($mode) {
            case 'insert':
                $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(',', array_fill(0, count($columns), '?')) . ")";
                break;
            case 'update':
                $sql = "UPDATE $table SET " . implode(', ', $sqlSet) . $sqlWhere;
                break;
            case 'delete':
                $sql = "DELETE FROM $table" . $sqlWhere;
                break;
            case 'select':
                $sql = "SELECT {$options['field']}" . ($options['addField'] ? ", {$options['addField']}" : "") . " FROM $table$joinClause$sqlWhere";
                if ($options['groupBy']) $sql .= " GROUP BY {$options['groupBy']}";
                if ($options['having']) $sql .= " HAVING {$options['having']}";
                if ($options['order']) $sql .= " ORDER BY {$options['order']}";
                $sql .= $limitClause;
                break;
            default:
                throw new Exception('유효하지 않은 쿼리 모드');
        }

        //error_log("SQL QUERY::".print_r($sql, true));
        //error_log("VALUES::".print_r($values, true));

        // 쿼리 실행
        try {
            $stmt = $this->pdo->prepare($sql);
            $this->executeStatement($stmt, $values); // 새로운 메서드 사용

            if ($mode === 'select') {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [
                    'result' => 'success',
                    'message' => '',
                    'affectedRows' => $stmt->rowCount(),
                    'ins_id' => $mode === 'insert' ? $this->pdo->lastInsertId() : null
                ];
            }
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql, $values);
        }
    }

    /**
     * PDOStatement 실행을 위한 내부 메서드
     */
    private function executeStatement(\PDOStatement $stmt, array $params = []): bool
    {
        try {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $params[$key] = json_encode($value);
                } elseif (is_object($value)) {
                    throw new \InvalidArgumentException("Invalid parameter type for key '$key': Object");
                }
            }

            $result = $stmt->execute($params);
            return $result;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $stmt->queryString, $params);
        }
    }

    /**
     * 쿼리 준비 메서드
     */
    public function prepare(string $sql): \PDOStatement
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql);
        }
    }

    /**
     * 외부에서 사용할 수 있는 쿼리 실행 메서드
     */
    public function execute(\PDOStatement $stmt, array $params = []): bool
    {
        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $stmt->queryString, $params);
        }
    }

    /**
     * 단일 행 가져오기
     */
    public function fetch(\PDOStatement $stmt): ?array
    {
        try {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $stmt->queryString);
        }
    }

    /**
     * 모든 행 가져오기
     */
    public function fetchAll(\PDOStatement $stmt): array
    {
        try {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $stmt->queryString);
        }
    }

    /**
     * 특정 열 가져오기
     */
    public function fetchColumn(string $sql, array $params = [], int $columnNumber = 0)
    {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchColumn($columnNumber);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql, $params);
        }
    }

    /**
     * 트랜잭션 시작 메서드
     */
    public function beginTransaction(): void
    {
        try {
            $this->pdo->beginTransaction();
        } catch (PDOException $e) {
            $this->handleQueryError($e, "BEGIN TRANSACTION");
        }
    }

    /**
     * 트랜잭션 커밋 메서드
     */
    public function commit(): void
    {
        try {
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->handleQueryError($e, "COMMIT");
        }
    }

    /**
     * 트랜잭션 롤백 메서드
     */
    public function rollBack(): void
    {
        try {
            $this->pdo->rollBack();
        } catch (PDOException $e) {
            $this->handleQueryError($e, "ROLLBACK");
        }
    }

    /**
     * 마지막 삽입 ID 가져오기
     */
    public function lastInsertId(): string
    {
        try {
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handleQueryError($e, "GET LAST INSERT ID");
        }
    }

    // 추가된 query 메서드
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql, $params);
        }
    }

    // 추가된 exec 메서드
    public function exec(string $sql): int
    {
        $sql = rtrim($sql, ', ');  // 마지막 콤마 제거
        if (trim($sql) === '') {
            error_log("경고: 빈 SQL 문 실행 시도");
            return 0;
        }
        
        try {
            $result = $this->pdo->exec($sql);
            return $result;
        } catch (PDOException $e) {
            $errorInfo = $this->pdo->errorInfo();
            $this->handleQueryError($e, $sql);
        }
    }

    public function getPdoInstance()
    {
        return $this->pdo; // 실제 PDO 인스턴스를 반환합니다.
    }

     /**
     * 쿼리 에러 처리 메서드
     */
    private function handleQueryError(PDOException $e, string $sql, array $params = []): void
    {
        $errorInfo = $e->errorInfo;
        $errorMessage = "Database Error: " . $e->getMessage();
        $errorMessage .= "\nSQL: " . $sql;
        $errorMessage .= "\nParameters: " . json_encode($params, JSON_PARTIAL_OUTPUT_ON_ERROR);
        $errorMessage .= "\nError Code: " . $e->getCode();
        $errorMessage .= "\nError Info: " . json_encode($errorInfo, JSON_PARTIAL_OUTPUT_ON_ERROR);

        // 올바른 정수형 오류 코드를 전달하도록 수정
        throw new Exception($errorMessage, (int)$e->getCode());
    }

    /**
     * 테이블의 필드명을 키로 하고 값은 null로 하는 배열을 반환하는 메서드
     * 
     * @param string $table 대상 테이블 이름
     * @return array 필드명 => null 형식의 배열
     * @throws Exception 테이블 필드를 가져오지 못할 경우 예외 발생
     */
    public function getTableFieldsWithNull(string $table): array
    {
        // 테이블 이름에 접두사 처리
        $table = $this->getTableName($table);

        try {
            // 테이블의 필드 정보를 가져오기 위한 쿼리 실행
            $sql = "SHOW COLUMNS FROM $table";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            // 필드명을 키로 하고 값은 null로 초기화
            $fields = [];
            while ($column = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $fields[$column['Field']] = null;
            }

            return $fields;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql);
        }
    }

    /**
    * 필드를 체크하고 없을 경우 해당 필드를 생성
    */
    private function checkedDbField(string $field, string $table, ?string $option = null, ?string $key = null): void
    {
        $table = $this->getTableName($table);
        if ($option === null) {
            $option = 'TEXT';
        }

        try {
            // 테이블에 해당 필드가 존재하는지 확인
            $sql = "SHOW COLUMNS FROM $table LIKE :field";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':field' => $field]);
            $columnExists = $stmt->fetch();

            // 필드가 존재하지 않으면 추가
            if (!$columnExists) {
                $sql = "ALTER TABLE $table ADD COLUMN $field $option";
                $this->pdo->exec($sql);
                error_log("필드 $field가 $table 테이블에 추가되었습니다.");
            }

            // 키 추가 (필드가 추가된 후 실행)
            if ($key) {
                switch (strtoupper($key)) {
                    case 'UNIQUE':
                        $sql = "ALTER TABLE $table ADD UNIQUE KEY idx_$field ($field)";
                        $this->pdo->exec($sql);
                        error_log("UNIQUE KEY가 $field 필드에 추가되었습니다.");
                        break;
                    case 'INDEX':
                        $sql = "ALTER TABLE $table ADD INDEX idx_$field ($field)";
                        $this->pdo->exec($sql);
                        error_log("INDEX가 $field 필드에 추가되었습니다.");
                        break;
                    case 'PRIMARY':
                        $sql = "ALTER TABLE $table ADD PRIMARY KEY ($field)";
                        $this->pdo->exec($sql);
                        error_log("PRIMARY KEY가 $field 필드에 추가되었습니다.");
                        break;
                    default:
                        error_log("알 수 없는 키 타입: $key");
                }
            }
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql);
        }
    }

    // 싱글톤 패턴을 위한 매직 메서드
    public function __clone() {}
    public function __wakeup() {}
}