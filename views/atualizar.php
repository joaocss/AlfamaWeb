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
function atualizarPerfil($pdo, $userId, $nome, $email, $telefone, $endereco, $cpf, $empresa, $imagemPath) {
    $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, endereco = ?, cpf = ?, empresa = ?, imagem_path = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $telefone, $endereco, $cpf, $empresa, $imagemPath, $userId]);
    return $stmt->rowCount();
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

    // Validação básica no servidor

    $imagemPath = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoNome = uniqid() . '_' . $_FILES['foto']['name'];
        $destino = "../imagens/uploads/" . $fotoNome;

        if (move_uploaded_file($fotoTmp, $destino)) {
            $imagemPath = $destino; // Caminho completo da imagem
        } else {
            $erro = "Erro ao fazer upload da imagem.";
        }
    }

      if (empty($nome) || empty($email)) {
          $erro = "Nome e email são obrigatórios.";
          // Recarrega os dados do usuário para exibir no formulário
          $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
          $stmt->execute([$_SESSION['user_id']]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $erro = "Formato de email inválido.";
          // Recarrega os dados do usuário para exibir no formulário
          $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
          $stmt->execute([$_SESSION['user_id']]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
      } else {
          $atualizado = atualizarPerfil($pdo, $_SESSION['user_id'], $nome, $email, $telefone, $endereco, $cpf, $empresa, $imagemPath);


          $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, endereco = ?, cpf = ?, empresa = ?, imagem_path = ? WHERE id = ?");
          $stmt->execute([$nome, $email, $telefone, $endereco, $cpf, $empresa, $imagemPath, $_SESSION['user_id']]);

          if ($atualizado > 0) {
              $sucesso = "        Perfil atualizado com sucesso!";
              // Recarrega os dados do usuário atualizados
              $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
              $stmt->execute([$_SESSION['user_id']]);
              $user = $stmt->fetch(PDO::FETCH_ASSOC);
          } else {
              $erro = "Erro ao atualizar o perfil. Verifique se algum dados foi alterado e tente novamente.";
              // **Adicione esta parte para recarregar os dados mesmo em caso de erro**
              $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
              $stmt->execute([$_SESSION['user_id']]);
              $user = $stmt->fetch(PDO::FETCH_ASSOC);
          }
          
      }
  } else {
      // Busca os dados do usuário para exibir no formulário (carregamento inicial)
      $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
      $stmt->execute([$_SESSION['user_id']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Perfil do Corretor</title>
  <link rel="stylesheet" href="../folhas/profile.css">
</head>
<body>

  <header>
    <img src="../imagens/logo.svg" alt="Logo Alfama Web">
    <div class="menu-container">
      <div class="menu-icon">&#9776;</div>
      <div class="dropdown-menu">
        <a href="#" id="logoutBtn">Sair</a>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="profile-image-container">
    <img id="profileImage" src="<?= htmlspecialchars($user['imagem_path'] ? $user['imagem_path'] : '../imagens/perfil.svg') ?>" alt="Foto de Perfil">
      <label for="uploadFoto" class="edit-icon">&#128247;</label>
      <input type="file" id="uploadFoto" name="foto" accept="image/*">
    </div>

    <h1 class="profile-name"><?= htmlspecialchars($user['nome']) ?></h1>
    <p class="profile-role"><?= htmlspecialchars($user['empresa']) ?></p>

    <form id="atualizarCadastroForm" method="post" enctype="multipart/form-data">
    
      <?php if (isset($sucesso)): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
      <?php endif; ?>
      
      <?php if (isset($erro)): ?>
        
        <div class="alert alert-danger"><?= $erro ?></div>
        
      <?php endif; ?>

      <div class="form-group">
        <label for="nome">Nome Completo</label>
        <input type="text" id="nome" name="nome" placeholder="Digite seu nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Digite seu email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="tel" id="telefone" name="telefone" placeholder="Digite seu telefone" value="<?= htmlspecialchars($user['telefone']) ?>">
      </div>
      <div class="form-group">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF" value="<?= htmlspecialchars($user['cpf']) ?>">
      </div>
      <div class="form-group">
        <label for="empresa">Empresa</label>
        <input type="text" id="empresa" name="empresa" placeholder="Digite sua empresa" value="<?= htmlspecialchars($user['empresa']) ?>">
      </div>
      <div class="form-group">
        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" placeholder="Digite seu endereço" value="<?= htmlspecialchars($user['endereco']) ?>">
      </div>

      <div class="full-width">
        <button type="submit" class="submit-btn">Atualizar cadastro</button>
      </div>
    </form>
  </div>

  <script>
    // Script para upload de imagem
    const uploadInput = document.getElementById("uploadFoto");
    const profileImage = document.getElementById("profileImage");

    uploadInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
          profileImage.src = e.target.result;
        };

        reader.readAsDataURL(file);
      }
    });
    const menuIcon = document.querySelector('.menu-icon');
  const dropdownMenu = document.querySelector('.dropdown-menu');

  menuIcon.addEventListener('click', () => {
    dropdownMenu.classList.toggle('show');
  });

  // Fecha o menu ao clicar fora dele
  document.addEventListener('click', function(event) {
    if (!event.target.closest('.menu-container')) {
      dropdownMenu.classList.remove('show');
    }
  });
    // Script para logout
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Deseja realmente sair?')) {
        window.location.href = '../controllers/controller.php?action=logout';
      }
    });
  </script>

</body>
</html>