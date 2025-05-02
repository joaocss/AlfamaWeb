<?php
require_once "../config/database.php";

$token = $_GET['token'] ?? '';
$senha_atualizada = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    if ($nova_senha !== $confirma_senha) {
        $erro = "As senhas não coincidem";
    } else {
        // Verifica o token
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expira > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Atualiza a senha e limpa o token
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?");
            $stmt->execute([$senha_hash, $user['id']]);
            $senha_atualizada = true;
        } else {
            $erro = "Link inválido ou expirado";
        }
    }
} elseif ($token) {
    // Verifica se o token é válido (apenas visualização)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expira > NOW()");
    $stmt->execute([$token]);
    $token_valido = $stmt->fetch();
    
    if (!$token_valido) {
        $erro = "Link inválido ou expirado";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - Alfama Web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .password-reset-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo-reset {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-reset-container">
            <div class="logo-reset">
                <img src="../imagens/logo.svg" alt="Alfama Web" width="150">
            </div>
            
            <h2 class="text-center mb-4">Criar Nova Senha</h2>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
                <div class="d-grid gap-2">
                    <a href="../views/esqueci_senha.php" class="btn btn-primary">Solicitar novo link</a>
                </div>
            <?php elseif ($senha_atualizada): ?>
                <div class="alert alert-success">Senha atualizada com sucesso!</div>
                <div class="d-grid gap-2">
                    <a href="../views/login.html" class="btn btn-primary">Ir para o Login</a>
                </div>
            <?php elseif ($token): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirma_senha" class="form-label">Confirme a Nova Senha</label>
                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required minlength="6">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Atualizar Senha</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Token não fornecido</div>
                <div class="d-grid gap-2">
                    <a href="../views/esqueci_senha.php" class="btn btn-primary">Solicitar link de redefinição</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>