<?php
session_start();

require_once('../core/config.php');
require_once('../core/DB.php');
require_once('../core/Core.php');
require_once('../core/Personagens.php');

$core = new Core();
$personagem = new Personagens();

if(isset($_POST['id']) && $_POST['id'] != ''){
    $idPersonagem = intval($_POST['id']);
    
    // Get active hunt for this character
    $sql = "SELECT * FROM cacadas 
            WHERE idPersonagem = $idPersonagem 
            AND concluida = 0 
            AND cancelada = 0 
            ORDER BY id DESC LIMIT 1";
    $stmt = DB::prepare($sql);
    $stmt->execute();
    
    if($stmt->rowCount() > 0){
        $cacada = $stmt->fetch();
        
        $tempo_restante = $cacada->tempo_final - time();
        
        if($tempo_restante > 0){
            // Hunt still running - return seconds remaining
            echo $tempo_restante;
        } else {
            // Hunt finished - BUT CHECK IF IT WAS CANCELLED FIRST!
            
            // Re-check if hunt was cancelled (race condition protection)
            $sql_recheck = "SELECT cancelada FROM cacadas WHERE id = " . $cacada->id;
            $stmt_recheck = DB::prepare($sql_recheck);
            $stmt_recheck->execute();
            $recheck = $stmt_recheck->fetch();
            
            if($recheck->cancelada == 1){
                // Hunt was cancelled! Don't give rewards
                echo "0";
                exit;
            }
            
            // ✅ Hunt finished successfully - Give rewards
            
            // Mark as completed
            $campos_cacada = array('concluida' => 1);
            $where_cacada = 'id = ' . $cacada->id;
            $core->update('cacadas', $campos_cacada, $where_cacada);
            
            // Get character data
            $sql_char = "SELECT * FROM usuarios_personagens WHERE id = " . $cacada->idPersonagem;
            $stmt_char = DB::prepare($sql_char);
            $stmt_char->execute();
            $char_data = $stmt_char->fetch();
            
            // Calculate rewards
            $gold_ganho = intval($cacada->gold);
            $exp_ganho = intval($cacada->exp);
            
            // Update character gold and exp
            $novo_gold = intval($char_data->gold) + $gold_ganho;
            $novo_gold_total = intval($char_data->gold_total) + $gold_ganho;
            $novo_exp = intval($char_data->exp) + $exp_ganho;
            
            $campos_personagem = array(
                'gold' => $novo_gold,
                'gold_total' => $novo_gold_total,
                'exp' => $novo_exp
            );
            $where_personagem = 'id = ' . $cacada->idPersonagem;
            $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
            
            // Insert rewards notification for popup
            $campos_valor = array(
                'idPersonagem' => $cacada->idPersonagem,
                'gold' => $gold_ganho,
                'exp' => $exp_ganho,
                'visualizado' => 0
            );
            $core->insert('personagens_new_valores', $campos_valor);
            
            // Check for level up
            if(method_exists($personagem, 'checkLevelUp')){
                $personagem->checkLevelUp($cacada->idPersonagem);
            }
            
            // Clear session
            unset($_SESSION['cacada']);
            unset($_SESSION['cacada_id']);
            
            // Return 0 to trigger page reload and show popup
            echo "0";
        }
    } else {
        echo "0";
    }
} else {
    echo "0";
}
?>
