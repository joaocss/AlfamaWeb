<?php
session_start();
require_once "../config/database.php";

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        login($pdo);
        break;
    case 'register':
        register($pdo);
        break;
    case 'logout':
        logout();
        break;
    default:
        echo json_encode(["success" => false, "message" => "Ação inválida!"]);
}

function login($pdo)
{
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["success" => true, "message" => "Login efetuado!"]);
    } else {
        echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
    }
}

function register($pdo)
{
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
        return;
    }

    if (strlen($senha) < 8) {
        echo json_encode(["success" => false, "message" => "Senha deve ter pelo menos 8 caracteres."]);
        return;
    }

    // Verifica se o e-mail já existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "E-mail já cadastrado."]);
        return;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
    if ($stmt->execute([$nome, $email, $senhaHash])) {
        echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao cadastrar."]);
    }
}

function logout()
{
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}