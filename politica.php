<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política OfferBuy - Termos e Condições</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .policy-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .policy-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .policy-header h1 {
            font-size: 2.5rem;
            color: #101821;
            margin-bottom: 20px;
        }
        
        .last-updated {
            color: #666;
            font-style: italic;
            margin-bottom: 30px;
        }
        
        .policy-section {
            margin-bottom: 40px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .policy-section h2 {
            color: #FB8929;
            margin-bottom: 20px;
            font-size: 1.8rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .policy-section h3 {
            color: #101821;
            margin: 25px 0 15px 0;
            font-size: 1.3rem;
        }
        
        .policy-section p {
            line-height: 1.8;
            margin-bottom: 15px;
            color: #555;
        }
        
        .policy-section ul {
            margin: 15px 0;
            padding-left: 20px;
        }
        
        .policy-section li {
            margin-bottom: 10px;
            line-height: 1.6;
            color: #555;
        }
        
        .highlight {
            background: #fff9e6;
            padding: 20px;
            border-left: 4px solid #FB8929;
            margin: 20px 0;
            border-radius: 5px;
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
                    <a href="perfil.php">Meu Perfil</a>
                    <a href="pedidos.php">Meus Pedidos</a>
                    <a href="logout.php">Sair</a>
                </div>
            <?php else: ?>
                <button id="openPopupBtn">Login / Cadastro</button>
            <?php endif; ?>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <div id="overlay"></div>

    <!-- Popup de Login/Cadastro -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="closePopupBtn" class="close">&times;</span>
            <div class="form-toggle">
                <button id="loginToggle" class="toggle active">Login</button>
                <button id="signupToggle" class="toggle">Cadastro</button>
            </div>
            <form id="loginForm" class="form" method="POST" action="controllers/auth.php">
                <input type="hidden" name="action" value="login">
                <h2>Login</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
            <form id="signupForm" class="form" method="POST" action="controllers/auth.php">
                <input type="hidden" name="action" value="register">
                <h2>Cadastro</h2>
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required minlength="6">
                <input type="tel" name="telefone" placeholder="Telefone">
                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </div>

    <main style="margin-top: 120px;">
        <div class="policy-container">
            <div class="policy-header">
                <h1>Política OfferBuy</h1>
                <p class="last-updated">Última atualização: <?php echo date('d/m/Y'); ?></p>
            </div>
            
            <div class="policy-section">
                <h2>1. Termos de Uso</h2>
                <p>Ao acessar e usar o site OfferBuy, você concorda em cumprir e estar vinculado aos seguintes termos e condições.</p>
                
                <h3>1.1 Elegibilidade</h3>
                <p>Para usar nossos serviços, você deve:</p>
                <ul>
                    <li>Ter pelo menos 18 anos de idade</li>
                    <li>Possuir capacidade legal para celebrar contratos</li>
                    <li>Fornecer informações verdadeiras e precisas</li>
                </ul>
                
                <h3>1.2 Conta do Usuário</h3>
                <p>Você é responsável por:</p>
                <ul>
                    <li>Manter a confidencialidade de sua conta e senha</li>
                    <li>Todas as atividades que ocorram em sua conta</li>
                    <li>Notificar-nos imediatamente sobre qualquer uso não autorizado</li>
                </ul>
            </div>
            
            <div class="policy-section">
                <h2>2. Compras e Pagamentos</h2>
                
                <h3>2.1 Processamento de Pedidos</h3>
                <p>Os pedidos estão sujeitos à disponibilidade de estoque e confirmação do preço. Reservamo-nos o direito de recusar ou cancelar pedidos a qualquer momento.</p>
                
                <h3>2.2 Preços e Taxas</h3>
                <p>Todos os preços estão em Reais (R$) e incluem impostos aplicáveis. Taxas de frete são calculadas durante o checkout.</p>
                
                <h3>2.3 Métodos de Pagamento</h3>
                <p>Aceitamos os seguintes métodos de pagamento:</p>
                <ul>
                    <li>Cartão de crédito</li>
                    <li>Cartão de débito</li>
                    <li>PIX</li>
                    <li>Boleto bancário</li>
                </ul>
            </div>
            
            <div class="policy-section">
                <h2>3. Entregas e Prazos</h2>
                
                <h3>3.1 Prazos de Entrega</h3>
                <p>Os prazos de entrega variam conforme a localidade e modalidade de frete escolhida. Estimativas são fornecidas durante o checkout.</p>
                
                <h3>3.2 Acompanhamento</h3>
                <p>Fornecemos código de rastreamento para todas as encomendas. O acompanhamento pode ser feito em nossa central de rastreamento.</p>
            </div>
            
            <div class="policy-section">
                <h2>4. Trocas e Devoluções</h2>
                
                <h3>4.1 Direito de Arrependimento</h3>
                <p>Conforme o Código de Defesa do Consumidor, você tem 7 dias corridos para desistir da compra.</p>
                
                <h3>4.2 Produtos com Defeito</h3>
                <p>Para produtos com defeito de fabricação, o prazo para reclamação é de 90 dias.</p>
                
                <div class="highlight">
                    <p><strong>Importante:</strong> Produtos personalizados ou sob encomenda não podem ser trocados ou devolvidos, exceto em caso de defeito.</p>
                </div>
            </div>
            
            <div class="policy-section">
                <h2>5. Garantia</h2>
                <p>Todos os produtos vendidos pela OfferBuy possuem garantia contra defeitos de fabricação. O prazo de garantia varia conforme o produto e é especificado em cada anúncio.</p>
            </div>
        </div>
    </main>

    <footer>
        <div class="contact">
            <div class="contact-content">
                <img src="img/Allin onde sem fondo.jpg" alt="Logo OfferBuy">
                <div class="icons">
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-instagram-alt'></i></a>
                    <a href="#"><i class='bx bxl-twitter'></i></a>
                    <a href="#"><i class='bx bxl-youtube'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
            </div>
            <div class="contact-content">
                <h4>Sobre OfferBuy</h4>
                <li><a href="sobre.php">Sobre nós</a></li>
                <li><a href="politica.php">Política OfferBuy</a></li>
                <li><a href="privacidade.php">Política de Privacidade</a></li>
            </div>
            <div class="contact-content">
                <h4>Métodos de Pagamento</h4>
                <li><a href="#">Pix</a></li>
                <li><a href="#">Cartão</a></li>
                <li><a href="#">Boleto</a></li>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>