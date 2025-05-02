<?php
session_start();
header('Content-Type: application/json');
require_once "../config/database.php";

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                login($pdo);
            } else {
                http_response_code(405);
                echo jsonResponse(false, "Método não permitido");
            }
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                register($pdo);
            } else {
                http_response_code(405);
                echo jsonResponse(false, "Método não permitido");
            }
            break;
            
        case 'logout':
            logout();
            break;
            
        case 'check-session':
            checkSession();
            break;
            
        default:
            http_response_code(400);
            echo jsonResponse(false, "Ação inválida!");
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo jsonResponse(false, "Erro no servidor: " . $e->getMessage());
}

/**
 * Função auxiliar para padronizar respostas JSON
 */
function jsonResponse($success, $message, $additionalData = []) {
    return json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $additionalData));
}

/**
 * Obtém dados JSON da requisição
 */
function getJsonInput() {
    $data = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo jsonResponse(false, "JSON inválido");
        exit;
    }
    return $data ?? [];
}

function login($pdo) {
    $data = getJsonInput();
    $email = $data['email'] ?? '';
    $senha = $data['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        http_response_code(400);
        echo jsonResponse(false, "Preencha todos os campos!");
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    
    if (!$stmt->execute()) {
        throw new PDOException("Erro ao executar a query");
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        
        echo jsonResponse(true, "Login realizado com sucesso!", [
            'user' => [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo jsonResponse(false, "E-mail ou senha incorretos.");
    }
}

function register($pdo) {
    $data = getJsonInput();
    $nome = $data['nome'] ?? '';
    $email = $data['email'] ?? '';
    $senha = $data['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        http_response_code(400);
        echo jsonResponse(false, "Preencha todos os campos!");
        return;
    }

    if (strlen($senha) < 8) {
        http_response_code(400);
        echo jsonResponse(false, "Senha deve ter pelo menos 8 caracteres.");
        return;
    }

    // Verifica se e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    
    if (!$stmt->execute()) {
        throw new PDOException("Erro ao verificar e-mail existente");
    }
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo jsonResponse(false, "E-mail já cadastrado.");
        return;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $caminhoImagem = '../imagens/perfil.svg'; // Caminho padrão para a imagem de perfil
    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, foto_perfil) VALUES (:nome, :email, :senha, :foto)");
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':senha', $senhaHash, PDO::PARAM_STR);
    $stmt->bindParam(':foto', $caminhoImagem, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $userId = $pdo->lastInsertId();
        echo jsonResponse(true, "Cadastro realizado com sucesso!", [
            'user_id' => $userId
        ]);
    } else {
        throw new PDOException("Erro ao cadastrar usuário");
    }
}

function logout() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    echo jsonResponse(true, "Logout realizado com sucesso");
    header('Location: ../views/login.html');
}

function checkSession() {
    if (isset($_SESSION['user_id'])) {
        echo jsonResponse(true, "Sessão ativa", [
            'user_id' => $_SESSION['user_id'],
            'user_email' => $_SESSION['user_email'] ?? null
        ]);
    } else {
        http_response_code(401);
        echo jsonResponse(false, "Não autenticado");
    }
}