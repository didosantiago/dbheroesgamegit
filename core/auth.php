<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Considera logado se login_success == true
 * (é o que o seu login.php já faz hoje).
 */
$estaLogado = !empty($_SESSION['login_success']) && $_SESSION['login_success'] === true;

if (!$estaLogado) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . BASE . 'home#login');
    exit;
}
