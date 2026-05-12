<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    public function index()
    {
        // create model
        $productModel = new Product();

        // get products
        $products = $productModel->getAllProducts();

        // load view
        require_once __DIR__ . '/../views/products/index.php';
    }
}