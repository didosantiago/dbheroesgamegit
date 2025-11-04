<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Usuarios.php";
require_once "../core/Personagens.php";

$core = new Core();
$user = new Usuarios();
$personagem = new Personagens();

// Validate and sanitize ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo "error";
    exit;
}
$_SESSION["PERSONAGEMID"] = $id;

// Fetch user info if username is set
if(isset($_SESSION['username'])){
    $user->getUserInfo($_SESSION['username']);
}

// Ensure user ID is valid before continuing
if (empty($user->id)) {
    echo "error";
    exit;
}

// Set all user's characters offline first
$campos_volta = array('online' => 0);
$where_volta = 'idUsuario = "'.$user->id.'"';
$core->update('usuarios_personagens', $campos_volta, $where_volta);

// Set selected character online
$campos = array('online' => 1);
$where = 'id = "'.$id.'" AND idUsuario = "'.$user->id.'"';
$core->update('usuarios_personagens', $campos, $where);

// Check for expired hunts for this character
$sql_expired = "SELECT id FROM cacadas 
                WHERE idPersonagem = :idPersonagem 
                AND concluida = 0 
                AND cancelada = 0 
                AND tempo_final <= :now";
$stmt_expired = DB::prepare($sql_expired);
$stmt_expired->bindParam(':idPersonagem', $id, PDO::PARAM_INT);
$stmt_expired->bindParam(':now', $now = time(), PDO::PARAM_INT);
$stmt_expired->execute();

if($stmt_expired->rowCount() > 0){
    $row = $stmt_expired->fetch();
    $_SESSION['cacada_completed'] = true; // Flag to show popup
    $_SESSION['cacada'] = true;
    $_SESSION['cacada_id'] = $row->id;
}

echo "success";
?>