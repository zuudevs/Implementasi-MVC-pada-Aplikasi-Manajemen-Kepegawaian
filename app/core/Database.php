<?php

class Database {
    private string $host     = DB_HOST;
    private string $dbname   = DB_NAME;
    private string $username = DB_USER;
    private string $password = DB_PASS;
    private string $charset  = 'utf8mb4';

    private ?PDO $conn = null;
    private mixed $stmt = null;

    public function connect(): PDO {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]));
            }
        }
        return $this->conn;
    }

    public function query(string $sql): static {
        $this->stmt = $this->connect()->prepare($sql);
        return $this;
    }

    public function bind(string $param, mixed $value, ?int $type = null): static {
        if ($type === null) {
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    public function execute(): bool {
        return $this->stmt->execute();
    }

    public function resultSet(): array {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function single(): mixed {
        $this->execute();
        return $this->stmt->fetch();
    }

    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    public function lastInsertId(): string|false {
        return $this->connect()->lastInsertId();
    }
}
