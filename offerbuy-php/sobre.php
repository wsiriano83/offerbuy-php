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
    <title>Sobre Nós - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .about-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .about-header h1 {
            font-size: 3rem;
            color: #101821;
            margin-bottom: 20px;
        }
        
        .about-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .about-text h2 {
            color: #FB8929;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .about-text p {
            line-height: 1.8;
            margin-bottom: 20px;
            color: #555;
        }
        
        .about-image img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .values-section {
            background: #f8f8f8;
            padding: 50px;
            border-radius: 15px;
            margin-bottom: 50px;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .value-card {
            text-align: center;
            padding: 30px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .value-card i {
            font-size: 3rem;
            color: #FB8929;
            margin-bottom: 20px;
        }
        
        .value-card h3 {
            color: #101821;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .about-content {
                grid-template-columns: 1fr;
            }
            
            .about-header h1 {
                font-size: 2.5rem;
            }
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
        <div class="about-container">
            <div class="about-header">
                <h1>Sobre a OfferBuy</h1>
                <p>Conheça mais sobre nossa história, missão e valores</p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h2>Nossa História</h2>
                    <p>A OfferBuy nasceu em 2024 com o objetivo de revolucionar o comércio eletrônico no Brasil. Começamos como uma pequena startup e hoje somos uma das maiores plataformas de e-commerce do país.</p>
                    
                    <p>Nossa missão é oferecer aos clientes uma experiência de compra única, com produtos de qualidade, preços competitivos e um atendimento excepcional.</p>
                    
                    <h2>O Que Fazemos</h2>
                    <p>Conectamos milhões de compradores e vendedores em uma plataforma segura e confiável. Oferecemos desde eletrônicos e moda até produtos para casa e ferramentas, sempre com a garantia OfferBuy.</p>
                </div>
                
                <div class="about-image">
                    <img src="img/Allin onde sem fondo.jpg" alt="Nossa Equipe OfferBuy">
                </div>
            </div>
            
            <div class="values-section">
                <h2 style="text-align: center; color: #101821; margin-bottom: 30px;">Nossos Valores</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class='bx bx-shield-alt'></i>
                        <h3>Confiança</h3>
                        <p>Garantimos a segurança e privacidade dos dados dos nossos clientes em todas as transações.</p>
                    </div>
                    
                    <div class="value-card">
                        <i class='bx bx-trophy'></i>
                        <h3>Qualidade</h3>
                        <p>Selecionamos rigorosamente cada produto para oferecer o melhor aos nossos clientes.</p>
                    </div>
                    
                    <div class="value-card">
                        <i class='bx bx-heart'></i>
                        <h3>Paixão</h3>
                        <p>Amamos o que fazemos e nos dedicamos a proporcionar a melhor experiência de compra.</p>
                    </div>
                    
                    <div class="value-card">
                        <i class='bx bx-rocket'></i>
                        <h3>Inovação</h3>
                        <p>Estamos sempre evoluindo para oferecer tecnologias e serviços de ponta.</p>
                    </div>
                </div>
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
