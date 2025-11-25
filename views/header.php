<?php
// views/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

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
    <title>OfferBuy - Sua Loja Online</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a href="../index.php" class="logo">
            <img src="../img/Allin onde sem fondo.jpg" alt="OfferBuy Logo">
        </a>

        <ul class="navbar">
            <li><a href="../categoria.php?id=1&nome=roupas">Roupas</a></li>
            <li><a href="../categoria.php?id=2&nome=eletronicos">Eletrônicos</a></li>
            <li><a href="../categoria.php?id=3&nome=acessorios">Acessórios</a></li>
            <li><a href="../categoria.php?id=4&nome=beleza-moda">Beleza & Moda</a></li>
            <li><a href="../categoria.php?id=5&nome=eletrodomesticos">Eletrodomésticos</a></li>
            <li><a href="../categoria.php?id=6&nome=ferramentas">Ferramentas</a></li>
        </ul>

        <div class="h-btn">
            <?php if($isLoggedIn): ?>
                <button id="userMenuBtn">Olá, <?php echo htmlspecialchars($userName); ?></button>
                <div class="user-menu" id="userMenu">
                    <a href="../perfil.php"><i class='bx bx-user'></i> Meu Perfil</a>
                    <a href="../pedidos.php"><i class='bx bx-package'></i> Meus Pedidos</a>
                    <a href="../carrinho.php"><i class='bx bx-cart'></i> Meu Carrinho</a>
                    <a href="../logout.php"><i class='bx bx-log-out'></i> Sair</a>
                </div>
            <?php else: ?>
                <button id="openPopupBtn">Login / Cadastro</button>
            <?php endif; ?>

            <a href="../carrinho.php" class="cart-icon">
                <i class='bx bx-cart' style="font-size: 24px;"></i>
                <span id="cart-count" class="cart-count">0</span>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>