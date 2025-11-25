<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$product = new Product($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

$product_id = $_GET['id'] ?? 0;

if ($product_id > 0) {
    $product->id = $product_id;
    
    // Tenta carregar os dados do produto. Se falhar, redireciona.
    if (!$product->readOne()) { 
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product->nome); ?> - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /*
        * NOTA: Os estilos de .product-detail (antigo) foram substituídos pelo
        * .detalhes-produto-container no código do usuário.
        * O CSS de detalhe do produto foi incluído no corpo do arquivo fornecido anteriormente.
        */

        /* 1. Container principal: Usa Flexbox para criar colunas */
        .detalhes-produto-container {
            display: flex; 
            gap: 40px; 
            max-width: 1200px; 
            margin: 40px auto; 
            padding: 0 20px;
        }

        /* 2. Coluna da Imagem: Ocupa 40% do espaço */
        .detalhes-produto-imagem {
            flex: 0 0 40%; 
            max-width: 40%; 
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            text-align: center;
            /* Adicionado altura máxima para limitar o contêiner se necessário */
            /* Se for removido, o height: auto da img funcionará totalmente */
            max-height: 600px; 
        }

        /* 3. A Imagem em Si (CORREÇÃO AQUI) */
        .detalhes-produto-imagem img {
            max-width: 100%; 
            /* Define uma altura máxima razoável para imagens muito longas */
            max-height: 500px; 
            height: auto; /* Garante proporção */
            display: block;
            margin: 0 auto;
            /* PROPRIEDADE CHAVE: Garante que a imagem inteira apareça, sem cortar */
            object-fit: contain; 
        }

        /* 4. Coluna de Informações: Ocupa 60% do espaço */
        .detalhes-produto-info {
            flex: 1; 
        }
        
        /* 5. Estilos de texto */
        .detalhes-produto-info h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #101821;
        }

        .detalhes-produto-info h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #FB8929;
            margin-bottom: 5px;
        }

        .preco-antigo {
            color: #999;
            text-decoration: line-through;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .descricao h3 {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .descricao p {
            line-height: 1.6;
            color: #555;
        }

        /* 6. Responsividade para Celulares */
        @media (max-width: 768px) {
            .detalhes-produto-container {
                flex-direction: column; 
            }
            
            .detalhes-produto-imagem, 
            .detalhes-produto-info {
                flex-basis: 100%;
                max-width: 100%;
            }

            .btn-adicionar-carrinho.large {
                width: 100%; /* Ajuste o botão para a largura total em telas pequenas */
                max-width: none;
            }
        }
        /* Aplica o estilo laranja para todos os botões do carrinho */
        .btn-adicionar-carrinho {
            background-color: #FB8929; /* Usando var(--main-color) */
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-adicionar-carrinho:hover {
            background-color: #e57a24; /* Usando var(--secondary-color) */
        }


        /* Estilo para o botão grande na página de detalhes */
        .btn-adicionar-carrinho.large {
            font-size: 1.1em;
            padding: 15px 20px;
            margin-top: 20px;
            width: 80%; 
            max-width: 400px; 
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

    <main>
        <div class="detalhes-produto-container">
            
            <div class="detalhes-produto-imagem">
                <img src="<?php echo $product->imagem_url; ?>" alt="<?php echo $product->nome; ?>">
            </div>
            
            <div class="detalhes-produto-info">
                <h1><?php echo htmlspecialchars($product->nome); ?></h1>
                
                <h2>R$ <?php echo number_format($product->preco, 2, ',', '.'); ?></h2>
                <p class="preco-antigo">De: R$ <?php echo number_format($product->preco_original, 2, ',', '.'); ?></p>
                
                <p>Frete Grátis</p>
                <p>Garantia de 12 meses</p>
                <p>Estoque: 100 unidades</p>
                
                <form action="adicionar_carrinho.php" method="post">
                    <input type="hidden" name="produto_id" value="<?php echo $product->id; ?>">
                    <input type="hidden" name="quantidade" value="1">
                    <button type="submit" class="btn-adicionar-carrinho large">ADICIONAR ao Carrinho</button>
                </form>
                
                <div class="descricao">
                    <h3>Descrição</h3>
                    <p><?php echo htmlspecialchars($product->descricao); ?></p>
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
    <script>
    function adicionarAoCarrinho(productId) {
        alert('Produto adicionado ao carrinho! ID: ' + productId);
        // Aqui você pode implementar a lógica para adicionar ao carrinho
    }
    </script>
</body>
</html>