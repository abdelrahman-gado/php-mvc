<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use Framework\Controller;
use Framework\Exceptions\PageNotFoundException;
use Framework\Response;

class ProductController extends Controller
{
    public function __construct(
        private Product $model,
    ) {
    }

    public function index(): Response
    {
        require_once __DIR__ . '/../Models/Product.php';
        $products = $this->model->findAll();

       return $this->view('Products/index.mvc.php', [
        'products' => $products,
        'total' => $this->model->getTotal(),
       ]); 
    }

    public function show(string $id): Response
    {
        $product = $this->getProduct($id);

        return $this->view('Products/show.mvc.php', ['product' => $product]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, ' ', $id, ' ', $page;
    }

    public function new(): Response
    {
        return $this->view('Products/new.mvc.php');
    }

    public function create(): Response 
    {
        $data = [
            'name' => $this->request->post['name'],
            'description' => empty($this->request->post['description']) ?
                null : $this->request->post['description'],
        ];

        if ($this->model->insert($data)) {
            $id = $this->model->getInsertedID();
            return $this->redirect("/products/{$id}/show");
        } else {
            return $this->view('Products/new.mvc.php', [
                'errors' => $this->model->getErrors(),
                'product' => $data,
            ]);
        }
    }

    public function edit(string $id)
    {
        $product = $this->getProduct($id);

        echo $this->viewer->render('Products/edit.mvc.php', ['product' => $product]);
    }

    public function update(string $id)
    {
        $product = $this->getProduct($id);
        $product['name'] = $_POST['name'];
        $product['description'] = empty($this->request->post['description']) ? null : $this->request->post['description'];

        if ($this->model->update($id, $product)) {
            return $this->redirect("/products/{$id}/show");
        } else {
            echo $this->viewer->render('Products/edit.mvc.php', [
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

    public function delete(string $id): Response 
    {
        $product = $this->getProduct($id);
        return $this->view('Products/delete.mvc.php', ['product' => $product]);
    }

    public function destroy(string $id): Response
    {
        $product = $this->getProduct($id);
        $this->model->delete($id);
        return $this->redirect("Location: /products");
    }

    public function responseCodeExample(): Response
    {
        $this->response->setStatusCode(451);
        $this->response->setBody('Unavailable for legal reasons');
        return $this->response;
    }
}