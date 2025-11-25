<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>DEBUG - FINALIZAR COMPRA</h2>";
echo "<pre>";

// Simular dados de sessão para teste (REMOVA DEPOIS)
$_SESSION['user_id'] = 1; // Altere para um ID de usuário válido no seu banco

require_once 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $cart = new Cart($db);
    $order = new Order($db);

    echo "1. Conexão com banco: OK\n";

    // Testar método create básico
    $order->usuario_id = $_SESSION['user_id'];
    $order->subtotal = 100.00;
    $order->frete = 0.00;
    $order->total = 100.00;
    $order->status = 'confirmado';
    $order->metodo_pagamento = 'pix';

    echo "2. Dados do pedido configurados\n";
    echo "   - usuario_id: {$order->usuario_id}\n";
    echo "   - subtotal: {$order->subtotal}\n";
    echo "   - metodo_pagamento: {$order->metodo_pagamento}\n";

    // Testar criação do pedido
    if ($order->create()) {
        echo "3. ✅ Pedido criado com SUCESSO!\n";
        echo "   - ID do pedido: {$order->id}\n";
        echo "   - Número do pedido: {$order->numero_pedido}\n";

        // Testar adição de item
        $item_result = $order->addItem(
            $order->id,
            1, // produto_id - altere para um ID válido no seu banco
            null, // variacao_id
            2, // quantidade
            50.00 // preco_unitario
        );

        if ($item_result) {
            echo "4. ✅ Item adicionado com SUCESSO!\n";
        } else {
            echo "4. ❌ FALHA ao adicionar item\n";
        }

        // Testar createCompleteOrder
        $itens_teste = [
            [
                'produto_id' => 1, // altere para ID válido
                'quantidade' => 1,
                'preco' => 100.00,
                'variacao_id' => null,
                'nome' => 'Produto Teste'
            ]
        ];

        $complete_result = $order->createCompleteOrder(
            $_SESSION['user_id'],
            'pix',
            100.00,
            0.00,
            $itens_teste,
            null
        );

        if ($complete_result) {
            echo "5. ✅ createCompleteOrder com SUCESSO!\n";
        } else {
            echo "5. ❌ FALHA no createCompleteOrder\n";
        }

    } else {
        echo "3. ❌ FALHA ao criar pedido\n";
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>