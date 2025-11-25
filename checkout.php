<?php
// Verifica se a sessão já não está ativa antes de iniciar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$cart = new Cart($db);
$order = new Order($db);
$product = new Product($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Redirecionar se não estiver logado
if (!$isLoggedIn) {
    $_SESSION['error'] = "Faça login para finalizar a compra";
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cartItems = $cart->getByUser($user_id);

// Redirecionar se o carrinho estiver vazio
if (empty($cartItems)) {
    $_SESSION['error'] = "Seu carrinho está vazio";
    header('Location: carrinho.php');
    exit;
}

// Calcular totais
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}
$frete = 0; // Frete grátis
$total = $subtotal + $frete;

// Processar o pedido quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
    
    if (empty($metodo_pagamento)) {
        $_SESSION['error'] = "Selecione um método de pagamento";
    } else {
        // Usar o controlador para processar o pedido via AJAX
        $response = array('success' => false, 'message' => '');
        
        try {
            // Fazer requisição para o controlador
            $formData = new FormData();
            $formData->append('action', 'checkout');
            $formData->append('metodo_pagamento', $metodo_pagamento);
            
            // Aqui você faria uma requisição AJAX para o controlador
            // Por enquanto, vamos processar diretamente
            require_once 'controllers/checkout.php';
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
        }
        
        .checkout-header {
            margin-bottom: 30px;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .checkout-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-title {
            margin-bottom: 20px;
            color: #333;
            font-size: 20px;
            border-bottom: 2px solid #FB8929;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .payment-method {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #FB8929;
        }
        
        .payment-method.selected {
            border-color: #FB8929;
            background: #fff5e6;
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-method i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            position: sticky;
            top: 120px;
        }
        
        .order-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        
        .order-item-details {
            flex: 1;
        }
        
        .order-item-name {
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .order-item-quantity {
            color: #666;
            font-size: 12px;
        }
        
        .order-item-price {
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-total {
            font-size: 20px;
            font-weight: bold;
            color: #FB8929;
            border-bottom: none;
            padding-top: 10px;
        }
        
        .btn-checkout {
            background: #FB8929;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background 0.3s ease;
        }
        
        .btn-checkout:hover {
            background: #e57a24;
        }
        
        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
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
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FB8929;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
                <span id="cart-count" class="cart-count"><?php echo count($cartItems); ?></span>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="checkout-header">
                <h1>Finalizar Compra</h1>
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

            <form id="checkoutForm" method="POST">
                <div class="checkout-grid">
                    <div class="checkout-left">
                        <div class="checkout-section">
                            <h2 class="section-title">Método de Pagamento</h2>
                            <div class="form-group">
                                <div class="payment-methods">
                                    <label class="payment-method">
                                        <input type="radio" name="metodo_pagamento" value="pix" required>
                                        <i class='bx bxl-paypal'></i>
                                        <span>PIX</span>
                                    </label>
                                    <label class="payment-method">
                                        <input type="radio" name="metodo_pagamento" value="cartao_credito" required>
                                        <i class='bx bx-credit-card'></i>
                                        <span>Cartão de Crédito</span>
                                    </label>
                                    <label class="payment-method">
                                        <input type="radio" name="metodo_pagamento" value="cartao_debito" required>
                                        <i class='bx bx-credit-card'></i>
                                        <span>Cartão de Débito</span>
                                    </label>
                                    <label class="payment-method">
                                        <input type="radio" name="metodo_pagamento" value="boleto" required>
                                        <i class='bx bx-barcode'></i>
                                        <span>Boleto</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-section">
                            <h2 class="section-title">Endereço de Entrega</h2>
                            <div class="form-group">
                                <p>Entregaremos os produtos no endereço cadastrado em seu perfil.</p>
                                <p><strong>Observação:</strong> Você pode atualizar seu endereço na página do seu perfil.</p>
                                <a href="perfil.php" class="btn btn-secondary" style="margin-top: 10px;">Editar Endereço</a>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-right">
                        <div class="order-summary">
                            <h2 class="section-title">Resumo do Pedido</h2>
                            
                            <div class="order-items">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo $item['imagem_url'] ?: 'img/placeholder.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['nome']); ?>" 
                                             class="order-item-image"
                                             onerror="this.src='img/placeholder.jpg'">
                                        
                                        <div class="order-item-details">
                                            <div class="order-item-name"><?php echo htmlspecialchars($item['nome']); ?></div>
                                            <div class="order-item-quantity">Qtd: <?php echo $item['quantidade']; ?></div>
                                        </div>
                                        
                                        <div class="order-item-price">
                                            R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Frete:</span>
                                <span style="color: #28a745;">Grátis</span>
                            </div>
                            
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                            
                            <button type="submit" class="btn-checkout" id="btnFinalizarCompra">
                                <i class='bx bx-credit-card'></i> Finalizar Compra
                            </button>
                            
                            <div class="loading" id="loadingCheckout">
                                <div class="loading-spinner"></div>
                                <p>Processando seu pedido...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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

            // Seleção de método de pagamento
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    // Add selected class to clicked method
                    this.classList.add('selected');
                    // Check the radio button
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });

            // Form submission with AJAX
            const checkoutForm = document.getElementById('checkoutForm');
            const btnFinalizarCompra = document.getElementById('btnFinalizarCompra');
            const loadingCheckout = document.getElementById('loadingCheckout');

            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const metodoPagamento = document.querySelector('input[name="metodo_pagamento"]:checked');
                if (!metodoPagamento) {
                    alert('Por favor, selecione um método de pagamento.');
                    return;
                }

                // Show loading
                btnFinalizarCompra.style.display = 'none';
                loadingCheckout.style.display = 'block';

                // Create FormData
                const formData = new FormData();
                formData.append('metodo_pagamento', metodoPagamento.value);

                // Send AJAX request to controller
                fetch('controllers/checkout.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success - redirect to orders page
                        window.location.href = 'pedidos.php?success=' + encodeURIComponent(data.message);
                    } else {
                        // Error - show message
                        alert('Erro: ' + data.message);
                        btnFinalizarCompra.style.display = 'block';
                        loadingCheckout.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar pedido. Tente novamente.');
                    btnFinalizarCompra.style.display = 'block';
                    loadingCheckout.style.display = 'none';
                });
            });

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