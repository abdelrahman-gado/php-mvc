<?php

namespace App\Models;

use App\Database;
use PDO;

class Product
{
    public function __construct(private Database $database)
    {
    }

    public function getData(): array
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    }
}