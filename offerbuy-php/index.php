<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';

echo "<!-- Executando: " . __FILE__ . " -->";
echo "<!-- Data: " . date('Y-m-d H:i:s') . " -->";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$product = new Product($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Buscar produtos em destaque
try {
    $stmt = $product->readFeatured();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $produtos = [];
    error_log("Erro ao buscar produtos em destaque: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OfferBuy - Sua Loja Online Completa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos adicionais para corrigir os problemas específicos */
        .navbar li a {
            white-space: nowrap;
        }
        
        .h-btn {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .h-btn button {
            white-space: nowrap;
        }
        
        /* Garante que Beleza & Moda não quebre */
        .navbar li:nth-child(4) a {
            white-space: nowrap;
        }

        /* CORREÇÃO DE LAYOUT: Garante que o conteúdo principal não fique sob o header fixo */
        main {
            margin-top: 80px;
        }
        
        /* Estilo para o botão de login/cadastro */
        #openPopupBtn {
            padding: 12px 18px;
            background-color: var(--main-color);
            font-size: 14px;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.4s ease;
            border-radius: 8px;
            text-align: center;
            white-space: nowrap;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 5px rgba(251, 137, 41, 0.3);
            font-family: "Work Sans", sans-serif;
            min-width: 120px;
        }
        
        #openPopupBtn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(251, 137, 41, 0.4);
        }
        
        /* Estilo para o botão do usuário quando logado */
        #userMenuBtn {
            padding: 12px 18px;
            background-color: var(--main-color);
            font-size: 14px;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.4s ease;
            border-radius: 8px;
            text-align: center;
            white-space: nowrap;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 5px rgba(251, 137, 41, 0.3);
            font-family: "Work Sans", sans-serif;
            min-width: 120px;
        }
        
        #userMenuBtn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(251, 137, 41, 0.4);
        }

        /* Estilos para a barra de pesquisa */
        .h-search {
            display: flex;
            justify-content: center;
            padding: 20px 5%;
            background: linear-gradient(135deg, #faebd7 0%, #fff5e6 100%);
        }

        .search-input {
            width: 100%;
            max-width: 600px;
            padding: 12px 20px;
            border: 2px solid #FB8929;
            border-radius: 50px 0 0 50px;
            font-size: 16px;
            outline: none;
        }

        #search-btn {
            background: #FB8929;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        #search-btn:hover {
            background: #e57a24;
        }

        /* Estilos para a seção de recursos */
        .features {
            padding: 80px 5%;
            background: #f8f8f8;
        }

        .features-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-item {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-item i {
            font-size: 3rem;
            color: #FB8929;
            margin-bottom: 20px;
        }

        .feature-item h3 {
            margin-bottom: 15px;
            color: #101821;
        }

        .feature-item p {
            color: #666;
        }

        /* Estilos para notificações */
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.3s ease-out;
            max-width: 300px;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .notification button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .notification button:hover {
            background: rgba(255,255,255,0.2);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Melhorias nos produtos */
        .product-image {
            position: relative;
        }

        .desconto {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .frete-gratis {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .preco-original {
            color: #999;
            text-decoration: line-through;
            font-size: 14px;
            margin: 5px 0;
        }

        /* Estilos para o ícone do carrinho */
        .cart-icon {
            position: relative;
            display: inline-block;
            color: #101821;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #FB8929;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        /* Botão comprar */
        .btn-comprar {
            background: #FB8929;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
            width: 100%;
            margin-top: 10px;
        }

        .btn-comprar:hover {
            background: #e57a24;
            transform: translateY(-2px);
        }

        /* ========== ESTILOS PARA O POPUP CORRIGIDO ========== */
        .popup .form-group {
            margin-bottom: 15px;
        }

        .popup .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .popup .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }

        .popup .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .popup .auth-switch {
            text-align: center;
            margin-top: 15px;
        }

        .popup .auth-switch a {
            color: #FB8929;
            text-decoration: none;
        }

        .popup .auth-switch a:hover {
            text-decoration: underline;
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

            <!-- ÍCONE DO CARRINHO ADICIONADO AQUI -->
            <a href="carrinho.php" class="cart-icon">
                <i class='bx bx-cart' style="font-size: 24px;"></i>
                <span id="cart-count" class="cart-count">0</span>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <div id="overlay"></div>
    
    <!-- Popup de Login/Cadastro CORRIGIDO -->                     
    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="closePopupBtn" class="close">&times;</span>
            
            <div class="popup-header">
                <h2>Acesso à OfferBuy</h2>
                <p>Faça login ou cadastre-se para continuar</p>
            </div>
            
            <div class="forms-container">
                <!-- Formulário de Login -->
                <div class="form" id="loginFormPopup">
                    <h3>Login</h3>
                    <div id="loginMessagePopup"></div>
                    
                    <div class="form-group">
                        <input type="email" id="popupLoginEmail" placeholder="Seu email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="popupLoginSenha" placeholder="Sua senha" required>
                    </div>
                    <button type="button" id="loginButtonPopup">Entrar na Minha Conta</button>
                    
                    <div class="auth-switch">
                        <p>Não tem conta? <a href="#" id="showSignupPopupLink">Cadastre-se</a></p>
                    </div>
                </div>
                
                <!-- Formulário de Cadastro -->
                <div class="form" id="signupFormPopup">
                    <h3>Criar Nova Conta</h3>
                    <div id="signupMessagePopup"></div>
                    
                    <div class="form-group">
                        <input type="text" id="popupSignupNome" placeholder="Seu nome completo" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="popupSignupEmail" placeholder="Seu melhor email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="popupSignupSenha" placeholder="Crie uma senha (mínimo 6 caracteres)" required minlength="6">
                    </div>
                    <div class="form-group">
                        <input type="tel" id="popupSignupTelefone" placeholder="Seu telefone (opcional)">
                    </div>
                    <button type="button" id="signupButtonPopup">Cadastrar e Criar Conta</button>
                    
                    <div class="auth-switch">
                        <p>Já tem conta? <a href="#" id="showLoginPopupLink">Fazer login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main>
        <div class="h-search">
            <form action="busca.php" method="GET">
                <input type="search" name="q" placeholder="Pesquisar produtos, marcas e categorias..." class="search-input" required>
                <input id="search-btn" type="submit" value="Procurar">
            </form>
        </div>

        <section class="home">
            <div class="home-text">
                <h1>OfferBuy</h1>
                <p>De tudo em um só lugar. Encontre os melhores produtos com os melhores preços!</p>
                <h2>Até 70% OFF</h2>
                <a href="#produtos" class="cta-button">Ver Ofertas</a>
            </div>

            <div class="home-img">
                <img src="img/camisa.png" alt="Produto em destaque">
            </div>
        </section>

        <section class="property" id="produtos">
            <div class="center-left">
                <h2>Produtos Populares</h2>
                <p>Os mais vendidos da semana</p>
            </div>
            <div class="property-content">
                <?php 
                if (count($produtos) > 0) {
                    foreach ($produtos as $produto) {
                        $desconto = '';
                        if (isset($produto['preco_original']) && $produto['preco_original'] > $produto['preco']) {
                            $percentual = (($produto['preco_original'] - $produto['preco']) / $produto['preco_original']) * 100;
                            $desconto = '<span class="desconto">-'.number_format($percentual, 0).'%</span>';
                        }
                        
                        $frete_gratis = (isset($produto['frete_gratis']) && $produto['frete_gratis']) ? '<span class="frete-gratis">Frete Grátis</span>' : '';
                        
                        echo '
                        <div class="row">
                            <div class="product-image">
                                <img src="' . ($produto['imagem_url'] ?: 'img/placeholder.jpg') . '" alt="' . $produto['nome'] . '" onerror="this.src=\'img/placeholder.jpg\'">
                                ' . $desconto . $frete_gratis . '
                            </div>
                            <h5>R$ '. number_format($produto['preco'], 2, ',', '.') . '</h5>
                            ' . (isset($produto['preco_original']) && $produto['preco_original'] > $produto['preco'] ? 
                                '<h6 class="preco-original">De: R$ '. number_format($produto['preco_original'], 2, ',', '.') . '</h6>' : '') . '
                            <p>' . htmlspecialchars($produto['nome']) . '</p>
                            <button class="btn-comprar" data-product-id="' . $produto['id'] . '" data-product-name="' . htmlspecialchars($produto['nome']) . '">
                                <i class="bx bx-cart"></i> Comprar
                            </button>
                        </div>';
                    }
                } else {
                    echo '<div class="no-products" style="text-align: center; padding: 40px; width: 100%;">
                        <h3 style="color: #666; margin-bottom: 20px;">Nenhum produto disponível</h3>
                        <p style="color: #999;">Em breve teremos novidades!</p>
                    </div>';
                }
                ?>
            </div>
        </section>

        <section class="features">
            <div class="features-container">
                <div class="feature-item">
                    <i class='bx bx-shield-alt'></i>
                    <h3>Compra Segura</h3>
                    <p>Seus dados protegidos com criptografia</p>
                </div>
                <div class="feature-item">
                    <i class='bx bxs-truck'></i>
                    <h3>Entrega Rápida</h3>
                    <p>Entregamos em todo o Brasil</p>
                </div>
                <div class="feature-item">
                    <i class='bx bx-credit-card'></i>
                    <h3>Parcele em até 12x</h3>
                    <p>No cartão de crédito</p>
                </div>
                <div class="feature-item">
                    <i class='bx bx-support'></i>
                    <h3>Suporte 24/7</h3>
                    <p>Estamos aqui para ajudar</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="contact">
            <div class="contact-content">
                <img src="img/Allin onde sem fondo.jpg" alt="Logo OfferBuy">
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
        // ========== SISTEMA PRINCIPAL ==========
        
        // Função para adicionar produto ao carrinho
        function adicionarAoCarrinho(productId, productName, quantity = 1) {
            console.log('Adicionando produto:', productId, productName, quantity);
            
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('controllers/cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ ' + data.message, 'success');
                    atualizarContadorCarrinho();
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('❌ Erro ao adicionar ao carrinho', 'error');
            });
        }

        // Função para atualizar contador do carrinho
        function atualizarContadorCarrinho() {
            fetch('controllers/cart.php?action=get')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.total_items;
                        // Adicionar animação
                        cartCount.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            cartCount.style.transform = 'scale(1)';
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar contador:', error);
            });
        }

        // Função para mostrar notificações
        function showNotification(message, type) {
            // Remove notificações existentes
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());

            // Criar nova notificação
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            `;
            
            document.body.appendChild(notification);
            
            // Remover automaticamente após 3 segundos
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 3000);
        }

        // ========== POPUP LOGIN/CADASTRO - CORREÇÃO ==========

        // Alternar entre login e cadastro no popup
        function showLoginPopup() {
            document.getElementById('loginFormPopup').style.display = 'block';
            document.getElementById('signupFormPopup').style.display = 'none';
            clearPopupMessages();
        }

        function showSignupPopup() {
            document.getElementById('loginFormPopup').style.display = 'none';
            document.getElementById('signupFormPopup').style.display = 'block';
            clearPopupMessages();
        }

        function clearPopupMessages() {
            document.getElementById('loginMessagePopup').innerHTML = '';
            document.getElementById('signupMessagePopup').innerHTML = '';
        }

        function showPopupMessage(elementId, message, type) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<div class="${type}-message">${message}</div>`;
        }

        // Login no Popup
        document.getElementById('loginButtonPopup').addEventListener('click', function() {
            console.log('🎯 Processando login do popup...');
            
            const email = document.getElementById('popupLoginEmail').value;
            const senha = document.getElementById('popupLoginSenha').value;

            if (!email || !senha) {
                showPopupMessage('loginMessagePopup', 'Preencha email e senha.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('senha', senha);

            // Mostrar loading
            const button = this;
            button.disabled = true;
            button.textContent = 'Entrando...';

            fetch('controllers/auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('📊 Resposta do login popup:', data);
                if (data.success) {
                    // Fechar popup e recarregar a página para atualizar o header
                    document.getElementById('popup').style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
                    document.body.style.overflow = 'auto';
                    window.location.reload();
                } else {
                    showPopupMessage('loginMessagePopup', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('❌ Erro no login popup:', error);
                showPopupMessage('loginMessagePopup', 'Erro ao processar login.', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = 'Entrar na Minha Conta';
            });
        });

        // Cadastro no Popup
        document.getElementById('signupButtonPopup').addEventListener('click', function() {
            console.log('🎯 Processando cadastro do popup...');
            
            const nome = document.getElementById('popupSignupNome').value;
            const email = document.getElementById('popupSignupEmail').value;
            const senha = document.getElementById('popupSignupSenha').value;
            const telefone = document.getElementById('popupSignupTelefone').value;

            if (!nome || !email || !senha) {
                showPopupMessage('signupMessagePopup', 'Preencha todos os campos obrigatórios.', 'error');
                return;
            }

            if (senha.length < 6) {
                showPopupMessage('signupMessagePopup', 'A senha deve ter pelo menos 6 caracteres.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('nome', nome);
            formData.append('email', email);
            formData.append('senha', senha);
            formData.append('telefone', telefone);

            // Mostrar loading
            const button = this;
            button.disabled = true;
            button.textContent = 'Cadastrando...';

            fetch('controllers/auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('📊 Resposta do cadastro popup:', data);
                if (data.success) {
                    showPopupMessage('signupMessagePopup', data.message, 'success');
                    // Limpar formulário
                    document.getElementById('popupSignupNome').value = '';
                    document.getElementById('popupSignupEmail').value = '';
                    document.getElementById('popupSignupSenha').value = '';
                    document.getElementById('popupSignupTelefone').value = '';
                    
                    // Mostrar formulário de login após 2 segundos
                    setTimeout(() => {
                        showLoginPopup();
                        showPopupMessage('loginMessagePopup', 'Cadastro realizado! Agora faça login.', 'success');
                    }, 2000);
                } else {
                    showPopupMessage('signupMessagePopup', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('❌ Erro no cadastro popup:', error);
                showPopupMessage('signupMessagePopup', 'Erro ao processar cadastro.', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = 'Cadastrar e Criar Conta';
            });
        });

        // ========== CONFIGURAÇÃO GERAL ==========

        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== SISTEMA INICIADO ===');

            // Elementos
            const overlay = document.getElementById('overlay');
            const menuIcon = document.getElementById('menu-icon');
            const navbar = document.querySelector('.navbar');
            const openPopupBtn = document.getElementById('openPopupBtn');
            const popup = document.getElementById('popup');
            const closePopupBtn = document.getElementById('closePopupBtn');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');

            // Atualizar contador do carrinho quando a página carregar
            atualizarContadorCarrinho();
            
            // Adicionar event listeners para botões de compra
            document.querySelectorAll('.btn-comprar').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    adicionarAoCarrinho(productId, productName, 1);
                });
            });

            // Configurar alternância no popup
            document.getElementById('showSignupPopupLink').addEventListener('click', function(e) {
                e.preventDefault();
                showSignupPopup();
            });
            
            document.getElementById('showLoginPopupLink').addEventListener('click', function(e) {
                e.preventDefault();
                showLoginPopup();
            });
            
            // Mostrar login por padrão
            showLoginPopup();

            // Menu Mobile
            if (menuIcon && navbar) {
                menuIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    navbar.classList.toggle('open');
                    menuIcon.classList.toggle('bx-x');
                    if (overlay) overlay.style.display = navbar.classList.contains('open') ? 'block' : 'none';
                    document.body.style.overflow = navbar.classList.contains('open') ? 'hidden' : 'auto';
                    
                    // Fechar outros menus
                    if (userMenu) userMenu.style.display = 'none';
                });

                // Fechar menu ao clicar em links
                document.querySelectorAll('.navbar a').forEach(link => {
                    link.addEventListener('click', function() {
                        navbar.classList.remove('open');
                        menuIcon.classList.remove('bx-x');
                        if (overlay) overlay.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    });
                });
            }

            // Popup Login/Cadastro
            if (openPopupBtn && popup) {
                openPopupBtn.addEventListener('click', function() {
                    popup.style.display = "flex";
                    if (overlay) overlay.style.display = "block";
                    document.body.style.overflow = 'hidden';
                    // Resetar para o formulário de login
                    showLoginPopup();
                });
            }

            if (closePopupBtn && popup) {
                closePopupBtn.addEventListener('click', function() {
                    popup.style.display = "none";
                    if (overlay) overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }

            // Menu do Usuário
            if (userMenuBtn && userMenu) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
                    
                    // Fechar outros menus
                    if (navbar) navbar.classList.remove('open');
                    if (menuIcon) menuIcon.classList.remove('bx-x');
                });

                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (userMenu && !userMenu.contains(e.target) && e.target !== userMenuBtn) {
                        userMenu.style.display = 'none';
                    }
                });
            }

            // Overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    // Fechar todos os menus
                    if (navbar) {
                        navbar.classList.remove('open');
                        if (menuIcon) menuIcon.classList.remove('bx-x');
                    }
                    if (popup) popup.style.display = 'none';
                    if (userMenu) userMenu.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }

            // Tecla ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (popup && popup.style.display === 'flex') {
                        popup.style.display = 'none';
                        if (overlay) overlay.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }

                    if (navbar && navbar.classList.contains('open')) {
                        navbar.classList.remove('open');
                        if (menuIcon) menuIcon.classList.remove('bx-x');
                        if (overlay) overlay.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }

                    if (userMenu) {
                        userMenu.style.display = 'none';
                    }
                }
            });

            // Redimensionamento
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1170) {
                    if (navbar) navbar.classList.remove('open');
                    if (menuIcon) menuIcon.classList.remove('bx-x');
                    if (overlay) overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });

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

            // Scroll suave para produtos
            const ctaButton = document.querySelector('.cta-button');
            if (ctaButton) {
                ctaButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const produtosSection = document.querySelector('#produtos');
                    if (produtosSection) {
                        produtosSection.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            }

            console.log('=== SISTEMA CONFIGURADO ===');
        });
    </script>
</body>
</html>