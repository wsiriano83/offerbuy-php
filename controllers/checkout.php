<?php
session_start();
header('Content-Type: application/json');

include_once '../config/database.php';
include_once '../models/Cart.php';
include_once '../models/Order.php';

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$order = new Order($db);

$response = array('success' => false, 'message' => '', 'order_id' => 0, 'numero_pedido' => '');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Você precisa estar logado para finalizar a compra.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Buscar itens do carrinho
    $cart_items = $cart->getByUser($user_id);
    
    if (empty($cart_items)) {
        $response['message'] = 'Seu carrinho está vazio.';
        echo json_encode($response);
        exit;
    }

    // Calcular subtotal
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['quantidade'] * $item['preco'];
    }

    // Configurar frete (grátis)
    $frete = 0.00;

    // Criar pedido
    $order->usuario_id = $user_id;
    $order->subtotal = $subtotal;
    $order->frete = $frete;
    $order->status = 'confirmado';
    $order->metodo_pagamento = 'pix';
    
    if ($order->create()) {
        $order_id = $order->id;
        
        // SALVAR ITENS DO PEDIDO - CORREÇÃO APLICADA
        $itens_salvos = true;
        $erros_itens = [];
        
        foreach ($cart_items as $item) {
            // CORREÇÃO: Passando todos os 5 argumentos necessários
            if (!$order->addItem(
                $order_id,                    // pedido_id
                $item['produto_id'],          // produto_id
                $item['variacao_id'] ?? null, // variacao_id (pode ser null)
                $item['quantidade'],          // quantidade
                $item['preco']                // preco_unitario
            )) {
                $itens_salvos = false;
                $erros_itens[] = $item['produto_id'];
                error_log("Erro ao salvar item do pedido: " . $item['produto_id']);
            }
        }
        
        if ($itens_salvos) {
            // Limpar carrinho
            if ($cart->clearCart($user_id)) {
                $response['success'] = true;
                $response['message'] = 'Compra finalizada com sucesso!';
                $response['order_id'] = $order_id;
                $response['numero_pedido'] = $order->numero_pedido;
                
                // Log de sucesso
                error_log("SUCESSO: Pedido #$order_id criado com " . count($cart_items) . " itens para usuário $user_id");
            } else {
                $response['message'] = 'Pedido criado, mas erro ao limpar carrinho.';
                error_log("ERRO: Pedido #$order_id criado mas carrinho não foi limpo para usuário $user_id");
            }
        } else {
            $response['message'] = 'Pedido criado, mas erro ao salvar alguns itens do pedido.';
            error_log("ERRO: Pedido #$order_id criado mas itens não foram salvos: " . implode(', ', $erros_itens));
        }
    } else {
        $response['message'] = 'Erro ao criar pedido.';
        error_log("ERRO: Falha ao criar pedido para usuário $user_id");
    }
    
} catch (Exception $e) {
    error_log("Erro no checkout: " . $e->getMessage());
    $response['message'] = 'Erro interno do servidor: ' . $e->getMessage();
}

echo json_encode($response);
?>