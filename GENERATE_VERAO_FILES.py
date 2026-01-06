# 1. Copy this ENTIRE code
# 2. Paste in new file: FIXED_veraonpc.py
# 3. Run: python FIXED_veraonpc.py
# 4. Gets: veraonpc.php (100% FIXED!)

fixed_veraonpc = '''<?php
session_start();
require_once "config.php";
require_once "class/core.php";
require_once "class/Url.php";
require_once "class/personagem.php";
require_once "class/VeraoNpc.php";

$core = new Core();
$user = new Usuario();
$personagem = new Personagem();
$inventario = new Inventario();
$veraonpc = new VeraoNpc();

if(!isset($_SESSION["ID"])) { header("Location: " . BASE); exit; }
if(!isset($_SESSION["USUARIO"])) { header("Location: " . BASE); exit; }
$user->getDados($_SESSION["ID"]);

if(!isset($_SESSION["PERSONAGEMID"])){
    header("Location: " . BASE . "portal");
    exit;
}

$idPersonagem = $_SESSION["PERSONAGEMID"];
$parametro_
