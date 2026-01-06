<?php
/**
 * Verão Litorando Event - Standalone System
 * Summer Beach Event with 8 progressive enemies
 * All data self-contained in event tables
 * @author DBHeroes Team
 */

class Veraoexplorando {
    
    /**
     * Get player's event progress
     */
    private function getPlayerProgress($id_personagem) {
        $sql = "SELECT * FROM verao_progresso WHERE id_personagem = :id";
        $stmt = DB::prepare($sql);
        $stmt->bindParam(':id', $id_personagem, PDO::PARAM_INT);
        $stmt->execute();
        $progress = $stmt->fetch();
        
        // If player has no progress, create initial entry
        if(!$progress) {
            $sql = "INSERT INTO verao_progresso (id_personagem, ultimo_desbloqueado) VALUES (:id, 1)";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $id_personagem, PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the newly created progress
            $sql = "SELECT * FROM verao_progresso WHERE id_personagem = :id";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $id_personagem, PDO::PARAM_INT);
            $stmt->execute();
            $progress = $stmt->fetch();
        }
        
        return $progress;
    }
    
    /**
     * Display grid layout for event enemies (NEW METHOD)
     */
    public function getListGrid() {
        $core = new Core();
        $idPersonagem = $_SESSION['PERSONAGEMID'];
        
        $personagem_data = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        $energia_restante = intval($personagem_data->energia) - intval($personagem_data->energia_usada);
        
        $progress = $this->getPlayerProgress($idPersonagem);
        $ultimo_desbloqueado = $progress->ultimo_desbloqueado;
        
        $sql = "SELECT * FROM verao_guerreiros WHERE ativo = 1 ORDER BY ordem ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $guerreiros = $stmt->fetchAll();
        
        if(count($guerreiros) > 0) {
            foreach ($guerreiros as $guerreiro) {
                $fotoPath = !empty($guerreiro->foto) ? $guerreiro->foto : 'default.png';
                $ordem = $guerreiro->ordem;
                
                // ✅ MAP DATABASE ID TO NPC ID (1001-1008)
                $npc_id = 1000 + $ordem;
                
                $is_current = ($ordem == $ultimo_desbloqueado);
                $is_completed = ($ordem < $ultimo_desbloqueado);
                $is_locked = ($ordem > $ultimo_desbloqueado);
                
                // Determine card class
                $cardClass = 'warrior-card';
                if($is_current) $cardClass .= ' warrior-current';
                if($is_completed) $cardClass .= ' warrior-completed';
                if($is_locked) $cardClass .= ' warrior-locked';
                
                echo '<li class="'.$cardClass.'">';
                echo '<div class="order-badge">'.$ordem.'</div>';
                echo '<div class="warrior-image">';
                echo '<img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$guerreiro->nome.'">';
                echo '</div>';
                
                echo '<div class="warrior-content">';
                
                // Name (hide if far locked)
                $displayName = ($is_locked && $ordem > $ultimo_desbloqueado + 1) ? '???' : $guerreiro->nome;
                echo '<h3 class="warrior-name">'.$displayName.'</h3>';
                
                // Description
                if($is_locked && $ordem > $ultimo_desbloqueado + 1) {
                    echo '<p class="warrior-description">Continue derrotando os inimigos para desbloquear este guerreiro misterioso!</p>';
                } else {
                    echo '<p class="warrior-description">'.$guerreiro->descricao.'</p>';
                }
                
                // Stats (show if not far locked)
                if(!$is_locked || $ordem == $ultimo_desbloqueado + 1) {
                    echo '<div class="warrior-stats">';
                    echo '<div class="stat-item">Nível: <strong>'.$guerreiro->nivel.'</strong></div>';
                    echo '<div class="stat-item">HP: <strong>'.number_format(floatval($guerreiro->hp_max)).'</strong></div>';
                    echo '<div class="stat-item">Força: <strong>'.$guerreiro->forca.'</strong></div>';
                    echo '<div class="stat-item">Defesa: <strong>'.$guerreiro->defesa.'</strong></div>';
                    echo '</div>';
                }
                
                // Status Tag
                if($is_current) {
                    echo '<div class="status-tag tag-available">DISPONÍVEL</div>';
                } elseif($is_completed) {
                    echo '<div class="status-tag tag-completed">DERROTADO</div>';
                } else {
                    echo '<div class="status-tag tag-locked">BLOQUEADO</div>';
                }
                
                // Rewards (if not far locked)
                if(!$is_locked || $ordem == $ultimo_desbloqueado + 1) {
                    echo '<div class="rewards">';
                    echo '<div class="reward-item"><i class="fas fa-star"></i> '.number_format(floatval($guerreiro->exp_recompensa)).' EXP</div>';
                    echo '<div class="reward-item"><i class="fas fa-coins"></i> $'.number_format(floatval($guerreiro->dinheiro_recompensa)).'</div>';
                    echo '</div>';
                }
                
                // Action Button
                if($is_current) {
                    if($energia_restante >= 10) {
                        echo '<a href="'.BASE.'veraonpc/'.$npc_id.'" class="action-btn btn-attack">';
                        echo '<i class="fas fa-fist-raised"></i> ATACAR';
                        echo '</a>';
                    } else {
                        echo '<button class="action-btn btn-no-energy" onclick="showEnergyModal()">';
                        echo '<i class="fas fa-battery-empty"></i> SEM ENERGIA';
                        echo '</button>';
                    }
                } elseif($is_completed) {
                    echo '<button class="action-btn btn-completed">';
                    echo '<i class="fas fa-check-circle"></i> DERROTADO';
                    echo '</button>';
                } else {
                    echo '<button class="action-btn btn-locked">';
                    echo '<i class="fas fa-lock"></i> BLOQUEADO';
                    echo '</button>';
                }
                
                echo '</div>'; // warrior-content
                echo '</li>';
            }
        } else {
            echo '<li class="warrior-card">';
            echo '<div class="warrior-content">';
            echo '<h3 class="warrior-name">Evento não disponível</h3>';
            echo '<p class="warrior-description">O Verão Litorando ainda não começou!</p>';
            echo '</div>';
            echo '</li>';
        }
    }
    
    /**
     * Display the list of 8 event enemies (OLD LIST FORMAT)
     */
    public function getList() {
        $core = new Core();
        $idPersonagem = $_SESSION['PERSONAGEMID'];
        
        // Get player stats
        $personagem_data = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        $energia_restante = intval($personagem_data->energia) - intval($personagem_data->energia_usada);
        
        // Get player's event progress
        $progress = $this->getPlayerProgress($idPersonagem);
        $ultimo_desbloqueado = $progress->ultimo_desbloqueado;
        
        // Get all 8 event enemies in order - DIRECT FROM verao_guerreiros
        $sql = "SELECT * FROM verao_guerreiros WHERE ativo = 1 ORDER BY ordem ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $guerreiros = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $row = '';
        if(count($guerreiros) > 0) {
            foreach ($guerreiros as $key => $guerreiro) {
                $fotoPath = !empty($guerreiro->foto) ? $guerreiro->foto : 'default.png';
                $ordem = $guerreiro->ordem;
                
                // ✅ MAP DATABASE ID TO NPC ID (1001-1008)
                $npc_id = 1000 + $ordem;
                
                // Determine if this enemy is unlocked
                $is_unlocked = ($ordem <= $ultimo_desbloqueado);
                $is_current = ($ordem == $ultimo_desbloqueado);
                
                if($is_current) {
                    // CURRENT - Available to battle
                    if($energia_restante >= 10) {
                        $btnAtacar = '<a href="'.BASE.'veraonpc/'.$npc_id.'" class="bts-form btn-atacar"><i class="far fa-hand-rock"></i> Atacar</a>';
                    } else {
                        $btnAtacar = '<a href="javascript:void(0)" class="bts-form btn-sem-energia" onclick="showEnergiaPopup()"><i class="fas fa-battery-empty"></i> <span class="btn-text-small">Sem<br>Energia</span></a>';
                    }
                    
                    $row .= '<li class="guerreiro guerreiro-atual">';
                    $row .= '<div class="badge-ordem ordem-atual">'.$ordem.'</div>';
                    $row .= '<img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$guerreiro->nome.'" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'">';
                    $row .= '<div class="info">';
                    $row .= '<h3><strong>'.$guerreiro->nome.'</strong></h3>';
                    $row .= '<p class="descricao">'.$guerreiro->descricao.'</p>';
                    $row .= '<div class="stats-box">';
                    $row .= '<span class="stat">Nível: <strong>'.$guerreiro->nivel.'</strong></span>';
                    $row .= '<span class="stat">HP: <strong>'.$guerreiro->hp_max.'</strong></span>';
                    $row .= '<span class="stat">Força: <strong>'.$guerreiro->forca.'</strong></span>';
                    $row .= '<span class="stat">Defesa: <strong>'.$guerreiro->defesa.'</strong></span>';
                    $row .= '</div>';
                    $row .= '<span class="recompensas"><em class="tag-disponivel">DISPONÍVEL</em><br>';
                    $row .= '<i class="fas fa-star"></i> <strong>'.$guerreiro->exp_recompensa.' EXP</strong> ';
                    $row .= '<i class="fas fa-coins"></i> <strong>$'.$guerreiro->dinheiro_recompensa.'</strong>';
                    $row .= '</span>';
                    $row .= $btnAtacar;
                    $row .= '</div>';
                    $row .= '</li>';
                    
                } else if($is_unlocked && $ordem < $ultimo_desbloqueado) {
                    // COMPLETED - Already defeated
                    $row .= '<li class="guerreiro guerreiro-completo">';
                    $row .= '<div class="badge-ordem ordem-completo"><i class="fas fa-check"></i></div>';
                    $row .= '<img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$guerreiro->nome.'" class="img-completed" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'">';
                    $row .= '<div class="info">';
                    $row .= '<h3><strong>'.$guerreiro->nome.'</strong></h3>';
                    $row .= '<p class="descricao">Você já derrotou este inimigo!</p>';
                    $row .= '<div class="stats-box">';
                    $row .= '<span class="stat">Nível: <strong>'.$guerreiro->nivel.'</strong></span>';
                    $row .= '<span class="stat">HP: <strong>'.$guerreiro->hp_max.'</strong></span>';
                    $row .= '</div>';
                    $row .= '<span class="recompensas"><em class="tag-completo">✓ DERROTADO</em><br>';
                    $row .= '<i class="fas fa-trophy"></i> Recompensas já recebidas';
                    $row .= '</span>';
                    $row .= '</div>';
                    $row .= '</li>';
                    
                } else {
                    // LOCKED - Not yet available
                    $displayName = ($ordem > $ultimo_desbloqueado + 1) ? '???' : $guerreiro->nome;
                    $displayDesc = ($ordem > $ultimo_desbloqueado + 1) 
                        ? 'Derrote o desafio anterior para enfrentar este guerreiro!' 
                        : 'Continue derrotando os inimigos para desbloquear!';
                    
                    $row .= '<li class="guerreiro guerreiro-bloqueado">';
                    $row .= '<div class="badge-ordem ordem-locked"><i class="fas fa-lock"></i></div>';
                    $row .= '<img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="Bloqueado" class="img-locked" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'">';
                    $row .= '<div class="info">';
                    $row .= '<h3><strong>'.$displayName.'</strong></h3>';
                    $row .= '<p class="descricao">'.$displayDesc.'</p>';
                    $row .= '<div class="stats-box">';
                    $row .= '<span class="stat">Desafio: <strong>'.$ordem.'</strong></span>';
                    $row .= '<span class="stat"><em class="tag-bloqueado">BLOQUEADO</em></span>';
                    $row .= '</div>';
                    $row .= '<a href="javascript:void(0)" class="bts-form disabled"><i class="fas fa-lock"></i> Bloqueado</a>';
                    $row .= '</div>';
                    $row .= '</li>';
                }
            }
        } else {
            $row .= '<li class="guerreiro guerreiro-bloqueado">';
            $row .= '<div class="info">';
            $row .= '<h3>Evento não disponível</h3>';
            $row .= '<p>O Verão Litorando ainda não começou!</p>';
            $row .= '</div>';
            $row .= '</li>';
        }
        
        echo $row;
    }
    
    /**
     * Get enemy data by ID
     */
    public function getEnemyData($id_verao_guerreiro) {
        $sql = "SELECT * FROM verao_guerreiros WHERE id = :id AND ativo = 1";
        $stmt = DB::prepare($sql);
        $stmt->bindParam(':id', $id_verao_guerreiro, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Unlock next enemy when player wins
     */
    public function unlockNext($id_personagem) {
        $progress = $this->getPlayerProgress($id_personagem);
        $current = $progress->ultimo_desbloqueado;
        
        // Only unlock if not at max (8)
        if($current < 8) {
            $next = $current + 1;
            $sql = "UPDATE verao_progresso SET ultimo_desbloqueado = :next, vitorias = vitorias + 1 WHERE id_personagem = :id";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':next', $next, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id_personagem, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } else {
            // Event completed!
            $sql = "UPDATE verao_progresso SET vitorias = vitorias + 1, evento_completo = 1, data_conclusao = NOW() WHERE id_personagem = :id";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $id_personagem, PDO::PARAM_INT);
            $stmt->execute();
            return false;
        }
    }
    
    /**
     * Get player statistics for this event
     */
    public function getStats($id_personagem) {
        $progress = $this->getPlayerProgress($id_personagem);
        
        return array(
            'desbloqueados' => $progress->ultimo_desbloqueado - 1,
            'vitorias' => $progress->vitorias,
            'derrotas' => $progress->derrotas,
            'progresso' => (($progress->ultimo_desbloqueado - 1) / 8) * 100,
            'total_exp' => $progress->total_exp_ganho,
            'total_dinheiro' => $progress->total_dinheiro_ganho,
            'completo' => $progress->evento_completo
        );
    }
}
?>
