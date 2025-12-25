<?php
session_start();

// Check if user is logged in
$idPersonagem = null;

if(isset($_SESSION['PERSONAGEM']['ID'])) {
    $idPersonagem = $_SESSION['PERSONAGEM']['ID'];
} else if(isset($_SESSION['PERSONAGEM_ID'])) {
    $idPersonagem = $_SESSION['PERSONAGEM_ID'];
} else if(isset($_SESSION['PERSONAGEMID'])) {
    $idPersonagem = $_SESSION['PERSONAGEMID'];
} else if(isset($_SESSION['ID'])) {
    $idPersonagem = $_SESSION['ID'];
} else if(isset($_SESSION['id'])) {
    $idPersonagem = $_SESSION['id'];
} else if(isset($_SESSION['personagem_id'])) {
    $idPersonagem = $_SESSION['personagem_id'];
}

if(!$idPersonagem || $idPersonagem <= 0){
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado.']);
    exit;
}

// Get POST data  
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$idEquipe = isset($_POST['idEquipe']) ? intval($_POST['idEquipe']) : 0;

// Validate team ID
if($idEquipe <= 0){
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'ID da equipe inválido.']);
    exit;
}

// Validate action type
if(empty($tipo)){
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
    exit;
}

// Connect to database
define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', '');
define('DATABASE', 'dbheroes');

$conexao = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
mysqli_set_charset($conexao, 'utf8mb4');

if(!$conexao){
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Erro de conexão.']);
    exit;
}

// Check if user is member
$stmtMembro = mysqli_prepare($conexao, "SELECT * FROM equipes_membros WHERE idPersonagem = ? AND idEquipe = ?");
mysqli_stmt_bind_param($stmtMembro, "ii", $idPersonagem, $idEquipe);
mysqli_stmt_execute($stmtMembro);
$resultMembro = mysqli_stmt_get_result($stmtMembro);

if(!$resultMembro || mysqli_num_rows($resultMembro) == 0){
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Você não é membro desta equipe.']);
    mysqli_close($conexao);
    exit;
}

// 🎯 GET TEAM DATA
$stmtEquipe = mysqli_prepare($conexao, "SELECT * FROM equipes WHERE id = ?");
mysqli_stmt_bind_param($stmtEquipe, "i", $idEquipe);
mysqli_stmt_execute($stmtEquipe);
$resultEquipe = mysqli_stmt_get_result($stmtEquipe);
$equipe = mysqli_fetch_object($resultEquipe);

if(!$equipe) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Equipe não encontrada.']);
    mysqli_close($conexao);
    exit;
}

// 👑 Helper function to get user role with SMALL CROWN icon
function getUserRole($userId, $idEquipe, $conexao, $equipe) {
    $userId = intval($userId);

    // Check if user is team creator (the real leader)
    if($userId == $equipe->idCriador) {
        return [
            'color' => '#ffae00ff',
            'role' => 'LÍDER',
            'crown' => '<i class="fas fa-crown" style="color: #1883fdff; font-size: 10px; margin-left: 3px; vertical-align: middle;"></i>'
        ];
    }

    // 🎯 Check equipes_membros table for lider and vicelider
    $stmtRole = mysqli_prepare($conexao, 
        "SELECT lider, vicelider FROM equipes_membros 
         WHERE idPersonagem = ? AND idEquipe = ? AND status = 1");
    mysqli_stmt_bind_param($stmtRole, "ii", $userId, $idEquipe);
    mysqli_stmt_execute($stmtRole);
    $resultRole = mysqli_stmt_get_result($stmtRole);

    if($resultRole && mysqli_num_rows($resultRole) > 0) {
        $membro = mysqli_fetch_object($resultRole);

        // Check if LIDER
        if($membro->lider == 1) {
            return [
                'color' => '#ffd700',
                'role' => 'LÍDER',
                'crown' => '<i class="fas fa-crown" style="color: #00ff9dff; font-size: 10px; margin-left: 3px; vertical-align: middle;"></i>'
            ];
        }

        // Check if VICE LIDER
        if($membro->vicelider == 1) {
            return [
                'color' => '#ff6b6b',
                'role' => 'VICE LÍDER',
                'crown' => '<i class="fas fa-crown" style="color: #ff6b6b; font-size: 10px; margin-left: 3px; vertical-align: middle;"></i>'
            ];
        }
    }

    // Regular member
    return [
        'color' => '#667eea',
        'role' => 'MEMBRO',
        'crown' => ''
    ];
}

// Process action
if($tipo === 'listar'){
    // Get pagination parameters
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $perPage = 50;
    $offset = ($page - 1) * $perPage;

    // Get chat messages with author names using JOIN
    $stmtMsgs = mysqli_prepare($conexao, 
        "SELECT m.*, up.nome as autorNome, up.id as autorId
         FROM equipes_chat_mensagens m 
         LEFT JOIN usuarios_personagens up ON up.id = m.idPersonagem 
         WHERE m.idEquipe = ? 
         ORDER BY m.id DESC 
         LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmtMsgs, "iii", $idEquipe, $perPage, $offset);
    mysqli_stmt_execute($stmtMsgs);
    $result = mysqli_stmt_get_result($stmtMsgs);

    if($result && mysqli_num_rows($result) > 0){
        $mensagens = [];
        while($row = mysqli_fetch_object($result)){
            $mensagens[] = $row;
        }

        // Reverse to show oldest first
        $mensagens = array_reverse($mensagens);

        foreach($mensagens as $msg){
            $nomeAutor = !empty($msg->autorNome) ? htmlspecialchars($msg->autorNome) : 'Desconhecido';
            $hora = date('H:i', strtotime($msg->data));
            $textoMsg = nl2br(htmlspecialchars($msg->mensagem));

            // 👑 GET THE ROLE WITH SMALL CROWN!
            $userRole = getUserRole($msg->autorId, $idEquipe, $conexao, $equipe);

            echo '<div class="mensagem-chat">';
            echo '<div class="autor" style="color: '.$userRole['color'].';">';
            echo $nomeAutor . $userRole['crown'];
            echo ' <span class="hora">'.$hora.'</span>';
            echo '</div>';
            echo '<div class="texto">'.$textoMsg.'</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-messages">Nenhuma mensagem ainda. Seja o primeiro a falar!</div>';
    }

} else if($tipo === 'enviar'){
    header('Content-Type: application/json; charset=utf-8');

    // COOLDOWN CHECK
    $cooldownSeconds = 15;

    $stmtLastMsg = mysqli_prepare($conexao, 
        "SELECT data 
         FROM equipes_chat_mensagens 
         WHERE idPersonagem = ? AND idEquipe = ? 
         ORDER BY id DESC 
         LIMIT 1");
    mysqli_stmt_bind_param($stmtLastMsg, "ii", $idPersonagem, $idEquipe);
    mysqli_stmt_execute($stmtLastMsg);
    $resultLastMsg = mysqli_stmt_get_result($stmtLastMsg);

    if($resultLastMsg && mysqli_num_rows($resultLastMsg) > 0){
        $rowLastMsg = mysqli_fetch_object($resultLastMsg);
        $ultimaMensagem = $rowLastMsg->data;

        $agora = new DateTime();
        $ultimaMsg = new DateTime($ultimaMensagem);
        $tempoDecorrido = $agora->getTimestamp() - $ultimaMsg->getTimestamp();

        if($tempoDecorrido < $cooldownSeconds){
            $tempoRestante = $cooldownSeconds - $tempoDecorrido;
            http_response_code(429);
            echo json_encode([
                'success' => false, 
                'message' => 'Aguarde '.$tempoRestante.' segundos para enviar outra mensagem.',
                'cooldown' => $tempoRestante
            ]);
            mysqli_close($conexao);
            exit;
        }
    }

    // Get message
    $mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';

    if(empty($mensagem)){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mensagem vazia']);
        mysqli_close($conexao);
        exit;
    }

    if(strlen($mensagem) > 255){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mensagem muito longa (máximo 255 caracteres)']);
        mysqli_close($conexao);
        exit;
    }

    // Insert message
    $data = date('Y-m-d H:i:s');
    $stmtInsert = mysqli_prepare($conexao, 
        "INSERT INTO equipes_chat_mensagens (idEquipe, idPersonagem, mensagem, data) 
         VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtInsert, "iiss", $idEquipe, $idPersonagem, $mensagem, $data);
    $resultInsert = mysqli_stmt_execute($stmtInsert);

    if($resultInsert){
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem: ' . mysqli_error($conexao)]);
    }

} else {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
}

mysqli_close($conexao);
?>