<?php

namespace App\Controllers;

use App\Models\Product;
use Framework\Viewer;

class ProductController
{
    public function __construct(
        private Viewer $viewer,
        private Product $model,
    ) {
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Models/Product.php';
        $products = $this->model->getData();

        echo $this->viewer->render('shared/header.php', ['title' => 'Product']);
        echo $this->viewer->render('Products/index.php', ['products' => $products]);
    }

    public function show(string $id)
    {
        echo $this->viewer->render('shared/header.php', ['title' => 'Product']);
        echo $this->viewer->render('Products/show.php', ['id' => $id]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, ' ', $id, ' ', $page;
    }
}