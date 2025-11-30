<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Personagens.php";

// Validate session
if(!isset($_SESSION['PERSONAGEMID'])){
    echo json_encode(['success' => false, 'message' => 'Sessão inválida']);
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idInventario = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if($idInventario == 0){
    echo json_encode(['success' => false, 'message' => 'Item inválido']);
    exit;
}

try {
    $core = new Core();
    $personagem = new Personagens();
    $personagem->getGuerreiro($idPersonagem);
    
    // Get consumable item from inventory
    $sql = "SELECT pii.*, i.* 
            FROM personagens_inventario_itens as pii
            INNER JOIN itens as i ON i.id = pii.idItem
            WHERE pii.id = ? AND pii.idPersonagem = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idInventario, $idPersonagem]);
    $item = $stmt->fetch();
    
    if(!$item){
        echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
        exit;
    }
    
    // Check if it's actually a consumable
    $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao', 'pocao'];
    if(!in_array(strtolower($item->tipo), $consumable_types)){
        echo json_encode(['success' => false, 'message' => 'Este item não é consumível']);
        exit;
    }
    
    // ✅ Get character stats (USE SNAKE_CASE)
    $level = $personagem->nivel;
    $hp_atual = $personagem->hp;
    $ki_usado = $personagem->ki_usado ?? 0;            // ✅ FIXED
    $energia_usada = $personagem->energia_usada ?? 0;  // ✅ FIXED
    
    // Calculate MAX stats
    $valor_hp_max = ($level * 50) + 100;
    $valor_ki_max = ($level * 50) + 50;
    $valor_energia_max = 100;
    
    $new_hp = $hp_atual;
    $new_ki_usado = $ki_usado;
    $new_energia_usada = $energia_usada;
    $capsule_buff = null;
    
    // HP RESTORATION - Database value = percentage of MAX HP
    if(isset($item->efeito_hp) && $item->efeito_hp > 0){
        $percentage = $item->efeito_hp;
        $heal_amount = floor($valor_hp_max * ($percentage / 100));
        $new_hp = min($hp_atual + $heal_amount, $valor_hp_max);
    }
    
    // KI RESTORATION - Database value = percentage of MAX KI
    if(isset($item->efeito_ki) && $item->efeito_ki > 0){
        $percentage = $item->efeito_ki;
        $restore_amount = floor($valor_ki_max * ($percentage / 100));
        $new_ki_usado = max($ki_usado - $restore_amount, 0);
    }
    
    // ENERGY RESTORATION - Direct value
    if(isset($item->efeito_energia) && $item->efeito_energia > 0){
        $new_energia_usada = max($energia_usada - $item->efeito_energia, 0);
    }
    
    // CAPSULE BUFF SYSTEM (Experience Boost)
    if(strtolower($item->tipo) == 'capsula'){
        if(isset($item->efeito_experiencia) && $item->efeito_experiencia > 0){
            $buff_percentage = $item->efeito_experiencia;
            $buff_end_time = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            $sql_check = "SELECT * FROM personagens_buffs 
                          WHERE idPersonagem = ? AND tipo = 'experiencia' AND ativo = 1";
            $stmt_check = DB::prepare($sql_check);
            $stmt_check->execute([$idPersonagem]);
            $existing_buff = $stmt_check->fetch();
            
            if($existing_buff){
                $sql_update = "UPDATE personagens_buffs 
                               SET porcentagem = ?, tempo_fim = ? 
                               WHERE id = ?";
                $stmt_update = DB::prepare($sql_update);
                $stmt_update->execute([$buff_percentage, $buff_end_time, $existing_buff->id]);
            } else {
                $campos_buff = array(
                    'idPersonagem' => $idPersonagem,
                    'tipo' => 'experiencia',
                    'porcentagem' => $buff_percentage,
                    'tempo_inicio' => date('Y-m-d H:i:s'),
                    'tempo_fim' => $buff_end_time,
                    'ativo' => 1
                );
                $core->insert('personagens_buffs', $campos_buff);
            }
            $capsule_buff = $buff_percentage;
        }
    }
    
    // ✅ UPDATE CHARACTER STATS (USE SNAKE_CASE)
    $sql_update = "UPDATE usuarios_personagens 
                   SET hp = ?, ki_usado = ?, energia_usada = ? 
                   WHERE id = ?";
    $stmt_update = DB::prepare($sql_update);
    $stmt_update->execute([$new_hp, $new_ki_usado, $new_energia_usada, $idPersonagem]);
    
    // Remove item from inventory
    $sql_delete = "DELETE FROM personagens_inventario_itens WHERE id = ?";
    $stmt_delete = DB::prepare($sql_delete);
    $stmt_delete->execute([$idInventario]);
    
    // Prepare response message
    $message = 'Item usado: ' . htmlspecialchars($item->nome);
    if($capsule_buff){
        $message .= ' - Bônus de ' . $capsule_buff . '% de experiência ativado por 30 minutos!';
    }
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'hp' => $new_hp,
        'hp_max' => $valor_hp_max,
        'ki_usado' => $new_ki_usado,
        'ki_max' => $valor_ki_max,
        'energia_usada' => $new_energia_usada,
        'energia_max' => $valor_energia_max,
        'capsule_buff' => $capsule_buff
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . htmlspecialchars($e->getMessage())]);
}
?>
