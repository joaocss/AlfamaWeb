# AlfamaWeb
# 🧑‍💻 Projeto Alfama Web - CRUD com Login Google

Este projeto consiste em um sistema CRUD de usuários com funcionalidades de **cadastro**, **login** (tradicional e com **Google**), e **edição de perfil**, desenvolvido com PHP, MySQL, Bootstrap 5, HTML, CSS e JavaScript (com AJAX).

---

## 🚀 Página inicial

O ponto de entrada do projeto está em: views/index.html


---

## 🛠 Tecnologias utilizadas

- PHP 8+
- MySQL
- HTML5, CSS3 e JavaScript
- Bootstrap 5.x
- Composer (gerenciador de dependências PHP)
- Google API (OAuth 2.0)
- AJAX

---

## 🖥️ Requisitos para rodar o projeto localmente

### ✅ 1. Instale um ambiente local

Use um pacote como:

- [XAMPP](https://www.apachefriends.org/)
- Laragon
- WAMP (Windows)
- MAMP (Mac)

Certifique-se de que PHP, Apache e MySQL estejam funcionando corretamente.

---

### ✅ 2. Instale o [Composer](https://getcomposer.org/)

Depois, instale as dependências do projeto. No terminal, dentro da pasta raiz do projeto:

```bash
composer install
<?php
$host = 'localhost';
$db   = 'alfama_crud';
$user = 'root';
$pass = ''; // padrão XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

