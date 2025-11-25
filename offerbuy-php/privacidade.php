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
        <title>Política de Privacidade - OfferBuy</title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
        <style>
            .privacy-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 40px 20px;
            }
            
            .privacy-header {
                text-align: center;
                margin-bottom: 50px;
            }
            
            .privacy-header h1 {
                font-size: 2.5rem;
                color: #101821;
                margin-bottom: 20px;
            }
            
            .last-updated {
                color: #666;
                font-style: italic;
                margin-bottom: 30px;
            }
            
            .privacy-section {
                margin-bottom: 40px;
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            }
            
            .privacy-section h2 {
                color: #FB8929;
                margin-bottom: 20px;
                font-size: 1.8rem;
                border-bottom: 2px solid #f0f0f0;
                padding-bottom: 10px;
            }
            
            .privacy-section h3 {
                color: #101821;
                margin: 25px 0 15px 0;
                font-size: 1.3rem;
            }
            
            .privacy-section p {
                line-height: 1.8;
                margin-bottom: 15px;
                color: #555;
            }
            
            .privacy-section ul {
                margin: 15px 0;
                padding-left: 20px;
            }
            
            .privacy-section li {
                margin-bottom: 10px;
                line-height: 1.6;
                color: #555;
            }
            
            .highlight-box {
                background: #e8f4ff;
                padding: 20px;
                border-left: 4px solid #2196F3;
                margin: 20px 0;
                border-radius: 5px;
            }
            
            .contact-info {
                background: #f0f8f0;
                padding: 25px;
                border-radius: 10px;
                margin-top: 30px;
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
            <div class="privacy-container">
                <div class="privacy-header">
                    <h1>Política de Privacidade</h1>
                    <p class="last-updated">Última atualização: <?php echo date('d/m/Y'); ?></p>
                </div>
                
                <div class="privacy-section">
                    <h2>1. Introdução</h2>
                    <p>A OfferBuy valoriza e respeita a privacidade de seus usuários. Esta Política de Privacidade explica como coletamos, usamos, divulgamos e protegemos suas informações quando você usa nosso site e serviços.</p>
                    
                    <div class="highlight-box">
                        <p><strong>LGPD:</strong> Esta política está em conformidade com a Lei Geral de Proteção de Dados (Lei 13.709/2018) e descreve seus direitos sobre seus dados pessoais.</p>
                    </div>
                </div>
                
                <div class="privacy-section">
                    <h2>2. Informações que Coletamos</h2>
                    
                    <h3>2.1 Informações Pessoais</h3>
                    <p>Coletamos as seguintes informações quando você se cadastra ou faz compras:</p>
                    <ul>
                        <li>Nome completo</li>
                        <li>Endereço de e-mail</li>
                        <li>Número de telefone</li>
                        <li>Endereço de entrega</li>
                        <li>CPF (para emissão de nota fiscal)</li>
                        <li>Dados de pagamento (processados de forma segura)</li>
                    </ul>
                    
                    <h3>2.2 Informações de Navegação</h3>
                    <p>Coletamos automaticamente:</p>
                    <ul>
                        <li>Endereço IP</li>
                        <li>Tipo de navegador</li>
                        <li>Páginas visitadas</li>
                        <li>Tempo de permanência no site</li>
                        <li>Dispositivo utilizado</li>
                    </ul>
                </div>
                
                <div class="privacy-section">
                    <h2>3. Como Usamos Suas Informações</h2>
                    
                    <p>Utilizamos suas informações para:</p>
                    <ul>
                        <li>Processar seus pedidos e pagamentos</li>
                        <li>Entregar produtos adquiridos</li>
                        <li>Enviar comunicações de serviço</li>
                        <li>Melhorar nossos produtos e serviços</li>
                        <li>Personalizar sua experiência de compra</li>
                        <li>Prevenir fraudes e garantir a segurança</li>
                        <li>Cumprir obrigações legais</li>
                    </ul>
                </div>
                
                <div class="privacy-section">
                    <h2>4. Compartilhamento de Informações</h2>
                    
                    <p>Seus dados pessoais podem ser compartilhados com:</p>
                    <ul>
                        <li><strong>Parceiros de entrega:</strong> Para entrega dos produtos</li>
                        <li><strong>Processadores de pagamento:</strong> Para processar transações</li>
                        <li><strong>Prestadores de serviço:</strong> Que nos auxiliam nas operações</li>
                        <li><strong>Autoridades legais:</strong> Quando exigido por lei</li>
                    </ul>
                    
                    <p><strong>Não vendemos</strong> suas informações pessoais para terceiros.</p>
                </div>
                
                <div class="privacy-section">
                    <h2>5. Seus Direitos</h2>
                    
                    <p>Conforme a LGPD, você tem direito a:</p>
                    <ul>
                        <li>Confirmar a existência de tratamento de dados</li>
                        <li>Acessar seus dados</li>
                        <li>Corrigir dados incompletos ou desatualizados</li>
                        <li>Solicitar a eliminação de dados</li>
                        <li>Revogar o consentimento</li>
                        <li>Solicitar a portabilidade dos dados</li>
                    </ul>
                </div>
                
                <div class="privacy-section">
                    <h2>6. Segurança dos Dados</h2>
                    
                    <p>Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações, incluindo:</p>
                    <ul>
                        <li>Criptografia de dados</li>
                        <li>Controles de acesso</li>
                        <li>Monitoramento de segurança</li>
                        <li>Backups regulares</li>
                    </ul>
                </div>
                
                <div class="privacy-section">
                    <h2>7. Cookies</h2>
                    
                    <p>Utilizamos cookies para:</p>
                    <ul>
                        <li>Lembrar suas preferências</li>
                        <li>Melhorar a performance do site</li>
                        <li>Personalizar conteúdo</li>
                        <li>Análise de uso do site</li>
                    </ul>
                    
                    <p>Você pode controlar o uso de cookies através das configurações do seu navegador.</p>
                </div>
                
                <div class="contact-info">
                    <h3>Dúvidas e Exercício de Direitos</h3>
                    <p>Para exercer seus direitos ou esclarecer dúvidas sobre esta Política de Privacidade, entre em contato conosco:</p>
                    <ul>
                        <li><strong>E-mail:</strong> privacidade@offerbuy.com</li>
                        <li><strong>Telefone:</strong> (11) 4004-4004</li>
                        <li><strong>Horário de atendimento:</strong> Segunda a sexta, 9h às 18h</li>
                    </ul>
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