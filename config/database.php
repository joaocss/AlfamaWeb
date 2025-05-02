<?php
// config/database.php

$host = 'localhost';
$dbname = 'alfama_crud';    // Banco de dados alfama
$username = 'root';    // Usuário padrão
$password = '';        // Senha vazia padrão

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>