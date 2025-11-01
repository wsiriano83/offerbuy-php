<?php 
session_start();
include_once '../config/database.php';
include_once '../models/User.php';

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
    <title>OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="img/Allin onde sem fondo.jpg">
        </a>

        <ul class="navbar">
            <li><a href="index.php?categorias=1">Roupas</a></li>
            <li><a href="index.php?categorias=2">Eletrônicos</a></li>
            <li><a href="index.php?categorias=3">Acessórios</a></li>
            <li><a href="index.php?categorias=4">Beleza & Moda</a></li>
            <li><a href="index.php?categorias=5">Eletrodomésticos</a></li>
            <li><a href="index.php?categorias=6">Ferramentas</a></li>
        </ul>

        <div class="h-btn">
            <?php if($isLoggedIn): ?>
                <button id="userMenuBtn">Olá, <?php  echo htmlspecialchars($userName); ?></button>
                <div class="user-menu">
                    <a href="perfil.php">Meu Perfil</a>
                    <a href="pedidos.php">Meus Pedidps</a>
                    <a href="logout.php">Sair</a>
                </div>

                <?php  else: ?>
                    <button id="openPopupBtn">Login / Cadastro</button>
             
                <!-- Popup de login -->                    
                <div id="pop-up" class="popup" style="display: none;">
                    <div class="popup-content">
                        <span id="closePopupBtn" class="close">&times;</span>
                        <div class="form-toogle">
                            <button id="loginToogle" class="toogle active">Login</button>
                            <button id="signupToggle" class="toogle">Cadastro</button>
                        </div>
                        <form id="loginForm" class="form">
                            <h2>Login</h2>
                            <input type="email" placeholder="Email" required>
                            <input type="password" placeholder="Senha" required>
                            <button type="submit">Entrar</button>
                        </form>
                        <form id="signupForm" class="form" style="display: nome;">
                            <h2>Cadastro</h2>
                            <input type="text" placeholder="Nome" required>
                            <input type="email" placeholder="Email" required>
                            <input type="password" placeholder="Senha" required>
                            <input type="tel" placeholder="Telefone" required>
                            <button type="submit">Cadastrar</button>
                        </form>
                    </div>
                </div>
        <?php endif; ?>

        <div class="bx bx-menu" id="menu-icon"></div>
        </div>
    </header>
