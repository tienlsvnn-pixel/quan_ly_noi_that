<?php
require_once "models/Product.php";

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->getAll();
        require "views/product_list.php";
    }

    public function detail($id)
    {
        $product = $this->productModel->getById($id);
        require "views/product_detail.php";
    }
}