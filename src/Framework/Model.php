<?php

declare(strict_types=1);

namespace Framework;
use App\Database;
use PDO;

abstract class Model
{
    protected ?string $table;
    protected array $errors = [];

    public function __construct(private Database $database)
    {
    }

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function validate(array $data): void
    {
    }

    public function getTable(): string
    {
        if ($this->table !== null) {
            return $this->table;
        }

        $parts = explode('\\', static::class);
        return strtolower(end($parts)) . 's';
    }

    public function findAll(): array
    {
        $pdo = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->getTable()}";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    }

    public function find(string $id): array|bool
    {
        $conn = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->getTable()} WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInsertedID(): string
    {
        $conn = $this->database->getConnection();
        return $conn->lastInsertId();
    }

    public function insert(array $data): bool
    {
        $this->validate($data);
        if (!empty($this->errors)) {
            return false;
        }

        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->getTable()} ({$cols}) 
            VALUES ({$placeholders})";

        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql);
        
        $i = 1;
        foreach (array_values($data) as $value) {
            $type = match(gettype($value)) {
                'integer' => PDO::PARAM_INT,
                'boolean' => PDO::PARAM_BOOL,
                'NULL' => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };

            $stmt->bindValue($i++, $value, $type);
        }

        return $stmt->execute();
    }
}