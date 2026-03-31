<?php
require_once "controllers/ProductController.php";

$controller = new ProductController();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'detail':
        $controller->detail($id);
        break;
    default:
        $controller->index();
        break;
}