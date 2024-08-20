<?php
// 파일 위치: /home/web/public_html/src/Helper/DatabaseQuery.php

namespace Web\PublicHtml\Helper;

use PDO;
use PDOException;
use Exception;

class DatabaseQuery
{
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
            throw new Exception("연결 실패: " . $e->getMessage());
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
        // 기본 옵션 설정 (PHP 7.1+ 문法 사용)
        $options += [
            'field' => '*',
            'order' => '',
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

        // 쿼리 파라미터 처리
        foreach ($param as $key=>$val) {
            if (is_array($val) && $val[0] === 'r') {
                // 'r'은 Raw SQL 표현식을 의미
                $sqlSet[] = "$key = {$val[1]}";
            } else {
                $sqlSet[] = "$key = ?";
                $values[] = is_array($val) ? ($val[1] ?? ($val[0] === 'i' ? 0 : '')) : $val;
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
                    $inValues = is_array($value) ? $value : explode(',', $value);
                    $sqlWhere .= "$key IN (" . implode(',', array_fill(0, count($inValues), '?')) . ")";
                    $values = array_merge($values, $inValues);
                    break;
                case 'find_in_set':
                    $sqlWhere .= "FIND_IN_SET(?, $key)";
                    $values[] = $value;
                    break;
                case 'between':
                    $sqlWhere .= "$key BETWEEN ? AND ?";
                    $values = array_merge($values, is_array($value) ? $value : explode(',', $value));
                    break;
                default:
                    $sqlWhere .= "$key $operator ?";
                    $values[] = $value;
            }
        }

        // 추가 WHERE 조건 처리
        if ($options['addWhere']) {
            $sqlWhere .= $sqlWhere ? " AND {$options['addWhere']}" : " WHERE {$options['addWhere']}";
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

        //echo '<pre>';
        //var_dump($sql);
        //echo '</pre>';

        // 쿼리 실행
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);

            if ($mode === 'select') {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [
                    'result' => 'success',
                    'message' => '',
                    'affectedRows' => $stmt->rowCount(),
                    'insertId' => $mode === 'insert' ? $this->pdo->lastInsertId() : null
                ];
            }
        } catch (PDOException $e) {
            throw new Exception("데이터베이스 쿼리 실패: " . $e->getMessage());
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
            throw new Exception("쿼리 준비 실패: " . $e->getMessage());
        }
    }

    /**
     * 쿼리 실행 메서드
     */
    public function execute(\PDOStatement $stmt, array $params = []): bool
    {
        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("쿼리 실행 실패: " . $e->getMessage());
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
            throw new Exception("데이터 가져오기 실패: " . $e->getMessage());
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
            throw new Exception("데이터 가져오기 실패: " . $e->getMessage());
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
            throw new Exception("컬럼 가져오기 실패: " . $e->getMessage());
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
            throw new Exception("트랜잭션 시작 실패: " . $e->getMessage());
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
            throw new Exception("트랜잭션 커밋 실패: " . $e->getMessage());
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
            throw new Exception("트랜잭션 롤백 실패: " . $e->getMessage());
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
            throw new Exception("마지막 삽입 ID 가져오기 실패: " . $e->getMessage());
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
            throw new Exception("쿼리 실패: " . $e->getMessage());
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
            throw new Exception("실행 실패: " . $e->getMessage() . "\nSQL: " . $sql);
        }
    }

    public function getPdoInstance()
    {
        return $this->pdo; // 실제 PDO 인스턴스를 반환합니다.
    }

    // 싱글톤 패턴을 위한 매직 메서드
    public function __clone() {}
    public function __wakeup() {}
}

/*
<?php
// 데이터베이스 연결 설정
$config = [
    'host' : 'localhost',
    'user' : 'username',
    'password' : 'password',
    'database' : 'dbname'
];

$db = new Database($config);

// SELECT 쿼리 예시
$result = $db->sqlBindQuery('select', 'users', [], ['id' : ['i', 1]], ['limit' : 10]);

// INSERT 쿼리 예시
$result = $db->sqlBindQuery('insert', 'users', ['name' : ['s', 'John'], 'age' : ['i', 30]]);

// UPDATE 쿼리 예시
$result = $db->sqlBindQuery('update', 'users', ['name' : ['s', 'Jane']], ['id' : ['i', 1]]);

// DELETE 쿼리 예시
$result = $db->sqlBindQuery('delete', 'users', [], ['id' : ['i', 1]]);

// 여러 테이블 JOIN 예시
$result = $db->sqlBindQuery(
    'select',
    'users',
    [],
    ['users.registration_date' : ['s', '2023-01-01', 'AND', '>=']], // WHERE 조건
    [
        'field' : 'users.id, users.name, orders.order_id, products.product_name, order_items.quantity',
        'joins' : [
            [
                'type' : 'LEFT',
                'table' : 'orders',
                'on' : 'users.id = orders.user_id'
            ],
            [
                'type' : 'LEFT',
                'table' : 'order_items',
                'on' : 'orders.order_id = order_items.order_id'
            ],
            [
                'type' : 'LEFT',
                'table' : 'products',
                'on' : 'order_items.product_id = products.id'
            ]
        ],
        'order' : 'users.id ASC, orders.order_date DESC',
        'groupBy' : 'users.id, orders.order_id, products.id',
        'having' : 'SUM(order_items.quantity) > 0'
    ]
);

// 결과 출력
foreach ($result as $row) {
    echo "User ID: {$row['id']}, Name: {$row['name']}, ";
    echo "Order ID: {$row['order_id']}, Product: {$row['product_name']}, Quantity: {$row['quantity']}\n";
}

// 카운터 증가 예시
$result = $db->sqlBindQuery('update', 'users', 
    ['login_count' : ['e', 'login_count + 1']], 
    ['id' : ['i', 1]]
);

// 타임스탬프 설정 및 카운터 증가 예시
$result = $db->sqlBindQuery('update', 'posts', 
    [
        'last_updated' : ['r', 'NOW()'],
        'view_count' : ['e', 'view_count + 1']
    ], 
    ['id' : ['i', 123]]
);

// 값 곱하기 예시
$result = $db->sqlBindQuery('update', 'products', 
    ['price' : ['e', 'price * 1.1']], // 가격 10% 인상
    ['category' : ['s', 'electronics']]
);

// CASE 문 사용 예시
$result = $db->sqlBindQuery('update', 'orders', 
    ['status' : ['e', "CASE WHEN total_amount > 1000 THEN 'high_value' ELSE 'normal' END"]], 
    ['status' : ['s', 'pending']]
);

// SQL 인젝션 위험이 있는 사용 예시
$userInput = $_GET['id']; // 사용자 입력을 직접 받아옴
$unsafeQuery = $db->sqlBindQuery('update', 'users', 
    ['status' : ['e', "CASE WHEN id = $userInput THEN 'active' ELSE 'inactive' END"]], 
    ['status' : ['s', 'pending']]
);

// 안전한 사용 예시
$safeUserId = intval($_GET['id']); // 정수형으로 변환하여 안전하게 처리
$safeQuery = $db->sqlBindQuery('update', 'users', 
    ['status' : ['e', "CASE WHEN id = ? THEN 'active' ELSE 'inactive' END"]], 
    ['status' : ['s', 'pending']]
);
$safeQuery = $db->sqlBindQuery('update', 'users', 
    ['status' : ['s', 'active']], 
    ['id' : ['i', $safeUserId]]
);

// 동적 테이블 이름이나 컬럼 이름을 안전하게 처리하는 방법
$allowedTables = ['users', 'orders', 'products'];
$userTable = $_GET['table'];
if (in_array($userTable, $allowedTables)) {
    $query = $db->sqlBindQuery('select', $userTable, [], []);
} else {
    die("Invalid table name");
}

// Raw SQL을 안전하게 사용하는 방법
$rawSql = "SELECT * FROM users WHERE id = :id AND status = :status";
$values = [':id' : 1, ':status' : 'active'];
$result = $db->sqlBindQuery('select', '', [], [], [
    'rawSql' : $rawSql,
    'values' : $values
]);
*/