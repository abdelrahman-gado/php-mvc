<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use Framework\Exceptions\PageNotFoundException;
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
        $products = $this->model->findAll();

        echo $this->viewer->render('shared/header.php', ['title' => 'Product']);
        echo $this->viewer->render('Products/index.php', ['products' => $products]);
    }

    public function show(string $id)
    {
        $product = $this->model->find($id);
        if ($product === false) {
            throw new PageNotFoundException("Product not found");
        }

        echo $this->viewer->render('shared/header.php', ['title' => 'Product']);
        echo $this->viewer->render('Products/show.php', ['product' => $product]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, ' ', $id, ' ', $page;
    }

    public function new(): void
    {
        echo $this->viewer->render('shared/header.php', ['title' => 'New Product']);
        echo $this->viewer->render('Products/new.php');
    }

    public function create()
    {
        $data = [
            'name' => $_POST['name'],
            'description' => empty($_POST['description']) ? null : $_POST['description'],
        ];

        if ($this->model->insert($data)) {
            $id = $this->model->getInsertedID();
            header("Location: /products/{$id}/show");
            exit();
        } else {
            echo $this->viewer->render('shared/header.php', ['title' => 'New Product']);
            echo $this->viewer->render('Products/new.php', [
                'errors' => $this->model->getErrors(),
            ]);
        }
    }
}