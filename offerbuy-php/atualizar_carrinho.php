<?php
session_start();
require_once 'config/database.php';
require_once 'models/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $cart = new Cart($db);
    
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($product_id > 0 && $quantity > 0) {
        $cart->updateQuantity($_SESSION['user_id'], $product_id, $quantity);
    }
}

header('Location: carrinho.php');
exit;
?>