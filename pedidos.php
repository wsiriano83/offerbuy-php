<?php
// Verifica se a sessão já não está ativa antes de iniciar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Order.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$order = new Order($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Redirecionar se não estiver logado
if (!$isLoggedIn) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$pedidos = $order->getByUserWithItens($user_id);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .order-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .order-number {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        
        .order-date {
            color: #666;
            font-size: 14px;
        }
        
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-confirmado {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelado {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-enviado {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-entregue {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .order-items {
            padding: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .item-quantity {
            color: #666;
            font-size: 14px;
        }
        
        .item-price {
            font-weight: bold;
            color: #333;
        }
        
        .order-footer {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .order-total {
            font-size: 18px;
            font-weight: bold;
            color: #FB8929;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            color: #666;

            display: flex;
            flex-direction: column;
            gap: 15px;

        }
        
        .empty-orders i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .payment-method {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .notification.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .notification.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="img/Allin onde sem fondo.jpg" alt="OfferBuy Logo">
        </a>

        <ul class="navbar">
            <li><a href="categoria.php?id=1&nome=roupas">Roupas</a></li>
            <li><a href="categoria.php?id=2&nome=eletronicos">Eletrônicos</a></li>
            <li><a href="categoria.php?id=3&nome=acessorios">Acessórios</a></li>
            <li><a href="categoria.php?id=4&nome=beleza-moda">Beleza & Moda</a></li>
            <li><a href="categoria.php?id=5&nome=eletrodomesticos">Eletrodomésticos</a></li>
            <li><a href="categoria.php?id=6&nome=ferramentas">Ferramentas</a></li>
        </ul>

        <div class="h-btn">
            <?php if($isLoggedIn): ?>
                <button id="userMenuBtn">Olá, <?php echo htmlspecialchars($userName); ?></button>
                <div class="user-menu" id="userMenu">
                    <a href="perfil.php"><i class='bx bx-user'></i> Meu Perfil</a>
                    <a href="pedidos.php"><i class='bx bx-package'></i> Meus Pedidos</a>
                    <a href="carrinho.php"><i class='bx bx-cart'></i> Meu Carrinho</a>
                    <a href="logout.php"><i class='bx bx-log-out'></i> Sair</a>
                </div>
            <?php else: ?>
                <button id="openPopupBtn">Login / Cadastro</button>
            <?php endif; ?>

            <a href="carrinho.php" class="cart-icon">
                <i class='bx bx-cart' style="font-size: 24px;"></i>
                <span id="cart-count" class="cart-count">0</span>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Meus Pedidos</h1>
                <a href="index.php" class="btn btn-secondary">
                    ← Continuar Comprando
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="notification success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="notification error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($pedidos)): ?>
                <div class="empty-orders">
                    <i class='bx bx-package'></i>
                    <h2>Nenhum pedido encontrado</h2>
                    <p>Você ainda não fez nenhum pedido.</p>
                    <a href="index.php" class="btn btn-secondary" style="margin-top: 20px;">
                        Fazer Minha Primeira Compra
                    </a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-number">Pedido #<?php echo htmlspecialchars($pedido['numero_pedido']); ?></div>
                                    <div class="order-date">Data: <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></div>
                                    <div class="payment-method">
                                        Pagamento: <?php echo htmlspecialchars($pedido['metodo_pagamento']); ?>
                                    </div>
                                </div>
                                <div class="order-status status-<?php echo htmlspecialchars($pedido['status']); ?>">
                                    <?php 
                                    $statusText = [
                                        'confirmado' => 'Confirmado',
                                        'pendente' => 'Pendente',
                                        'cancelado' => 'Cancelado',
                                        'enviado' => 'Enviado',
                                        'entregue' => 'Entregue'
                                    ];
                                    echo $statusText[$pedido['status']] ?? $pedido['status'];
                                    ?>
                                </div>
                            </div>
                            
                            <div class="order-items">
                                <?php if (!empty($pedido['itens'])): ?>
                                    <?php foreach ($pedido['itens'] as $item): ?>
                                        <div class="order-item">
                                            <img src="<?php echo $item['imagem_url'] ?: 'img/placeholder.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['produto_nome']); ?>" 
                                                 class="item-image"
                                                 onerror="this.src='img/placeholder.jpg'">
                                            
                                            <div class="item-details">
                                                <div class="item-name"><?php echo htmlspecialchars($item['produto_nome']); ?></div>
                                                <div class="item-quantity">Quantidade: <?php echo $item['quantidade']; ?></div>
                                            </div>
                                            
                                            <div class="item-price">
                                                R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; padding: 20px; color: #666;">
                                        <i class='bx bx-info-circle'></i>
                                        <p>Nenhum item encontrado para este pedido</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="order-footer">
                                <div class="order-total">
                                    Total: R$ <?php echo number_format(($pedido['subtotal'] + $pedido['frete']), 2, ',', '.'); ?>
                                    <?php if ($pedido['frete'] > 0): ?>
                                        <small style="display: block; font-size: 12px; color: #666;">
                                            (Produtos: R$ <?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?> 
                                            + Frete: R$ <?php echo number_format($pedido['frete'], 2, ',', '.'); ?>)
                                        </small>
                                    <?php else: ?>
                                        <small style="display: block; font-size: 12px; color: #28a745;">
                                            ✓ Frete Grátis
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="contact">
            <div class="contact-content">
                <img src="img/Allin onde sem fondo.jpg" alt="OfferBuy Logo">
                <p>Oferecemos os melhores produtos com os melhores preços. Sua satisfação é nossa prioridade.</p>
            </div>
            <div class="contact-content">
                <h4>Sobre OfferBuy</h4>
                <li><a href="sobre.php">Sobre nós</a></li>
                <li><a href="politica.php">Política OfferBuy</a></li>
                <li><a href="privacidade.php">Política de Privacidade</a></li>
                <li><a href="trocas.php">Trocas e Devoluções</a></li>
            </div>
            <div class="contact-content">
                <h4>Minha Conta</h4>
                <li><a href="perfil.php">Meu Perfil</a></li>
                <li><a href="pedidos.php">Meus Pedidos</a></li>
                <li><a href="carrinho.php">Meu Carrinho</a></li>
            </div>
            <div class="contact-content">
                <h4>Atendimento</h4>
                <li><a href="ajuda.php">Central de Ajuda</a></li>
                <li><a href="contato.php">Fale Conosco</a></li>
                <li><i class='bx bx-phone'></i> (11) 4004-4004</li>
            </div>
            <div class="contact-content">
                <h4>Métodos de Pagamento</h4>
                <div class="payment-methods">
                    <i class='bx bxl-paypal'></i>
                    <i class='bx bx-credit-card'></i>
                    <i class='bx bxl-visa'></i>
                    <i class='bx bxl-mastercard'></i>
                </div>
                <p>Pix, Cartão, Boleto</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 OfferBuy. Todos os direitos reservados. CNPJ: 12.345.678/0001-99</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu mobile
            const menuIcon = document.getElementById('menu-icon');
            const navbar = document.querySelector('.navbar');
            
            if (menuIcon) {
                menuIcon.addEventListener('click', function() {
                    navbar.classList.toggle('open');
                });
            }

            // Menu do usuário
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');
            
            if (userMenuBtn && userMenu) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
                });

                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.style.display = 'none';
                    }
                });
            }

            // Atualizar contador do carrinho
            function atualizarContadorCarrinho() {
                fetch('controllers/cart.php?action=get')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.total_items;
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar contador:', error);
                });
            }

            // Atualizar contador quando a página carrega
            atualizarContadorCarrinho();
        });
    </script>
</body>
</html>