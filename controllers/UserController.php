<?php
require_once "../config/database.php";

// Função para validar e processar a imagem
function processarFotoPerfil() {
    if (!isset($_FILES['fotoPerfil']) || $_FILES['fotoPerfil']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Verifica se é uma imagem válida
    $check = getimagesize($_FILES['fotoPerfil']['tmp_name']);
    if ($check === false) {
        throw new Exception("O arquivo não é uma imagem válida.");
    }

    // Verifica o tamanho (máximo 2MB)
    if ($_FILES['fotoPerfil']['size'] > 2097152) {
        throw new Exception("A imagem deve ter no máximo 2MB.");
    }

    // Verifica o tipo MIME
    $mime = $check['mime'];
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        throw new Exception("Somente imagens JPEG, PNG ou GIF são permitidas.");
    }

    // Lê o conteúdo da imagem
    return file_get_contents($_FILES['fotoPerfil']['tmp_name']);
}

function updateProfile($pdo) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "Usuário não autenticado!"]);
        return;
    }

    $id = $_SESSION['user_id'];
    $response = ["success" => false, "message" => "Ação não reconhecida."];

    try {
        // Verifica se é uma requisição com FormData (contendo arquivo)
        if (!empty($_POST['profileData'])) {
            $profileData = json_decode($_POST['profileData'], true);
            
            if (!$profileData) {
                throw new Exception("Dados inválidos ou ausentes.");
            }

            // Validação dos campos obrigatórios
            if (empty($profileData['nome']) || empty($profileData['email'])) {
                throw new Exception("Nome e E-mail são obrigatórios.");
            }

            // Processa a foto se foi enviada
            $fotoPerfil = null;
            try {
                $fotoPerfil = processarFotoPerfil();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            // Inicia transação
            $pdo->beginTransaction();

            // Atualiza os dados no banco de dados
            if ($fotoPerfil !== null) {
                $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, cpf = ?, empresa = ?, endereco = ?, foto_perfil = ? WHERE id = ?");
                $stmt->execute([
                    $profileData['nome'],
                    $profileData['email'],
                    $profileData['telefone'] ?? null,
                    $profileData['cpf'] ?? null,
                    $profileData['empresa'] ?? null,
                    $profileData['endereco'] ?? null,
                    $fotoPerfil,
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, cpf = ?, empresa = ?, endereco = ? WHERE id = ?");
                $stmt->execute([
                    $profileData['nome'],
                    $profileData['email'],
                    $profileData['telefone'] ?? null,
                    $profileData['cpf'] ?? null,
                    $profileData['empresa'] ?? null,
                    $profileData['endereco'] ?? null,
                    $id
                ]);
            }

            $pdo->commit();
            $response = ["success" => true, "message" => "Perfil atualizado com sucesso!", "hasPhoto" => ($fotoPerfil !== null)];

        } 
        // Se for uma requisição apenas para atualizar a foto
        elseif (!empty($_FILES['fotoPerfil'])) {
            $fotoPerfil = processarFotoPerfil();
            
            if ($fotoPerfil === null) {
                throw new Exception("Nenhuma imagem válida foi enviada.");
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE users SET foto_perfil = ? WHERE id = ?");
            $stmt->execute([$fotoPerfil, $id]);
            $pdo->commit();
            
            $response = ["success" => true, "message" => "Foto de perfil atualizada com sucesso!"];
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erro no UserController: " . $e->getMessage());
        $response = ["success" => false, "message" => $e->getMessage()];
    }

    echo json_encode($response);
}

// Verifica se é uma ação de upload separado apenas da foto
if (isset($_GET['action']) && $_GET['action'] === 'uploadPhoto') {
    updateProfile($pdo);
    exit;
}

// Ação padrão de update
if (isset($_GET['action']) && $_GET['action'] === 'update') {
    updateProfile($pdo);
    exit;
}

echo json_encode(["success" => false, "message" => "Ação não especificada."]);