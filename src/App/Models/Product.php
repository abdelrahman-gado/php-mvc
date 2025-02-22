<?php

declare(strict_types=1);

namespace App\Models;

use Framework\Model;
use PDO;

class Product extends Model
{
    protected ?string $table = 'products';
    
    protected function validate(array $data): void
    {
        if (empty($data['name'])) {
            $this->addError('name', 'Name is required');
        }
    }

    public function getTotal(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->getTable()}";
        $conn = $this->database->getConnection();
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}