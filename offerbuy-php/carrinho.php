<?php
// Verifica se a sessão já não está ativa antes de iniciar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Cart.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$product = new Product($db);
$cart = new Cart($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Lógica do carrinho
$cartItems = [];
$total = 0;

if ($isLoggedIn) {
    $cartItems = $cart->getByUser($_SESSION['user_id']);
    
    // Calcular total
    foreach ($cartItems as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
}

// Processar ações do carrinho via POST (não AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) {
        $_SESSION['error'] = "Faça login para gerenciar seu carrinho";
        header('Location: login.php');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    switch ($action) {
        case 'add':
            $quantity = $_POST['quantity'] ?? 1;
            
            // Verificar se o produto existe
            $product->id = $product_id;
            if ($product->readOne()) {
                // Verificar estoque
                if ($product->estoque >= $quantity) {
                    $cart->usuario_id = $user_id;
                    $cart->produto_id = $product_id;
                    $cart->quantidade = $quantity;
                    
                    if ($cart->addToCart()) {
                        $_SESSION['success'] = "Produto adicionado ao carrinho com sucesso!";
                    } else {
                        $_SESSION['error'] = "Erro ao adicionar produto ao carrinho.";
                    }
                } else {
                    $_SESSION['error'] = "Quantidade indisponível em estoque. Disponível: " . $product->estoque;
                }
            } else {
                $_SESSION['error'] = "Produto não encontrado.";
            }
            break;
            
        case 'update':
            $quantity = $_POST['quantity'] ?? 1;
            
            if ($quantity <= 0) {
                // Remove se quantidade for 0
                if ($cart->removeItem($user_id, $product_id)) {
                    $_SESSION['success'] = "Produto removido do carrinho.";
                } else {
                    $_SESSION['error'] = "Erro ao remover produto do carrinho.";
                }
            } else {
                // Verificar estoque
                $product->id = $product_id;
                if ($product->readOne() && $product->estoque >= $quantity) {
                    if ($cart->updateQuantity($user_id, $product_id, $quantity)) {
                        $_SESSION['success'] = "Quantidade atualizada.";
                    } else {
                        $_SESSION['error'] = "Erro ao atualizar quantidade.";
                    }
                } else {
                    $_SESSION['error'] = "Quantidade indisponível em estoque. Disponível: " . ($product->estoque ?? 0);
                }
            }
            break;
            
        case 'remove':
            if ($cart->removeItem($user_id, $product_id)) {
                $_SESSION['success'] = "Produto removido do carrinho.";
            } else {
                $_SESSION['error'] = "Erro ao remover produto do carrinho.";
            }
            break;
            
        case 'clear':
            if ($cart->clearCart($user_id)) {
                $_SESSION['success'] = "Carrinho limpo com sucesso.";
            } else {
                $_SESSION['error'] = "Erro ao limpar carrinho.";
            }
            break;
    }
    
    header('Location: carrinho.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .cart-items {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 8px;
            margin-right: 20px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .item-price {
            color: #FB8929;
            font-weight: bold;
            font-size: 18px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .quantity-btn {
            background: #f5f5f5;
            border: 1px solid #ddd;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .cart-summary {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
            text-align: center;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .total-row {
            font-size: 20px;
            font-weight: bold;
            color: #FB8929;
            border-bottom: none;
        }
        
        .checkout-btn {
            background: #FB8929;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: auto;
            min-width: 200px;
            margin-top: 20px;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .checkout-btn:hover {
            background: #e57a24;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-cart i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .continue-shopping {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
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

        .stock-warning {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
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
            <div class="cart-header">
                <h1>Meu Carrinho</h1>
                <a href="index.php" class="continue-shopping">
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

            <?php if (empty($cartItems) || !$isLoggedIn): ?>
                <div class="empty-cart">
                    <i class='bx bx-cart'></i>
                    <h2>Seu carrinho está vazio</h2>
                    <p>Adicione alguns produtos incríveis ao seu carrinho!</p>
                    <?php if (!$isLoggedIn): ?>
                        <p style="color: #dc3545; margin-top: 10px;">Faça login para ver seu carrinho</p>
                        <button id="openPopupBtn" style="background: #FB8929; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                            Fazer Login
                        </button>
                    <?php else: ?>
                        <a href="index.php" class="continue-shopping">
                            Começar a Comprar
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo $item['imagem_url'] ?: 'img/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['nome']); ?>" 
                                 class="item-image"
                                 onerror="this.src='img/placeholder.jpg'">
                            
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['nome']); ?></div>
                                <div class="item-price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                                
                                <?php if ($item['quantidade'] > $item['estoque']): ?>
                                    <div class="stock-warning">
                                        ⚠️ Quantidade no carrinho excede estoque disponível (<?php echo $item['estoque']; ?>)
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="quantity-controls">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['produto_id']; ?>">
                                    
                                    <button type="button" class="quantity-btn minus" 
                                            onclick="this.nextElementSibling.stepDown(); this.nextElementSibling.onchange();">
                                        <i class='bx bx-minus'></i>
                                    </button>
                                    
                                    <input type="number" name="quantity" 
                                           value="<?php echo $item['quantidade']; ?>" 
                                           min="0" max="<?php echo $item['estoque']; ?>" 
                                           class="quantity-input"
                                           onchange="this.form.submit()">
                                    
                                    <button type="button" class="quantity-btn plus"
                                            onclick="if(parseInt(this.previousElementSibling.value) < <?php echo $item['estoque']; ?>) { this.previousElementSibling.stepUp(); this.previousElementSibling.onchange(); }">
                                        <i class='bx bx-plus'></i>
                                    </button>
                                </form>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['produto_id']; ?>">
                                <button type="submit" class="remove-btn">
                                    <i class='bx bx-trash'></i> Remover
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Frete:</span>
                        <span>Grátis</span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span>Total:</span>
                        <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                    </div>
                    
                    <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                        <i class='bx bx-credit-card'></i> Finalizar Compra
                    </button>
                    
                    <form method="POST" style="text-align: center; margin-top: 15px;">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="remove-btn" 
                                onclick="return confirm('Tem certeza que deseja esvaziar o carrinho?')">
                            <i class='bx bx-trash'></i> Esvaziar Carrinho
                        </button>
                    </form>
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

            // Botão de login no carrinho vazio
            const openPopupBtn = document.getElementById('openPopupBtn');
            if (openPopupBtn) {
                openPopupBtn.addEventListener('click', function() {
                    window.location.href = 'index.php#popup';
                });
            }

            // Prevenir envio duplo de formulários
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        setTimeout(() => {
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });
        });
    </script>
</body>
</html>