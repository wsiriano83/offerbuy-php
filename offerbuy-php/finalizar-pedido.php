<?php
session_start();
require_once 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$user = new User($db);

$user_id = $_SESSION['user_id'];
$cart_items = $cart->getByUser($user_id);

if (empty($cart_items)) {
    header('Location: carrinho.php');
    exit;
}

// Calcular totais
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['quantidade'] * $item['preco'];
}
$frete = 0; // Você pode calcular frete aqui
$total = $subtotal + $frete;

// Incluir header
$header_path = __DIR__ . '/views/header.php';
if (file_exists($header_path)) {
    include $header_path;
} else {
    echo "Header não encontrado.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - OfferBuy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        .checkout-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 40px;
        }

        .checkout-header h1 {
            color: #101821;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .checkout-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            color: #101821;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .payment-option:hover {
            border-color: #FB8929;
        }

        .payment-option.selected {
            border-color: #28a745;
            background: #f8fff9;
        }

        .payment-option.selected::after {
            content: "✓";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .payment-option input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .payment-option input:checked {
            transform: scale(1.4);
        }

        .payment-icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .payment-info h4 {
            margin: 0 0 5px 0;
            color: #101821;
        }

        .payment-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .order-items {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-details h4 {
            margin: 0 0 5px 0;
            color: #101821;
            font-size: 1rem;
        }

        .order-item-price {
            color: #FB8929;
            font-weight: 600;
        }

        .order-item-quantity {
            color: #666;
            font-size: 0.9rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: 600;
            color: #101821;
            margin-top: 10px;
        }

        .summary-total span {
            color: #FB8929;
        }

        .confirm-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .confirm-btn:hover:not(:disabled) {
            background: #218838;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .confirm-btn:not(:disabled) {
            animation: pulse 2s infinite;
        }

        .confirm-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
                margin-top: 80px;
            }

            .checkout-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Finalizar Pedido</h1>
            <p>Escolha a forma de pagamento</p>
        </div>

        <div class="checkout-main">
            <!-- Seção de Método de Pagamento -->
            <div class="checkout-section">
                <h2 class="section-title"><i class='bx bx-credit-card'></i> Método de Pagamento</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <?php if ($_GET['error'] == 'metodo'): ?>
                        <div class="error-message">
                            <i class='bx bx-error-circle'></i>
                            Por favor, selecione um método de pagamento.
                        </div>
                    <?php elseif ($_GET['error'] == 'pedido'): ?>
                        <div class="error-message">
                            <i class='bx bx-error-circle'></i>
                            Erro ao processar o pedido. Tente novamente.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form id="payment-form" action="finalizar_compra.php" method="POST">
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="pix" required>
                            <div class="payment-icon">🧾</div>
                            <div class="payment-info">
                                <h4>PIX</h4>
                                <p>Pagamento instantâneo</p>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="cartao" required>
                            <div class="payment-icon">💳</div>
                            <div class="payment-info">
                                <h4>Cartão de Crédito</h4>
                                <p>Parcelamento em até 12x</p>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="boleto" required>
                            <div class="payment-icon">📄</div>
                            <div class="payment-info">
                                <h4>Boleto Bancário</h4>
                                <p>Pagamento em até 3 dias úteis</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="confirm-btn" id="confirm-btn" disabled>
                        <i class='bx bx-check'></i> Confirmar Pedido
                    </button>
                </form>
            </div>
        </div>

        <!-- Resumo do Pedido -->
        <div class="order-summary">
            <h2 class="section-title"><i class='bx bx-cart'></i> Resumo do Pedido</h2>
            
            <div class="order-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo $item['imagem_url'] ?: 'img/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($item['nome']); ?>" 
                             class="order-item-image"
                             onerror="this.src='img/placeholder.jpg'">
                        
                        <div class="order-item-details">
                            <h4><?php echo htmlspecialchars($item['nome']); ?></h4>
                            <div class="order-item-price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                            <div class="order-item-quantity">Qtd: <?php echo $item['quantidade']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-row">
                <span>Subtotal:</span>
                <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
                <span>Frete:</span>
                <span>R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
                <span>Desconto:</span>
                <span>R$ 0,00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <?php 
    $footer_path = __DIR__ . '/views/footer.php';
    if (file_exists($footer_path)) {
        include $footer_path;
    } else {
        include 'footer.php';
    }
    ?>

    <script>
        // Versão robusta e simplificada
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payment-form');
            const confirmBtn = document.getElementById('confirm-btn');
            const paymentOptions = document.querySelectorAll('.payment-option');
            const radioButtons = document.querySelectorAll('input[name="metodo_pagamento"]');
            
            console.log('=== FINALIZAR PEDIDO INICIADO ===');
            console.log('Opções de pagamento:', paymentOptions.length);
            console.log('Radio buttons:', radioButtons.length);
            
            // Clique em qualquer opção de pagamento
            paymentOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    console.log('Clicou na opção de pagamento');
                    
                    // Remover seleção anterior
                    paymentOptions.forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Adicionar seleção atual
                    this.classList.add('selected');
                    
                    // Marcar o radio button dentro deste label
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        confirmBtn.disabled = false;
                        console.log('✅ Método selecionado:', radio.value);
                        
                        // Disparar evento change para garantir
                        const changeEvent = new Event('change', { bubbles: true });
                        radio.dispatchEvent(changeEvent);
                    }
                });
            });
            
            // Change nos radio buttons (backup)
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    console.log('Radio change disparado:', this.value);
                    confirmBtn.disabled = false;
                    
                    // Atualizar visual da opção selecionada
                    paymentOptions.forEach(option => {
                        const optionRadio = option.querySelector('input[type="radio"]');
                        if (optionRadio && optionRadio.checked) {
                            option.classList.add('selected');
                        } else {
                            option.classList.remove('selected');
                        }
                    });
                });
            });
            
            // Validação do formulário
            form.addEventListener('submit', function(e) {
                const selected = document.querySelector('input[name="metodo_pagamento"]:checked');
                
                console.log('=== TENTATIVA DE ENVIO DO FORMULÁRIO ===');
                console.log('Método selecionado:', selected ? selected.value : 'NENHUM');
                
                if (!selected) {
                    e.preventDefault();
                    alert('❌ Por favor, selecione uma forma de pagamento antes de continuar.');
                    console.log('❌ Formulário bloqueado - método não selecionado');
                    return;
                }
                
                // Loading no botão
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processando Pedido...';
                
                console.log('✅ Formulário válido. Enviando para finalizar_compra.php...');
                console.log('Método:', selected.value);
            });
            
            // Debug: log inicial
            console.log('Formulário carregado. Aguardando seleção...');
        });
    </script>
</body>
</html>