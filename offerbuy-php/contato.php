<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Processar formulário de contato
$mensagemEnviada = false;
if ($_POST) {
    // Aqui você pode adicionar o código para processar o formulário
    // Enviar e-mail, salvar no banco de dados, etc.
    $mensagemEnviada = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fale Conosco - OfferBuy</title>
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
        
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .contact-form input,
        .contact-form textarea,
        .contact-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: "Work Sans", sans-serif;
        }
        
        .contact-form textarea {
            height: 120px;
            resize: vertical;
        }
        
        .contact-form button {
            background: #FB8929;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .contact-form button:hover {
            background: #e57a24;
        }
        
        .contact-info {
            padding: 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .contact-item i {
            font-size: 1.5rem;
            color: #FB8929;
            margin-right: 15px;
            width: 30px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <!-- SEU HEADER AQUI (o mesmo do index.php) -->
    </header>

    <main>
        <div class="page-header">
            <h1>Fale Conosco</h1>
            <p>Estamos aqui para ajudar você</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-form">
                <?php if ($mensagemEnviada): ?>
                    <div class="success-message">
                        <p>Mensagem enviada com sucesso! Retornaremos em breve.</p>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="text" name="nome" placeholder="Seu nome completo" required>
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <input type="tel" name="telefone" placeholder="Seu telefone">
                    <select name="assunto" required>
                        <option value="">Selecione o assunto</option>
                        <option value="duvida">Dúvida sobre produto</option>
                        <option value="pedido">Status do pedido</option>
                        <option value="troca">Troca ou devolução</option>
                        <option value="sugestao">Sugestão</option>
                        <option value="outro">Outro</option>
                    </select>
                    <textarea name="mensagem" placeholder="Sua mensagem" required></textarea>
                    <button type="submit">Enviar Mensagem</button>
                </form>
            </div>
            
            <div class="contact-info">
                <div class="contact-item">
                    <i class='bx bx-phone'></i>
                    <div>
                        <h3>Telefone</h3>
                        <p>(11) 4004-4004</p>
                        <small>Segunda a Sexta, 8h às 18h</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class='bx bx-envelope'></i>
                    <div>
                        <h3>E-mail</h3>
                        <p>contato@offerbuy.com.br</p>
                        <small>Respondemos em até 24h</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class='bx bxl-whatsapp'></i>
                    <div>
                        <h3>WhatsApp</h3>
                        <p>(11) 99999-9999</p>
                        <small>Atendimento 24/7</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class='bx bx-map'></i>
                    <div>
                        <h3>Endereço</h3>
                        <p>Rua Exemplo, 123<br>São Paulo - SP<br>CEP: 01234-567</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <!-- SEU FOOTER AQUI (o mesmo do index.php) -->
    </footer>

    <script src="js/script.js"></script>
</body>
</html>