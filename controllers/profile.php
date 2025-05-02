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

function atualizarPerfil($pdo, $userId, $nome, $email, $telefone, $endereco, $cpf, $empresa) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, endereco = ?, cpf = ?, empresa = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $telefone, $endereco, $cpf, $empresa, $userId]);
        
        // Debug: Verifique se há erros
        if($stmt->errorCode() != '00000') {
            error_log("Erro no PDO: ".print_r($stmt->errorInfo(), true));
        }
        
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Erro ao atualizar perfil: ".$e->getMessage());
        return false;
    }
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Atualizar Cadastro - Alfama Web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../folhas/style.css">
</head>
<body>
    <div class="header">
        <div class="logo-header">
            <img src="../imagens/logo.svg" alt="Logo Alfama Web">
        </div>
        <button class="menu-button">&#9776;</button>
    </div>

    <div class="profile-section">
        <div class="profile-image-container" id="profileImageContainer">
            <img src="../imagens/perfil.svg" alt="Foto de Perfil" class="profile-image" id="profileImage">
            <label for="uploadFoto" class="edit-icon">&#128247;</label>
            <input type="file" id="uploadFoto" accept="image/*">
        </div>
        <h1 class="profile-name" id="nomeUsuario"> <?= htmlspecialchars($user['nome']) ?></h1>
        <p class="profile-info">Corretor(a)</p>
    </div>

    <div class="cadastro-form">
        <h2 class="mb-4">Atualizar Cadastro</h2>
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        <form id="atualizarCadastroForm" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="nomeCompleto" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite Seu Nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="Digite seu telefone" value="<?= htmlspecialchars($user['telefone']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF" value="<?= htmlspecialchars($user['cpf']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" placeholder="Digite sua empresa"value="<?= htmlspecialchars($user['empresa']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Digite seu endereço"value="<?= htmlspecialchars($user['endereco']) ?>" required>
                </div>
            </div>
            <div class="update-button-container">
                <button type="submit" class="update-button">Atualizar cadastro</button>
            </div>
        </form>
    </div>
    <div class="mt-3">
        <button id="logoutBtn" class="btn btn-danger w-100">Sair</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/profile.js"></script>

    <script>
        // Mostra mensagem de logout se vier por GET
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('logout')) {
                const logoutToast = new bootstrap.Toast(document.getElementById('logoutToast'));
                logoutToast.show();

                // Redireciona após 2 segundos
                setTimeout(() => {
                    window.location.href = '../views/login.html';
                }, 2000);
            }
        });

        // Adiciona evento de clique no botão de logout
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('Deseja realmente sair?')) {
                window.location.href = '../controllers/controller.php?action=logout';
            }
        });
    </script>
</body>
</html>