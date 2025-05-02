<?php
require_once "../config/database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido";
    } else {
        // Verifica se o email existe no banco
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Gera um token único com validade de 1 hora
            $token = bin2hex(random_bytes(32));
            $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Armazena o token no banco
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expira = ? WHERE id = ?");
            $stmt->execute([$token, $expira_em, $user['id']]);
            
            // Configuração do email DEVE estar DENTRO deste bloco, após ter o $email e $token
            $reset_link = "http://localhost/alfama-crud/views/redefinir_senha.php?token=$token";
            
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Substitua pelo seu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'joao.sena.ufs@gmail.com'; // Seu email
                $mail->Password = 'ukbl hlsx vmjc mrgr'; // Sua senha
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('joao.sena.ufs@gmail.com', 'Alfama Web');
                $mail->addAddress($email); // Agora $email está definido
                
                $mail->isHTML(true);
                $mail->Subject = 'Redefinição de Senha';
                $mail->Body    = "Clique no link para redefinir sua senha: <a href='$reset_link'>$reset_link</a>";
                $mail->AltBody = "Clique no link para redefinir sua senha: $reset_link";
                
                if($mail->send()) {
                    $sucesso = "Enviamos um link para redefinir sua senha para o email informado.";
                } else {
                    $erro = "Erro ao enviar email. Tente novamente mais tarde.";
                }
            } catch (Exception $e) {
                error_log("Erro ao enviar email: {$mail->ErrorInfo}");
                $erro = "Erro ao enviar email. Tente novamente mais tarde.";
            }
        } else {
            $erro = "Email não encontrado";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha - Alfama Web</title>
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
            
            <h2 class="text-center mb-4">Redefinição de Senha</h2>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>
            
            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success"><?= $sucesso ?></div>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email cadastrado</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Enviar Link</button>
                        <a href="../views/login.html" class="btn btn-outline-secondary">Voltar ao Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>