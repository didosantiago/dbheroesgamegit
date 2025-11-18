<?php

/**
 * Description of Torneio
 *
 * @author Felipe Faciroli
 */
class Torneio {
    public function getList($nivel){
        // Get current character's energy
        $core = new Core();
        $idPersonagem = $_SESSION['PERSONAGEMID'];
        $personagem_data = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        $energia_restante = intval($personagem_data->energia) - intval($personagem_data->energia_usada);
        
        // Calculate level range: -1, current, +1
        $level_min = max(1, $nivel - 1);
        $level_max = $nivel + 1;
        
        // Get ONE NPC per level
        $sql = "SELECT MIN(g.id) as id, g.nivel 
                FROM guerreiros_arena as g 
                WHERE g.nivel >= $level_min AND g.nivel <= $level_max 
                GROUP BY g.nivel 
                ORDER BY g.nivel ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        $lista_guerreiros = array();
        
        foreach ($item as $key => $value) {
            array_push($lista_guerreiros, $value->id);
        }
        
        if(count($lista_guerreiros) > 0){
            $sql = "SELECT g.*, g.id as idguerreiro, 
                    p.foto as foto,
                    p.nome as nome,
                    g.nivel, g.exp, g.idGuerreiro
                FROM guerreiros_arena as g 
                LEFT JOIN guerreiros as p ON p.id = g.idGuerreiro 
                WHERE g.id IN(".implode(",", array_map('intval', $lista_guerreiros)).") 
                ORDER BY g.nivel ASC";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $guerreiros = $stmt->fetchAll();

            foreach ($guerreiros as $key2 => $value2) {
                $fotoPath = !empty($value2->foto) ? $value2->foto : 'default.png';
                
                if($value2->nivel < $nivel) {
                    // FRACO - Previous level
                    if($energia_restante >= 10) {
                        $btnAtacar = '<a href="'.BASE.'npc/'.$value2->idguerreiro.'" class="bts-form"><i class="far fa-hand-rock"></i> Atacar</a>';
                    } else {
                        $btnAtacar = '<a href="javascript:void(0);" class="bts-form btn-sem-energia" onclick="showEnergiaPopup()">
                                        <i class="fas fa-battery-empty"></i> 
                                        <span class="btn-text-small">Sem<br/>Energia</span>
                                      </a>';
                    }
                    
                    $row .= '<li class="guerreiro guerreiro-fraco">
                                <img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$value2->nome.'" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'" />
                                <div class="info">
                                    <h3><strong>'.$value2->nome.'</strong> está pronto para a batalha</h3>
                                    <span class="nivel">Nível: <strong>'.$value2->nivel.'</strong> <em class="tag-fraco">(FRACO)</em></span>
                                    <span class="exp">Exp: <strong>'.$value2->exp.'</strong></span>
                                    '.$btnAtacar.'
                                </div>
                             </li>';
                             
                } else if($value2->nivel == $nivel) {
                    // ATUAL - Current level
                    if($energia_restante >= 10) {
                        $btnAtacar = '<a href="'.BASE.'npc/'.$value2->idguerreiro.'" class="bts-form"><i class="far fa-hand-rock"></i> Atacar</a>';
                    } else {
                        $btnAtacar = '<a href="javascript:void(0);" class="bts-form btn-sem-energia" onclick="showEnergiaPopup()">
                                        <i class="fas fa-battery-empty"></i> 
                                        <span class="btn-text-small">Sem<br/>Energia</span>
                                      </a>';
                    }
                    
                    $row .= '<li class="guerreiro">
                                <img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$value2->nome.'" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'" />
                                <div class="info">
                                    <h3><strong>'.$value2->nome.'</strong> está pronto para a batalha</h3>
                                    <span class="nivel">Nível: <strong>'.$value2->nivel.'</strong> <em class="tag-atual">(ATUAL)</em></span>
                                    <span class="exp">Exp: <strong>'.$value2->exp.'</strong></span>
                                    '.$btnAtacar.'
                                </div>
                             </li>';
                             
                } else {
                    // BLOQUEADO - Next level
                    $row .= '<li class="guerreiro guerreiro-bloqueado">
                                <img src="'.BASE.'assets/guerreiros/'.$fotoPath.'" alt="'.$value2->nome.'" onerror="this.src=\''.BASE.'assets/guerreiros/default.png\'" />
                                <div class="info">
                                    <h3><strong>'.$value2->nome.'</strong> ainda não está liberado</h3>
                                    <span class="nivel">Nível: <strong>'.$value2->nivel.'</strong> <em class="tag-bloqueado">(BLOQUEADO)</em></span>
                                    <span class="exp">Exp: <strong>'.$value2->exp.'</strong></span>
                                    <a href="javascript:void(0);" class="bts-form disabled"><i class="fas fa-lock"></i> Bloqueado</a>
                                </div>
                             </li>';
                }
            }
        } else {
            $row .= '<li class="guerreiro guerreiro-bloqueado">
                        <div class="info">
                            <h3>Nenhum oponente disponível no momento</h3>
                            <p>Continue treinando para desbloquear novos desafios!</p>
                        </div>
                     </li>';
        }
        
        echo $row;
    }
}
?>