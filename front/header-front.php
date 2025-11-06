<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define BASE if not defined
if(!defined('BASE')){
    define('BASE', 'http://localhost:8080/dbheroes/');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Heroes - RPG Game</title>
    
    <link rel="stylesheet" href="<?php echo BASE; ?>assets/css/style.css">
    <script src="<?php echo BASE; ?>assets/js/jquery.min.js"></script>
</head>
<body>

<div class="page-wrapper">
    <header>
        <nav>
            <a href="<?php echo BASE; ?>">Início</a>
            <a href="<?php echo BASE; ?>cadastro">Criar Conta</a>
            <a href="<?php echo BASE; ?>login">Login</a>
        </nav>
    </header>
    <main>
