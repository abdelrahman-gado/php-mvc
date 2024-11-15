<?php

namespace App\Controllers;

use App\Models\Product;
use Framework\Viewer;

class ProductController
{
    public function index(): void
    {
        require_once __DIR__ . '/../Models/Product.php';
        $model = new Product();
        $products = $model->getData();

        $viewer = new Viewer();
        echo $viewer->render('shared/header.php', ['title' => 'Product']);
        echo $viewer->render('Products/index.php', ['products' => $products]);
    }

    public function show(string $id)
    {
        $viewer = new Viewer();
        echo $viewer->render('shared/header.php', ['title' => 'Product']);
        echo $viewer->render('Products/show.php', ['id' => $id]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, ' ', $id, ' ', $page;
    }
}