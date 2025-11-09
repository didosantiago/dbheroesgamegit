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
            echo $tempo_restante;
        } else {
            // Check if cancelled
            $sql_recheck = "SELECT cancelada FROM cacadas WHERE id = " . $cacada->id;
            $stmt_recheck = DB::prepare($sql_recheck);
            $stmt_recheck->execute();
            $recheck = $stmt_recheck->fetch();
            
            if($recheck->cancelada == 1){
                echo "0";
                exit;
            }
            
            // Give rewards
            $campos_cacada = array('concluida' => 1);
            $where_cacada = 'id = ' . $cacada->id;
            $core->update('cacadas', $campos_cacada, $where_cacada);
            
            $sql_char = "SELECT * FROM usuarios_personagens WHERE id = " . $cacada->idPersonagem;
            $stmt_char = DB::prepare($sql_char);
            $stmt_char->execute();
            $char_data = $stmt_char->fetch();
            
            $gold_ganho = intval($cacada->gold);
            $exp_ganho = intval($cacada->exp);
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
            
            // FIX: Use DIRECT SQL instead of $core->insert()
            $sql_insert = "INSERT INTO personagens_new_valores 
                          (idPersonagem, gold, exp, visualizado) 
                          VALUES (?, ?, ?, 0)";
            $stmt_insert = DB::prepare($sql_insert);
            $stmt_insert->execute([$cacada->idPersonagem, $gold_ganho, $exp_ganho]);
            
            if(method_exists($personagem, 'checkLevelUp')){
                $personagem->checkLevelUp($cacada->idPersonagem);
            }
            
            unset($_SESSION['cacada']);
            unset($_SESSION['cacada_id']);
            
            echo "0";
        }
    } else {
        echo "0";
    }
} else {
    echo "0";
}
?>
