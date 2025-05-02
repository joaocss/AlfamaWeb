<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

// Verifica se foi solicitado logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../views/login.html?logout=1");
    exit;
}

require_once "../config/database.php";

// Função para atualizar o perfil no banco de dados
function atualizarPerfil($pdo, $userId, $nome, $email, $telefone, $endereco, $cpf, $empresa) {
    $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, endereco = ?, cpf = ?, empresa = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $telefone, $endereco, $cpf, $empresa, $userId]);
    return $stmt->rowCount(); // Retorna o número de linhas afetadas (0 se falhou, 1 se sucesso)
var_dump($stmt->errorInfo()); // Debug: Mostra erros do PDO
}

// Processa a requisição de atualização se o formulário for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize os dados de entrada
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);

    // Validação básica no servidor (você pode adicionar mais)
    if (empty($nome) || empty($email)) {
        $erro = "Nome e email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Formato de email inválido.";
    } else {
        // Tenta atualizar o perfil
        $atualizado = atualizarPerfil($pdo, $_SESSION['user_id'], $nome, $email, $telefone, $endereco, $cpf, $empresa);
var_dump($atualizado); // Debug: Mostra o número de linhas afetadas
        if ($atualizado > 0) {
            $sucesso = "Perfil atualizado com sucesso!";
            // Recarrega os dados do usuário atualizados
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $erro = "Erro ao atualizar o perfil.";
        }
    }
} else {
    // Busca os dados do usuário para exibir no formulário (se não for um POST)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>