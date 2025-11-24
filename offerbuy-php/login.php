<?php
session_start();

// Se já estiver logado, redireciona para a página inicial
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Incluições básicas
include_once 'config/database.php';
include_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OfferBuy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .auth-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .auth-btn {
            width: 100%;
            padding: 12px;
            background: #FB8929;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .auth-btn:hover {
            background: #e57a24;
        }
        .auth-switch {
            text-align: center;
            margin-top: 20px;
        }
        .auth-switch a {
            color: #FB8929;
            text-decoration: none;
        }
        .auth-switch a:hover {
            text-decoration: underline;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        small {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Formulário de Login -->
        <div class="auth-form" id="loginForm">
            <h2>Login</h2>
            <div id="loginMessage"></div>

            <div class="form-group">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" placeholder="Seu email" required>
            </div>

            <div class="form-group">
                <label for="login-password">Senha:</label>
                <input type="password" id="login-password" placeholder="Sua senha" required>
            </div>

            <button type="button" class="auth-btn" id="loginButton">Entrar</button>

            <p class="auth-switch">
                Não tem conta? <a href="#" id="showRegisterLink">Cadastre-se</a>
            </p>
        </div>

        <!-- Formulário de Cadastro -->
        <div class="auth-form" id="registerForm" style="display: none;">
            <h2>Cadastro</h2>
            <div id="registerMessage"></div>

            <div class="form-group">
                <label for="register-name">Nome completo:</label>
                <input type="text" id="register-name" placeholder="Seu nome completo" required>
            </div>

            <div class="form-group">
                <label for="register-email">Email:</label>
                <input type="email" id="register-email" placeholder="Seu melhor email" required>
            </div>

            <div class="form-group">
                <label for="register-password">Senha:</label>
                <input type="password" id="register-password" placeholder="Crie uma senha" required minlength="6">
                <small>Mínimo seis caracteres</small>
            </div>

            <div class="form-group">
                <label for="register-phone">Telefone (opcional):</label>
                <input type="tel" id="register-phone" placeholder="Seu telefone">
            </div>

            <button type="button" class="auth-btn" id="registerButton">Cadastrar</button>

            <p class="auth-switch">
                Já tem conta? <a href="#" id="showLoginLink">Fazer login</a>
            </p>
        </div>
    </div>

    <script>
        // SOLUÇÃO SIMPLES E DIRETA - Sem formulários, apenas inputs e botões
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== SISTEMA DE LOGIN INICIADO ===');

            // Elementos
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const showRegisterLink = document.getElementById('showRegisterLink');
            const showLoginLink = document.getElementById('showLoginLink');
            const loginButton = document.getElementById('loginButton');
            const registerButton = document.getElementById('registerButton');

            // Funções para mostrar formulários
            function showLogin() {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                clearMessages();
            }

            function showRegister() {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                clearMessages();
            }

            function clearMessages() {
                document.getElementById('loginMessage').innerHTML = '';
                document.getElementById('registerMessage').innerHTML = '';
            }

            function showMessage(elementId, message, type) {
                const element = document.getElementById(elementId);
                element.innerHTML = `<div class="${type}-message">${message}</div>`;
            }

            // Event Listeners para alternar entre formulários
            showRegisterLink.addEventListener('click', function(e) {
                e.preventDefault();
                showRegister();
            });

            showLoginLink.addEventListener('click', function(e) {
                e.preventDefault();
                showLogin();
            });

            // LOGIN - Processamento direto
            loginButton.addEventListener('click', function() {
                console.log('🎯 Processando login...');
                
                const email = document.getElementById('login-email').value;
                const senha = document.getElementById('login-password').value;

                if (!email || !senha) {
                    showMessage('loginMessage', 'Preencha email e senha.', 'error');
                    return;
                }

                // Criar FormData manualmente
                const formData = new FormData();
                formData.append('action', 'login');
                formData.append('email', email);
                formData.append('senha', senha);

                // Mostrar loading
                loginButton.disabled = true;
                loginButton.textContent = 'Entrando...';

                fetch('controllers/auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('📨 Resposta recebida');
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Dados:', data);
                    if (data.success) {
                        console.log('✅ Login bem-sucedido - redirecionando');
                        window.location.href = 'index.php';
                    } else {
                        showMessage('loginMessage', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('❌ Erro:', error);
                    showMessage('loginMessage', 'Erro ao processar login.', 'error');
                })
                .finally(() => {
                    // Restaurar botão
                    loginButton.disabled = false;
                    loginButton.textContent = 'Entrar';
                });
            });

            // CADASTRO - Processamento direto
            registerButton.addEventListener('click', function() {
                console.log('🎯 Processando cadastro...');
                
                const nome = document.getElementById('register-name').value;
                const email = document.getElementById('register-email').value;
                const senha = document.getElementById('register-password').value;
                const telefone = document.getElementById('register-phone').value;

                if (!nome || !email || !senha) {
                    showMessage('registerMessage', 'Preencha todos os campos obrigatórios.', 'error');
                    return;
                }

                if (senha.length < 6) {
                    showMessage('registerMessage', 'A senha deve ter pelo menos 6 caracteres.', 'error');
                    return;
                }

                // Criar FormData manualmente
                const formData = new FormData();
                formData.append('action', 'register');
                formData.append('nome', nome);
                formData.append('email', email);
                formData.append('senha', senha);
                formData.append('telefone', telefone);

                // Mostrar loading
                registerButton.disabled = true;
                registerButton.textContent = 'Cadastrando...';

                fetch('controllers/auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Dados:', data);
                    if (data.success) {
                        showMessage('registerMessage', data.message, 'success');
                        // Limpar formulário
                        document.getElementById('register-name').value = '';
                        document.getElementById('register-email').value = '';
                        document.getElementById('register-password').value = '';
                        document.getElementById('register-phone').value = '';
                        
                        // Mostrar formulário de login após 2 segundos
                        setTimeout(() => {
                            showLogin();
                            showMessage('loginMessage', 'Cadastro realizado! Agora faça login.', 'success');
                        }, 2000);
                    } else {
                        showMessage('registerMessage', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('❌ Erro:', error);
                    showMessage('registerMessage', 'Erro ao processar cadastro.', 'error');
                })
                .finally(() => {
                    // Restaurar botão
                    registerButton.disabled = false;
                    registerButton.textContent = 'Cadastrar';
                });
            });

            console.log('=== SISTEMA DE LOGIN CONFIGURADO ===');
        });
    </script>
</body>
</html>