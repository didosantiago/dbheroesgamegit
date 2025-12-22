<?php
/**
 * Character Selection Protection
 * Include this at the top of pages that require a character to be selected
 */

if(!isset($_SESSION['PERSONAGEMID']) || empty($_SESSION['PERSONAGEMID'])){
    // Store the intended destination
    $_SESSION['redirect_after_character_selection'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to character selection with error message
    header('Location: '.BASE.'meus-personagens?error=no_character');
    exit;
}
