<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use Framework\Controller;
use Framework\Exceptions\PageNotFoundException;

class ProductController extends Controller
{
    public function __construct(
        private Product $model,
    ) {
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Models/Product.php';
        $products = $this->model->findAll();

        echo $this->viewer->render('Products/index.mvc.php', [
            'title' => 'Products',
            'products' => $products,
            'total' => $this->model->getTotal(),
        ]);
    }

    public function show(string $id)
    {
        $product = $this->getProduct($id);

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
            'name' => $this->request->post['name'],
            'description' => empty($this->request->post['description']) ?
                null : $this->request->post['description'],
        ];

        if ($this->model->insert($data)) {
            $id = $this->model->getInsertedID();
            header("Location: /products/{$id}/show");
            exit();
        } else {
            echo $this->viewer->render('shared/header.php', ['title' => 'New Product']);
            echo $this->viewer->render('Products/new.php', [
                'errors' => $this->model->getErrors(),
                'product' => $data,
            ]);
        }
    }

    public function edit(string $id)
    {
        $product = $this->getProduct($id);

        echo $this->viewer->render('shared/header.php', ['title' => 'Edit Product']);
        echo $this->viewer->render('Products/edit.php', ['product' => $product]);
    }

    public function update(string $id)
    {
        $product = $this->getProduct($id);
        $product['name'] = $_POST['name'];
        $product['description'] = empty($this->request->post['description']) ? null : $this->request->post['description'];

        if ($this->model->update($id, $product)) {
            header("Location: /products/{$id}/show");
            exit();
        } else {
            echo $this->viewer->render('shared/header.php', ['title' => 'Edit Product']);
            echo $this->viewer->render('Products/edit.php', [
                'errors' => $this->model->getErrors(),
                'product' => $product,
            ]);
        }
    }

    private function getProduct(string $id): array
    {
        $product = $this->model->find($id);
        if ($product === false) {
            throw new PageNotFoundException("Product not found");
        }

        return $product;
    }

    public function delete(string $id)
    {
        $product = $this->getProduct($id);
        echo $this->viewer->render('shared/header.php', ['title' => 'Delete Product']);
        echo $this->viewer->render('Products/delete.php', ['product' => $product]);
    }

    public function destroy(string $id)
    {
        $product = $this->getProduct($id);
        $this->model->delete($id);
        header("Location: /products");
        exit();
    }
}