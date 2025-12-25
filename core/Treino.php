<?php

/**
 * Description of Treino
 *
 * @author Felipe Faciroli
 */
class Treino {
    
    public function getValoresTreino($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_treino WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $atributo = $stmt->fetch();
        
        return $atributo;
    }
    
    public function newUpGolpes($level, $idPersonagem){
        $core = new Core();
        $batalha = new Batalha();
        
        $sql_golpes = "SELECT * FROM ataques WHERE level <= $level";
        $stmt = DB::prepare($sql_golpes);
        $stmt->execute();
        $lista_golpes = $stmt->fetchAll();

        foreach ($lista_golpes as $key => $value) {
            if(!$batalha->getGolpeExiste($value->id, $idPersonagem)){
                $campos_golpe = array(
                    'idGolpe' => $value->id,
                    'idPersonagem' => $idPersonagem
                );

                $core->insert('personagens_golpes', $campos_golpe);
            }
        }
    }
    
    public function newUpGraduation($nova_graduacao, $graduacao, $idPersonagem){
        $core = new Core();
        $inventario = new Inventario();
        
        if($nova_graduacao > $graduacao){
            $_SESSION['new_graduation'] = true;

            //RECEBE BAÚ DA GRADUAÇÃO
            $sql = "SELECT * FROM itens WHERE bau = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $baus = $stmt->fetchAll();

            foreach ($baus as $key => $value) {
                if($nova_graduacao >= $value->graduacao_inicial && $nova_graduacao <= $value->graduacao_final){
                    $bau_escolhido = $value->nome;

                    if($inventario->verificaItemIgual($bau_escolhido, $idPersonagem)){
                        $slot_recebido = $inventario->verificaItemIgual($bau_escolhido, $idPersonagem);

                        $campos_i = array(
                            'novo' => 1
                        );

                        $where_i = 'id = "'.$slot_recebido.'"';

                        $core->update('personagens_inventario', $campos_i, $where_i);

                        $campos_add = array(
                            'idItem' => $value->id,
                            'idSlot' => $slot_recebido,
                            'idPersonagem' => $idPersonagem
                        );

                        $core->insert('personagens_inventario_itens', $campos_add);

                        $campos_premio = array(
                            'idMissao' => 0,
                            'idItem' => $value->id,
                            'idPersonagem' => $idPersonagem,
                            'visualizado' => 0   
                        );

                        $core->insert('personagens_missoes_premios', $campos_premio);
                    }
                }
            }
        }
    }
    
    public function getPorcentageExp($idPersonagem, $nivel, $exp, $hp, $ki, $graduacao){
        $core = new Core();
        $inventario = new Inventario();
        $personagem = new Personagens();
        
        $config = $core->getConfiguracoes();
        
        if($nivel == $config->level_maximo){
            $level = $config->level_maximo;
        } else {
            $level = $nivel + 1;
        }
        
        $sql = "SELECT * FROM level WHERE level = $level";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        $dados_personagem = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        
        if($level <= $config->level_maximo){
            if($exp >= $item->exp){
                $hp_level = 50;
                $valor_recovery_hp = intval($level) * intval($hp_level);
                
                $_SESSION['new_nivel'] = 'Parabéns você avançou do level <strong>'.$nivel.'</strong> para o level <strong>'.$level.'</strong>!';
                
                $nova_graduacao = $personagem->getGraduacaoNumber($level);
                
                // ✅ Calculate new max values
                $hp_max_novo = intval($valor_recovery_hp) + 50;
                $ki_max_novo = intval($ki) + 50;
                
                // ✅ Preserve current HP and KI usage from battle
                $current_hp = intval($dados_personagem->hp);
                $current_ki_usado = intval($dados_personagem->ki_usado);
                
                // ✅ Only restore HP if player is dead (HP <= 0), otherwise keep current damaged HP
                $new_hp = ($current_hp <= 0) ? $hp_max_novo : $current_hp;
                
                $campos = array(
                    'nivel' => $level,
                    'graduacao' => $nova_graduacao,
                    'hp' => $new_hp, // ✅ Preserve battle damage (or revive if dead)
                    'mana' => $ki_max_novo, // ✅ Increase max KI
                    'ki_usado' => $current_ki_usado, // ✅ Preserve KI usage from battle
                    'energia_usada' => 0, // ✅ Reset energy (OK for battles)
                    'pontos' => intval($dados_personagem->pontos) + 1,
                    'forca' => intval($dados_personagem->forca) + 1,
                    'agilidade' => intval($dados_personagem->agilidade) + 1,
                    'habilidade' => intval($dados_personagem->habilidade) + 1,
                    'resistencia' => intval($dados_personagem->resistencia) + 1,
                    'sorte' => intval($dados_personagem->sorte) + 1
                );

                $where = "id = ".$idPersonagem;

                $core->update('usuarios_personagens', $campos, $where);
                
                
                //ADICIONA OS GOLPES DO LEVEL
                $this->newUpGolpes($level, $idPersonagem);
                
                //VERIFICA SE EXISTE NOVA GRADUAÇÃO
                $this->newUpGraduation($nova_graduacao, $graduacao, $idPersonagem);
            }
        }
        
        $du = $core->getDados('usuarios_personagens', 'WHERE id ='.$idPersonagem);
        
        $exp_anterior = $core->getDados('level', 'WHERE level ='.$du->nivel);
        
        $exp_alcancada = intval($du->exp) - intval($exp_anterior->exp);
        
        $exp_nova = intval($item->exp) - intval($exp_anterior->exp);
        
        $total = intval($exp_alcancada) / $exp_nova;
        
        $resultado = intval($total * 100);

        return $resultado;
    }
    
    public function getExpRestante($idPersonagem){
        $core = new Core();
        
        $dados_guerreiro = $core->getDados('usuarios_personagens', 'WHERE id ='.$idPersonagem);
        
        $prox_level = $dados_guerreiro->nivel + 1;
        
        $exp_anterior = $core->getDados('level', 'WHERE level ='.$dados_guerreiro->nivel);
        $exp_nova = $core->getDados('level', 'WHERE level ='.$prox_level);
        
        $exp_alcancada = intval($dados_guerreiro->exp) - intval($exp_anterior->exp);
        $exp_a_alcancar = intval($exp_nova->exp) - intval($exp_anterior->exp);
        
        $total = $exp_a_alcancar - $exp_alcancada;
        
        return $total;
    }
    
    public function viewNewLevel($idPersonagem, $nivel, $exp){
        $core = new Core();
        
        $config = $core->getConfiguracoes();
        
        if($nivel == $config->level_maximo){
            $level = $config->level_maximo;
        } else {
            $level = $nivel + 1;
        }
        
        $sql = "SELECT * FROM level WHERE level = $level";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        $dados_personagem = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        
        if($level <= $config->level_maximo){
            if($exp >= $item->exp){
                $_SESSION['new_nivel'] = 'Parabéns você avançou do level <strong>'.$nivel.'</strong> para o level <strong>'.$level.'</strong>!';
            }
        }
    }
    
    public function getPorcentagemKI($ki, $ki_usado){
        // Safety check
        if($ki == null || $ki <= 0){
            return 0;
        }
        
        if($ki_usado == null || $ki_usado < 0){
            $ki_usado = 0;
        }
        
        $ki_restante = $ki - $ki_usado;
        if($ki_restante < 0){
            $ki_restante = 0;
        }
        
        $total = $ki_restante / intval($ki);
        
        $resultado = intval($total * 100);

        return $resultado;
    }
    
    public function getPorcentagemEnergia($energia, $energia_usada){
        // Safety check
        if($energia == null || $energia <= 0){
            return 0;
        }
        
        if($energia_usada == null || $energia_usada < 0){
            $energia_usada = 0;
        }
        
        $energia_restante = $energia - $energia_usada;
        if($energia_restante < 0){
            $energia_restante = 0;
        }
        
        $total = $energia_restante / intval($energia);
        
        $resultado = intval($total * 100);

        return $resultado;
    }
    
    public function getPorcentagemHP($hp, $hp_max){
        // Safety check to prevent division by zero
        if($hp_max == null || $hp_max <= 0){
            return 0;
        }
        
        if($hp == null || $hp < 0){
            $hp = 0;
        }
        
        // Ensure HP doesn't exceed max
        if($hp > $hp_max){
            $hp = $hp_max;
        }
        
        $total = $hp / intval($hp_max);
        
        $resultado = intval($total * 100);

        return $resultado;
    }
    
    public function getProximoNivel($nivel){
        $core = new Core();
        
        $config = $core->getConfiguracoes();
        
        if($nivel == $config->level_maximo){
            $level = $config->level_maximo;
        } else {
            $level = $nivel + 1;
        }
        $sql = "SELECT * FROM level WHERE level = $level";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        return $item->exp;
    }
    
    public function getPorcentagemForca($forca, $agilidade, $habilidade, $resistencia, $sorte){
        
        $soma = $forca + $agilidade + $habilidade + $resistencia + $sorte;
        
        if($soma == 0){
            $soma = 1;
        }
        
        $total = $forca / intval($soma);
        $resultado = intval($total * 100);
        
        return $resultado;
    }
    
    public function getPorcentagemAgilidade($forca, $agilidade, $habilidade, $resistencia, $sorte){
        
        $soma = $forca + $agilidade + $habilidade + $resistencia + $sorte;
        
        if($soma == 0){
            $soma = 1;
        }
        
        $total = $agilidade / intval($soma);
        $resultado = intval($total * 100);
        
        return $resultado;
    }
    
    public function getPorcentagemHabilidade($forca, $agilidade, $habilidade, $resistencia, $sorte){
        
        $soma = $forca + $agilidade + $habilidade + $resistencia + $sorte;
        
        if($soma == 0){
            $soma = 1;
        }
        
        $total = $habilidade / intval($soma);
        $resultado = intval($total * 100);
        
        return $resultado;
    }
    
    public function getPorcentagemResistencia($forca, $agilidade, $habilidade, $resistencia, $sorte){
        
        $soma = $forca + $agilidade + $habilidade + $resistencia + $sorte;
        
        if($soma == 0){
            $soma = 1;
        }
        
        $total = $resistencia / intval($soma);
        $resultado = intval($total * 100);
        
        return $resultado;
    }
    
    public function getPorcentagemSorte($forca, $agilidade, $habilidade, $resistencia, $sorte){
        
        $soma = $forca + $agilidade + $habilidade + $resistencia + $sorte;
        
        if($soma == 0){
            $soma = 1;
        }
        
        $total = $sorte / intval($soma);
        $resultado = intval($total * 100);
        
        return $resultado;
    }
    
    public function setCorBarra($porcentagem){
        if($porcentagem < 30){
            echo 'red';
        } else if($porcentagem > 30 && $porcentagem < 70){
            echo 'orange';
        } else {
            echo '';
        }
    }

    public function usarPontos($idPersonagem, $atr, $pontos_usados){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $atributo_usuario = $stmt->fetch();

        $campo_user = intval($atributo_usuario->$atr) + $pontos_usados;
        $pontos_user = intval($atributo_usuario->pontos) - $pontos_usados;

        $campos_usuario = array(
            $atr => $campo_user,
            'pontos' => $pontos_user
        );

        $where_usuario = 'id = "'.$idPersonagem.'"';

        if($core->update('usuarios_personagens', $campos_usuario, $where_usuario)){
            return true;
        } else {
            return false;
        }   
    }
    
    public function treinarGuerreiro($idPersonagem, $item, $debitar, $unidades, $ultimoValor){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_treino WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $atributo = $stmt->fetch();
        
        $campo_item = intval($ultimoValor);
        
        $campos = array(
            $item => $campo_item + 99
        );
        
        $where = 'idPersonagem = "'.$idPersonagem.'"';
        
        if($core->update('personagens_treino', $campos, $where)){
            $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $atributo_usuario = $stmt->fetch();

            $campo_user = intval($atributo_usuario->$item) + $unidades;
            $golds_user = intval($atributo_usuario->gold) - $debitar;

            $campos_usuario = array(
                $item => $campo_user,
                'gold' => $golds_user
            );

            $where_usuario = 'id = "'.$idPersonagem.'"';

            if($core->update('usuarios_personagens', $campos_usuario, $where_usuario)){
                return true;
            }
        } else {
            return false;
        }
    }
    
    /**
     * ✅ ENERGIA regenerates every 10 seconds
     */
    public function recoveryEnergia($idPersonagem, $vip){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $p = $stmt->fetch();
        
        $energia_max = intval($p->energia);
        $energia_usada = intval($p->energia_usada);
        $time_stamina = intval($p->time_stamina);
        $now = time();
        
        // Initialize timestamp if zero
        if($time_stamina == 0){
            $core->update('usuarios_personagens',
                array('time_stamina' => $now),
                'id = '.$idPersonagem);
            return;
        }
        
        // Check if enough time has passed (10 seconds)
        $elapsed = $now - $time_stamina;
        if($elapsed < 10) return; // ✅ Changed from 5 to 10
        
        // Regenerate if needed
        if($energia_usada > 0){
            $regen = ceil($energia_max * 0.03); // 3% per cycle
            $ciclos = floor($elapsed / 10); // ✅ Changed from 5 to 10
            $total_regen = $ciclos * $regen;
            
            $novo_valor = max(0, $energia_usada - $total_regen);
            
            $core->update('usuarios_personagens',
                array(
                    'energia_usada' => $novo_valor,
                    'time_stamina' => $now
                ),
                'id = '.$idPersonagem);
        }
    }

    /**
     * ✅ KI regenerates every 10 seconds
     */
    public function recoveryKI($idPersonagem, $vip){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $p = $stmt->fetch();
        
        $ki_max = intval($p->mana);
        $ki_usado = intval($p->ki_usado);
        $time_ki = intval($p->time_ki);
        $now = time();
        
        // Initialize timestamp if zero
        if($time_ki == 0){
            $core->update('usuarios_personagens',
                array('time_ki' => $now),
                'id = '.$idPersonagem);
            return;
        }
        
        // Check if enough time has passed (10 seconds)
        $elapsed = $now - $time_ki;
        if($elapsed < 10) return; // Already correct
        
        // Regenerate if needed
        if($ki_usado > 0){
            $regen = ceil($ki_max * 0.10); // 10% per cycle
            $ciclos = floor($elapsed / 10); // Already correct
            $total_regen = $ciclos * $regen;
            
            $novo_valor = max(0, $ki_usado - $total_regen);
            
            $core->update('usuarios_personagens',
                array(
                    'ki_usado' => $novo_valor,
                    'time_ki' => $now
                ),
                'id = '.$idPersonagem);
        }
    }

    /**
     * ✅ HP regenerates every 10 seconds
     */
    public function recoveryHP($idPersonagem, $vip){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $p = $stmt->fetch();
        
        $hp_level = 50;
        $level = intval($p->nivel);
        $hp_max = ($level * $hp_level) + 100;
        $hp_atual = intval($p->hp);
        $time_hp = intval($p->time_hp);
        $now = time();
        
        // Initialize timestamp if zero
        if($time_hp == 0){
            $core->update('usuarios_personagens',
                array('time_hp' => $now),
                'id = '.$idPersonagem);
            return;
        }
        
        // Check if enough time has passed (10 seconds)
        $elapsed = $now - $time_hp;
        if($elapsed < 10) return; // Already correct
        
        // Regenerate if needed
        if($hp_atual < $hp_max){
            $regen = ceil($hp_max * 0.10); // 10% per cycle
            $ciclos = floor($elapsed / 10); // Already correct
            $total_regen = $ciclos * $regen;
            
            $novo_hp = min($hp_max, $hp_atual + $total_regen);
            
            $core->update('usuarios_personagens',
                array(
                    'hp' => $novo_hp,
                    'time_hp' => $now
                ),
                'id = '.$idPersonagem);
        }
    }

    
    public function getListBonus($dia, $idPersonagem){
        $sql = "SELECT * FROM adm_recompensas";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();
        
        $row = '';

        foreach ($itens as $key => $value) {
            
            $hoje = '';
            $img = '';
            $conteudo = '';
            $disabled = '';
            $txtDia = '';
            $coletado = '';
            $button = '<input type="submit" '.$disabled.' name="coletar" value="Coletar" />';
            
            if($dia == $value->dia_semana){
                $hoje = 'atual';
            } else {
                $disabled = 'disabled';
                $button = '<input type="submit" '.$disabled.' name="coletar" value="Indisponível" />';
            }
            
            if($this->verifyBonusColetado($idPersonagem, $value->dia_semana)){
                $coletado = 'coletado';
                $disabled = 'disabled';
                $hoje = '';
                $button = '<input type="submit" '.$disabled.' name="coletar" value="Coletado" />';
            }
            
            if($value->dia_semana == 'domingo'){
                $txtDia = 'Domingo';
            } else if($value->dia_semana == 'segunda'){
                $txtDia = 'Segunda';
            } else if($value->dia_semana == 'terca'){
                $txtDia = 'Terça';
            } else if($value->dia_semana == 'quarta'){
                $txtDia = 'Quarta';
            } else if($value->dia_semana == 'quinta'){
                $txtDia = 'Quinta';
            } else if($value->dia_semana == 'sexta'){
                $txtDia = 'Sexta';
            } else if($value->dia_semana == 'sabado'){
                $txtDia = 'Sábado';
            }
            
            if($value->premio == 'gold'){
                $img = BASE.'assets/icones/gold.png';
                $conteudo = '<h3>Receba '.$value->valor.' Golds</h3>';
            } else if($value->premio == 'item'){
                $sql = "SELECT * FROM itens WHERE id = $value->valor";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $produto = $stmt->fetch();
                
                $img = BASE.'assets/'.$produto->foto;
                $conteudo = '<h3>Receba o item '.$produto->nome.'</h3>';
            }
            
            $row .= '<li class="'.$hoje.' '.$coletado.'">';
                $row .= '<h2>'.$txtDia.'</h2>';
                $row .= '<form action="" method="post">';
                    $row .= '<img src="'.$img.'" />';
                    $row .= $conteudo;
                    $row .= '<input type="hidden" name="id" value="'.$value->id.'" />';
                    $row .= $button;
                $row .= '</form>';
            $row .= '</li>';
        }
        
        echo $row;
    }
    
    public function verifyBonusColetado($idPersonagem, $dia_semana){
        $core = new Core();
        
        if($dia_semana == 'domingo'){
            $dia = 1;
        } else if($dia_semana == 'segunda'){
            $dia = 2;
        } else if($dia_semana == 'terca'){
            $dia = 3;
        } else if($dia_semana == 'quarta'){
            $dia = 4;
        } else if($dia_semana == 'quinta'){
            $dia = 5;
        } else if($dia_semana == 'sexta'){
            $dia = 6;
        } else if($dia_semana == 'sabado'){
            $dia = 7;
        }
        
        $datas_semana = $core->getSemanaAtual($dia);
        
        $sql = "SELECT * FROM personagens_recompensas WHERE data = '$datas_semana' AND idPersonagem = $idPersonagem ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
}
