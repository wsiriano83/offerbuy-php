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
    <title>Central de Ajuda - OfferBuy</title>
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
        
        .faq-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .faq-question {
            color: #101821;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .faq-answer {
            color: #666;
            line-height: 1.6;
            display: none;
        }
        
        .faq-answer.show {
            display: block;
        }
        
        .contact-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .contact-option {
            background: #f8f8f8;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        
        .contact-option:hover {
            transform: translateY(-5px);
        }
        
        .contact-option i {
            font-size: 2.5rem;
            color: #FB8929;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <!-- SEU HEADER AQUI (o mesmo do index.php) -->
    </header>

    <main>
        <div class="page-header">
            <h1>Central de Ajuda</h1>
            <p>Encontre respostas para suas dúvidas mais frequentes</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question" onclick="toggleAnswer(this)">
                    Como faço meu pedido?
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="faq-answer">
                    <p>Para fazer um pedido, basta navegar pelos produtos, adicionar os itens desejados ao carrinho e finalizar a compra. Você precisará criar uma conta ou fazer login.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleAnswer(this)">
                    Quais são as formas de pagamento?
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="faq-answer">
                    <p>Aceitamos cartão de crédito (em até 12x), débito, PIX e boleto bancário.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleAnswer(this)">
                    Qual o prazo de entrega?
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="faq-answer">
                    <p>O prazo de entrega varia de 3 a 15 dias úteis, dependendo da sua localização e do produto escolhido.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleAnswer(this)">
                    Como rastrear meu pedido?
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="faq-answer">
                    <p>Após a postagem, você receberá um código de rastreamento por e-mail. Acesse "Meus Pedidos" em sua conta para acompanhar.</p>
                </div>
            </div>
            
            <div class="contact-options">
                <div class="contact-option">
                    <i class='bx bx-phone'></i>
                    <h3>Telefone</h3>
                    <p>(11) 4004-4004</p>
                    <small>Segunda a Sexta, 8h às 18h</small>
                </div>
                
                <div class="contact-option">
                    <i class='bx bx-envelope'></i>
                    <h3>E-mail</h3>
                    <p>ajuda@offerbuy.com.br</p>
                    <small>Respondemos em até 24h</small>
                </div>
                
                <div class="contact-option">
                    <i class='bx bxl-whatsapp'></i>
                    <h3>WhatsApp</h3>
                    <p>(11) 99999-9999</p>
                    <small>Atendimento 24/7</small>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <!-- SEU FOOTER AQUI (o mesmo do index.php) -->
    </footer>

    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('i');
            
            answer.classList.toggle('show');
            icon.classList.toggle('bx-chevron-down');
            icon.classList.toggle('bx-chevron-up');
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>