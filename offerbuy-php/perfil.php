<?php
session_start();
require_once 'config/database.php';

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Conectar ao banco
$database = new Database();
$db = $database->getConnection();

// Buscar dados do usuário
$user = null;
try {
    $query = "SELECT nome, email, telefone, data_cadastro FROM usuarios WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("Usuário não encontrado");
    }
} catch (Exception $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// Processar atualização do perfil
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    try {
        $query = "UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$nome, $telefone, $user_id])) {
            $success = "Perfil atualizado com sucesso!";
            $_SESSION['user_name'] = $nome;
            
            // Recarregar dados atualizados
            $query = "SELECT nome, email, telefone, data_cadastro FROM usuarios WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Erro ao atualizar perfil.";
        }
    } catch (Exception $e) {
        $error = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Original do Seu Site -->
    <?php
    // Incluir o header da mesma forma que no index.php
    $isLoggedIn = isset($_SESSION['user_id']);
    $userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'Usuário') : '';
    ?>
    
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

            <!-- Ícone do carrinho -->
            <a href="carrinho.php" class="cart-icon">
                <i class='bx bx-cart' style="font-size: 24px;"></i>
                <span id="cart-count" class="cart-count">0</span>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>

    <!-- Conteúdo Principal do Perfil -->
    <main>
        <div class="container" style="max-width: 800px; margin: 100px auto; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>Meu Perfil</h1>
                <a href="index.php" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    ← Voltar para a Loja
                </a>
            </div>
            
            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    ✅ <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="profile-info" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <form method="POST">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="nome" style="display: block; margin-bottom: 5px; font-weight: bold;">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f5f5f5; font-size: 16px;" readonly>
                        <small style="color: #666;">O email não pode ser alterado</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="telefone" style="display: block; margin-bottom: 5px; font-weight: bold;">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;"
                               placeholder="Digite seu telefone">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Data de Cadastro</label>
                        <p style="color: #666; padding: 12px; background: #f9f9f9; border-radius: 5px;">
                            <?php 
                            // Formatar a data para exibição
                            $data = new DateTime($user['data_cadastro']);
                            echo $data->format('d/m/Y');
                            ?>
                        </p>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" name="atualizar" style="background: #FB8929; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold;">
                            Atualizar Perfil
                        </button>
                        <a href="index.php" style="background: #6c757d; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; text-align: center;">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer do Seu Site -->
    <section class="contact">
        <div class="contact-content">
            <img src="img/Allin onde sem fondo.jpg" alt="OfferBuy Logo">
            <p>Seu e-commerce de confiança com os melhores produtos e preços imbatíveis.</p>
            <div class="icons">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-whatsapp'></i></a>
            </div>
        </div>

        <div class="contact-content">
            <h4>Links Rápidos</h4>
            <li><a href="index.php">Home</a></li>
            <li><a href="produtos.php">Produtos</a></li>
            <li><a href="sobre.php">Sobre Nós</a></li>
            <li><a href="contato.php">Contato</a></li>
        </div>

        <div class="contact-content">
            <h4>Minha Conta</h4>
            <li><a href="perfil.php">Meu Perfil</a></li>
            <li><a href="pedidos.php">Meus Pedidos</a></li>
            <li><a href="carrinho.php">Carrinho</a></li>
            <li><a href="favoritos.php">Favoritos</a></li>
        </div>

        <div class="contact-content">
            <h4>Atendimento</h4>
            <li><a href="#">Central de Ajuda</a></li>
            <li><a href="#">Política de Entregas</a></li>
            <li><a href="#">Trocas e Devoluções</a></li>
            <li><a href="#">Política de Privacidade</a></li>
        </div>
    </section>

    <div class="footer-bottom">
        <p>&copy; 2024 OfferBuy. Todos os direitos reservados.</p>
    </div>

    <!-- Scripts do Seu Site -->
    <script>
        // Menu mobile
        document.addEventListener('DOMContentLoaded', function() {
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
                userMenuBtn.addEventListener('click', function() {
                    userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
                });

                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.style.display = 'none';
                    }
                });
            }

            // Header scroll behavior
            let lastScrollTop = 0;
            const header = document.querySelector('header');

            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    header.classList.add('hidden');
                } else {
                    header.classList.remove('hidden');
                }
                lastScrollTop = scrollTop;
            });
        });
    </script>
</body>
</html>