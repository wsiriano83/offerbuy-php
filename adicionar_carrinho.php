<?php
session_start();
require_once 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/Product.php';

// 1. Verificação de Autenticação
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=auth');
    exit;
}

// 2. Coleta de dados do POST (Usando 'product_id' para compatibilidade)
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if ($product_id > 0) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $cart = new Cart($db);
        $product = new Product($db);
        
        // 3. Verificar se produto existe
        $product->id = $product_id;
        if ($product->readOne()) {
            
            // 4. Verificar estoque
            if ($product->estoque >= $quantity) {
                $cart->usuario_id = $_SESSION['user_id'];
                $cart->produto_id = $product_id;
                $cart->quantidade = $quantity;
                
                // 5. Adicionar ao Carrinho
                if ($cart->addToCart()) {
                    header('Location: carrinho.php?success=1');
                } else {
                    // Erro no método addToCart (pode ser problema de SQL)
                    header('Location: carrinho.php?error=1');
                }
            } else {
                // Produto sem estoque suficiente
                header('Location: carrinho.php?error=estoque');
            }
        } else {
            // Produto não encontrado no banco
            header('Location: carrinho.php?error=produto');
        }
    } catch (Exception $e) {
        // Loga o erro e redireciona para um erro genérico
        error_log("Erro fatal ao adicionar ao carrinho: " . $e->getMessage());
        header('Location: carrinho.php?error=fatal');
    }
} else {
    // ID do produto inválida (product_id <= 0)
    header('Location: carrinho.php?error=2');
}
exit;
?>