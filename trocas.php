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
    <title>Trocas e Devoluções - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        main {
            margin-top: 80px;
            padding: 40px 5%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .page-header h1 {
            color: #101821;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .page-content {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #FB8929;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .section p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .section ul {
            color: #666;
            line-height: 1.6;
            margin-left: 20px;
        }
        
        .section li {
            margin-bottom: 8px;
        }
        
        .contact-info {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
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

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h1>Trocas e Devoluções</h1>
            <p>Entenda nossa política de trocas e devoluções</p>
        </div>
        
        <div class="page-content">
            <div class="section">
                <h2>Prazo para Trocas e Devoluções</h2>
                <p>Você tem até 30 dias corridos, a partir da data de recebimento do produto, para solicitar trocas ou devoluções.</p>
            </div>
            
            <div class="section">
                <h2>Condições para Trocas</h2>
                <p>Para realizar a troca, o produto deve estar em perfeitas condições:</p>
                <ul>
                    <li>Produto sem uso e na embalagem original</li>
                    <li>Etiquetas e tags intactas</li>
                    <li>Manual e acessórios incluídos</li>
                    <li>Nota fiscal do pedido</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>Como Solicitar Troca ou Devolução</h2>
                <p>Entre em contato com nosso atendimento através dos canais:</p>
                <div class="contact-info">
                    <p><strong>WhatsApp:</strong> (11) 99999-9999</p>
                    <p><strong>E-mail:</strong> trocas@offerbuy.com.br</p>
                    <p><strong>Telefone:</strong> (11) 4004-4004</p>
                </div>
            </div>
            
            <div class="section">
                <h2>Reembolsos</h2>
                <p>O reembolso será processado em até 10 dias úteis após o recebimento e análise do produto em nosso centro de distribuição.</p>
            </div>
        </div>
    </main>

    <footer>
        <!-- SEU FOOTER AQUI (o mesmo do index.php) -->
    </footer>

    <script src="js/script.js"></script>
</body>
</html>