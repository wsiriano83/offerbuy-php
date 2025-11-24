<?php
session_start();
header('Content-Type: application/json');

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método não permitido';
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action == 'register') {
    $user->nome = $_POST['nome'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->senha = $_POST['senha'] ?? '';
    $user->telefone = $_POST['telefone'] ?? '';

    // Validações
    if (empty($user->nome) || empty($user->email) || empty($user->senha)) {
        $response['message'] = 'Preencha todos os campos obrigatórios.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email inválido.';
        echo json_encode($response);
        exit;
    }

    if ($user->emailExists()) {
        $response['message'] = 'Email já cadastrado.';
        echo json_encode($response);
        exit;
    }

    if (strlen($user->senha) < 6) {
        $response['message'] = 'A senha deve ter pelo menos seis caracteres.';
        echo json_encode($response);
        exit;
    }

    if ($user->register()) {
        $response['success'] = true;
        $response['message'] = 'Cadastro realizado com sucesso! Agora você pode fazer login.';
    } else {
        $response['message'] = 'Erro ao cadastrar. Tente novamente.';
    }
} elseif ($action == 'login') {
    $user->email = $_POST['email'] ?? '';
    $user->senha = $_POST['senha'] ?? '';

    if (empty($user->email) || empty($user->senha)) {
        $response['message'] = 'Preencha email e senha.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email inválido.';
        echo json_encode($response);
        exit;
    }

    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->nome;
        $response['success'] = true;
        $response['message'] = 'Login realizado com sucesso.';
    } else {
        $response['message'] = 'Email ou senha incorretos.';
    }
} else {
    $response['message'] = 'Ação desconhecida.';
}

echo json_encode($response);
?>