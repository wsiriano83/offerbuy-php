<?php
session_start();
require_once 'config/database.php';
require_once 'models/Order.php';
require_once 'models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;
$order_found = false;

if ($order_id > 0) {
    $database = new Database();
    $db = $database->getConnection();
    $order = new Order($db);
    $order->id = $order_id;
    $order->usuario_id = $_SESSION['user_id'];
    
    $order_found = $order->readOne();
}

// Inclua o header da raiz
$header_path = __DIR__ . '/views/header.php';
if (file_exists($header_path)) {
    include $header_path;
} else {
    include 'header.php';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Realizado - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .success-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 0 20px;
            text-align: center;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .success-title {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .order-details {
            text-align: left;
            max-width: 400px;
            margin: 0 auto 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .order-detail:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .detail-value {
            color: #212529;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #FB8929;
            color: white;
        }

        .btn-primary:hover {
            background: #e57a24;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .success-container {
                margin-top: 80px;
                padding: 0 15px;
            }

            .success-card {
                padding: 30px 20px;
            }

            .btn-group {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class='bx bx-check-circle'></i>
            </div>
            
            <h1 class="success-title">Pedido Realizado com Sucesso!</h1>
            
            <?php if ($order_found): ?>
                <div class="order-details">
                    <div class="order-detail">
                        <span class="detail-label">Número do Pedido:</span>
                        <span class="detail-value">#<?php echo $order->id; ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Número Único:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order->numero_pedido); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Subtotal:</span>
                        <span class="detail-value">R$ <?php echo number_format($order->subtotal, 2, ',', '.'); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Frete:</span>
                        <span class="detail-value">R$ <?php echo number_format($order->frete, 2, ',', '.'); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Total:</span>
                        <span class="detail-value" style="color: #28a745; font-weight: 600;">
                            R$ <?php echo number_format($order->total, 2, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><?php echo ucfirst($order->status); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Método de Pagamento:</span>
                        <span class="detail-value"><?php echo ucfirst($order->metodo_pagamento); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="detail-label">Data:</span>
                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($order->data_pedido)); ?></span>
                    </div>
                </div>
                
                <p style="font-size: 16px; color: #666; margin-bottom: 30px;">
                    Você receberá um email com os detalhes do pedido. Acompanhe o status em "Meus Pedidos".
                </p>
            <?php else: ?>
                <p style="font-size: 16px; color: #666; margin-bottom: 30px;">
                    Obrigado pelo seu pedido! Em breve você receberá um email com os detalhes.
                </p>
            <?php endif; ?>
            
            <div class="btn-group">
                <a href="pedidos.php" class="btn btn-primary">
                    <i class='bx bx-package'></i> Ver Meus Pedidos
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class='bx bx-shopping-bag'></i> Continuar Comprando
                </a>
            </div>
        </div>
    </div>

    <?php 
    // Inclua o footer
    $footer_path = __DIR__ . '/views/footer.php';
    if (file_exists($footer_path)) {
        include $footer_path;
    } else {
        include 'footer.php';
    }
    ?>
</body>
</html>