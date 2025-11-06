<?php
        session_start();
        require_once "../core/config.php";
        require_once "../core/DB.php";
        require_once "../core/Core.php";
        require_once "../core/Personagens.php";
        
        $core = new Core();
        $personagem = new Personagens();
        
        if(isset($_POST['id']) && $_POST['id'] != ''){
            $idPersonagem = intval($_POST['id']);
        
        // Get active mission
        $sql = "SELECT * FROM missoes WHERE idPersonagem = $idPersonagem AND status = 'ativa' ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $missao = $stmt->fetch();
            $tempo_restante = $missao->tempo_final - time();
            
            if($tempo_restante > 0){
                // Mission still running
                echo $tempo_restante;
            } else {
                // Mission finished - Use the EXACT same logic as getMissaoRunning()
                
                // Mark as completed
                $campos_missao = array(
                    'status' => 'concluida',
                    'data_conclusao' => date('Y-m-d H:i:s')
                );
                $where_missao = 'id = ' . $missao->id;
                $core->update('missoes', $campos_missao, $where_missao);
                
                // Get character data
                $sql_char = "SELECT * FROM usuarios_personagens WHERE id = " . $missao->idPersonagem;
                $stmt_char = DB::prepare($sql_char);
                $stmt_char->execute();
                $char_data = $stmt_char->fetch();
                
                // Get mission rewards from missoes_lista
                $sql_rewards = "SELECT * FROM missoes_lista WHERE id = " . $missao->idMissao;
                $stmt_rewards = DB::prepare($sql_rewards);
                $stmt_rewards->execute();
                
                if($stmt_rewards->rowCount() > 0){
                    $mission_data = $stmt_rewards->fetch();
                    $gold_ganho = intval($mission_data->recompensa_ouro ?? 100);
                    $exp_ganho = intval($mission_data->recompensa_exp ?? 50);
                } else {
                    $gold_ganho = 100;
                    $exp_ganho = 50;
                }
                
                // Update character gold and exp
                $novo_gold = intval($char_data->gold) + $gold_ganho;
                $novo_gold_total = intval($char_data->gold_total) + $gold_ganho;
                $novo_exp = intval($char_data->exp) + $exp_ganho;
                
                $campos_personagem = array(
                    'gold' => $novo_gold,
                    'gold_total' => $novo_gold_total,
                    'exp' => $novo_exp
                );
                $where_personagem = 'id = ' . $missao->idPersonagem;
                $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
                
                // ✅ THIS IS THE CRITICAL PART - Insert rewards notification
                $campos_valor = array(
                    'idPersonagem' => $missao->idPersonagem,
                    'gold' => $gold_ganho,
                    'exp' => $exp_ganho,
                    'tipo' => 'missao',
                    'visualizado' => 0
                );
                $core->insert('personagens_new_valores', $campos_valor);
                
                // Check for level up
                $personagem->checkLevelUp($missao->idPersonagem);
                
                // Clear session
                unset($_SESSION['missao']);
                unset($_SESSION['missao_id']);
                
                echo "0";
            }
        } else {
            echo "0";
        }
    } else {
        echo "0";
    }
?>
