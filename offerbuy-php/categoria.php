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
$categoria_id = $_GET['id'] ?? 0;

// Mapear IDs para nomes amigáveis
$nomes_categorias = [
    1 => 'Roupas',
    2 => 'Eletrônicos', 
    3 => 'Acessórios',
    4 => 'Beleza & Moda',
    5 => 'Eletrodomésticos',
    6 => 'Ferramentas'
];

$categoria_nome = $_GET['nome'] ?? 'Categoria';

// Buscar produtos da categoria
if ($categoria_id > 0 && isset($nomes_categorias[$categoria_id])) {
    try {
        $stmt = $product->readByCategoria($categoria_id);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categoria_nome = $nomes_categorias[$categoria_id];
    } catch (Exception $e) {
        $produtos = [];
        error_log("Erro ao buscar categoria: " . $e->getMessage());
    }
} else {
    $produtos = [];
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoria_nome); ?> - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
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

    <main style="margin-top: 120px; padding: 20px 5%;">
        <div class="category-results">
            <h2 style="margin-bottom: 30px; text-align: center;">
                <?php echo htmlspecialchars($categoria_nome); ?>
            </h2>

            <div class="property-content">
                <?php
                if (!empty($produtos)) {
                    foreach ($produtos as $produto) {
                        $desconto = '';
                        if (isset($produto['preco_original']) && $produto['preco_original'] > $produto['preco']) {
                            $percentual = (($produto['preco_original'] - $produto['preco']) / $produto['preco_original']) * 100;
                            $desconto = '<span class="desconto">-' . number_format($percentual, 0) . '%</span>';
                        }
                        
                        $frete_gratis = (isset($produto['frete_gratis']) && $produto['frete_gratis']) ? '<span class="frete-gratis">Frete Grátis</span>' : '';
                        
                        // --- BLOCO DE EXIBIÇÃO DO PRODUTO (Corrigido e robusto) ---
                        echo '
                        <div class="row">
                            
                            <a href="produto.php?id=' . $produto['id'] . '">
                                <div class="product-image">
                                    <img src="' . ($produto['imagem_url'] ?: 'img/placeholder.jpg') . '" alt="' . htmlspecialchars($produto['nome']) . '">
                                    ' . $desconto . $frete_gratis . '
                                </div>
                                <p class="product-name">' . htmlspecialchars($produto['nome']) . '</p>
                            </a> 
                            
                            <h5>R$ '. number_format($produto['preco'], 2, ',', '.') . '</h5>
                            ' . (isset($produto['preco_original']) && $produto['preco_original'] > $produto['preco'] ? 
                                '<h6 class="preco-original">De: R$ '. number_format($produto['preco_original'], 2, ',', '.') . '</h6>' : '') . '
                            
                            <form action="adicionar_carrinho.php" method="post">
                                <input type="hidden" name="product_id" value="' . $produto['id'] . '">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-adicionar-carrinho">ADICIONAR AO CARRINHO</button>
                            </form>
                        </div>';
                        // --- FIM DO BLOCO ---
                    }
                } else {
                    echo '
                    <div style="text-align: center; padding: 40px; width: 100%;">
                        <p style="font-size: 18px; margin-bottom: 20px;">
                            Nenhum produto encontrado na categoria ' . htmlspecialchars($categoria_nome) . '.
                        </p>
                        <a href="index.php" style="background: #FB8929; color: white; padding: 10px 20px;
                            border-radius: 8px; text-decoration: none;">Voltar à Loja</a>
                    </div>';
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="contact">
            <div class="contact-content">
                <img src="img/Allin onde sem fondo.jpg" alt="Logo OfferBuy">
            </div>
            <div class="contact-content">
                <h4>Sobre OfferBuy</h4>
                <li><a href="sobre.php">Sobre nós</a></li>
                <li><a href="politica.php">Política OfferBuy</a></li>
                <li><a href="privacidade.php">Política de Privacidade</a></li>
            </div>
            <div class="contact-content">
                <h4>Minha Conta</h4>
                <li><a href="sobre.php">Meu Perfil</a></li>
                <li><a href="politica.php">Meus Pedidos</a></li>
                <li><a href="privacidade.php">Meu Carrinho</a></li>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>