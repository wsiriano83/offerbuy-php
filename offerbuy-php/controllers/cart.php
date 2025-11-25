<?php 
session_start();
header('Content-Type: application/json');

include_once '../config/database.php';
include_once '../models/Cart.php';
include_once '../models/Product.php';

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$product = new Product($db);

$response = array('success' => false, 'message' => '');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Você precisa estar logado para gerenciar o carrinho.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

// Determinar a ação (POST ou GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
} else {
    $action = $_GET['action'] ?? '';
}

try {
    switch ($action) {
        case 'add':
            // Adicionar item ao carrinho via POST
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            if ($product_id <= 0) {
                $response['message'] = 'Produto inválido.';
                break;
            }

            // Verificar se o produto existe
            $product->id = $product_id;
            if (!$product->readOne()) {
                $response['message'] = 'Produto não encontrado.';
                break;
            }

            // Verificar estoque
            if ($product->estoque < $quantity) {
                $response['message'] = 'Quantidade indisponível em estoque. Disponível: ' . $product->estoque;
                break;
            }

            $cart->usuario_id = $user_id;
            $cart->produto_id = $product_id;
            $cart->quantidade = $quantity;

            if ($cart->addToCart()) {
                $response['success'] = true;
                $response['message'] = 'Produto adicionado ao carrinho com sucesso!';
            } else {
                $response['message'] = 'Erro ao adicionar produto ao carrinho.';
            }
            break;

        case 'remove':
            // Remover item do carrinho
            $product_id = $_POST['product_id'] ?? 0;

            if ($product_id <= 0) {
                $response['message'] = 'Produto inválido.';
                break;
            }

            if ($cart->removeItem($user_id, $product_id)) {
                $response['success'] = true;
                $response['message'] = 'Produto removido do carrinho.';
            } else {
                $response['message'] = 'Erro ao remover produto do carrinho.';
            }
            break;

        case 'update':
            // Atualizar quantidade
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            if ($product_id <= 0) {
                $response['message'] = 'Produto inválido.';
                break;
            }

            if ($quantity <= 0) {
                // Remove se quantidade for 0
                if ($cart->removeItem($user_id, $product_id)) {
                    $response['success'] = true;
                    $response['message'] = 'Produto removido do carrinho.';
                } else {
                    $response['message'] = 'Erro ao remover produto do carrinho.';
                }
            } else {
                // Verificar estoque
                $product->id = $product_id;
                if ($product->readOne() && $product->estoque >= $quantity) {
                    if ($cart->updateQuantity($user_id, $product_id, $quantity)) {
                        $response['success'] = true;
                        $response['message'] = 'Quantidade atualizada.';
                    } else {
                        $response['message'] = 'Erro ao atualizar quantidade.';
                    }
                } else {
                    $response['message'] = 'Quantidade indisponível em estoque. Disponível: ' . ($product->estoque ?? 0);
                }
            }
            break;

        case 'clear':
            // Limpar carrinho
            if ($cart->clearCart($user_id)) {
                $response['success'] = true;
                $response['message'] = 'Carrinho limpo com sucesso.';
            } else {
                $response['message'] = 'Erro ao limpar carrinho.';
            }
            break;

        case 'get':
            // Obter itens do carrinho
            $items = $cart->getByUser($user_id);
            
            // Calcular totais
            $total_items = 0;
            $total_price = 0;
            
            foreach ($items as $item) {
                $total_items += $item['quantidade'];
                $total_price += $item['quantidade'] * $item['preco'];
            }
            
            $response['success'] = true;
            $response['items'] = $items;
            $response['total_items'] = $total_items;
            $response['total_price'] = number_format($total_price, 2, '.', '');
            $response['message'] = 'Carrinho obtido com sucesso.';
            break;

        default:
            $response['message'] = 'Ação não reconhecida. Ações disponíveis: add, remove, update, clear, get';
            break;
    }
} catch (Exception $e) {
    error_log("Erro no carrinho: " . $e->getMessage());
    $response['message'] = 'Erro interno do servidor: ' . $e->getMessage();
}

echo json_encode($response);
?>