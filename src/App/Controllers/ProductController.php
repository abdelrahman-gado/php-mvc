<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController
{
    public function index(): void
    {
        require_once __DIR__ . '/../Models/Product.php';
        $model = new Product();
        $products = $model->getData();

        require_once __DIR__ . '/../../../views/products_index.php';
    }

    public function show(string $id)
    {
        var_dump($id);
        require_once __DIR__ . '/../../../views/products_show.php';
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, ' ', $id, ' ', $page;
    }
}