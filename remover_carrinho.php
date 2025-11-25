<?php
session_start();
require_once 'config/database.php';
require_once 'models/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$product_id = $_GET['product_id'] ?? 0;

if ($product_id > 0) {
    $database = new Database();
    $db = $database->getConnection();
    $cart = new Cart($db);
    
    $cart->removeItem($_SESSION['user_id'], $product_id);
}

header('Location: carrinho.php');
exit;
?>