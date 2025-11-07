<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
                
                $campos = array(
                    'nivel' => $level,
                    'graduacao' => $nova_graduacao,
                    'hp' => intval($valor_recovery_hp) + 50,
                    'mana' => intval($ki) + 50,
                    'ki_usado' => 0,
                    'energia_usada' => 0,
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
        $resultado = intval($total * 98);

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
        $resultado = intval($total * 98);

        return $resultado;
    }

    
    public function getPorcentagemHP($hp, $hp_usado){
        // Safety check to prevent division by zero
        if($hp == null || $hp <= 0){
            return 0;
        }
        
        if($hp_usado == null || $hp_usado < 0){
            $hp_usado = 0;
        }
        
        $hp_restante = $hp - $hp_usado;
        
        // Additional check: ensure hp_restante is not negative
        if($hp_restante < 0){
            $hp_restante = 0;
        }
        
        $total = $hp_restante / intval($hp);
        $resultado = intval($total * 98);

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
    
    public function recoveryEnergia($idPersonagem, $vip){
        $core = new Core();
        $personagem = new Personagens();
        
        $personagem->getGuerreiro($idPersonagem);

        $energia = ($personagem->energia) * (3 / 100);
        $energia_usada = $personagem->energia_usada;
        
        if($vip == 1){
            $minutos = 1;
        } else {
            $minutos = 2;
        }
        
        $segundos_energia = $minutos * 60;
        
        $time_stamina = $personagem->time_stamina;
        $tempo_decorrido = time() - $time_stamina;
        
        $total = floor(($tempo_decorrido / $segundos_energia) * $energia);
        
        if($energia_usada > 0){
            if($total >= 1){
                if($personagem->energia_usada >= $total){
                    $up_guerreiro = array(
                        'energia_usada' => intval($personagem->energia_usada) - $total,
                        'time_stamina' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                } else {
                    $up_guerreiro = array(
                        'energia_usada' => 0,
                        'time_stamina' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                }
            }
        } else {
            $campos = array(
                'time_stamina' => time()
            );

            $where = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $campos, $where);
        }
    }
    
    public function recoveryKI($idPersonagem, $vip){
        $core = new Core();
        $personagem = new Personagens();
        
        $personagem->getGuerreiro($idPersonagem);

        $ki = ($personagem->mana) * (10 / 100);
        $ki_usado = $personagem->ki_usado;
        
        if($vip == 1){
            $minutos = 1;
        } else {
            $minutos = 2;
        }
        
        $segundos_ki = $minutos * 60;
        
        $time_ki = $personagem->time_ki;
        $tempo_decorrido = time() - $time_ki;
        
        $total = floor(($tempo_decorrido / $segundos_ki) * $ki);
        
        if($ki_usado > 0){
            if($total >= 1){
                if($personagem->ki_usado >= $total){                    
                    $up_guerreiro = array(
                        'ki_usado' => intval($personagem->ki_usado) - $total,
                        'time_ki' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                } else {
                    $up_guerreiro = array(
                        'ki_usado' => 0,
                        'time_ki' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                }
            }
        } else {
            $campos = array(
                'time_ki' => time()
            );

            $where = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $campos, $where);
        }
    }
    
    public function recoveryHP($idPersonagem, $vip){
        $core = new Core();
        $personagem = new Personagens();
        
        $personagem->getGuerreiro($idPersonagem);

        $hp_level = 50;

        $hp = $personagem->hp;
        $level = $personagem->nivel;

        $valor_hp = ($level * $hp_level) + 100;

        $hp_usado = $valor_hp - $hp;
        
        $hp_porcentagem = $valor_hp * (10 / 100);
        
        if($vip == 1){
            $minutos = 5;
        } else {
            $minutos = 10;
        }
        
        $segundos_hp = $minutos * 60;
        
        $time_hp = $personagem->time_hp;
        $tempo_decorrido = time() - $time_hp;
        
        $total = floor(($tempo_decorrido / $segundos_hp) * $hp_porcentagem);
        
        if($hp_usado > 0){
            if($total >= 1){
                if($hp_usado >= $total){
                    $up_guerreiro = array(
                        'hp' => intval($personagem->hp) + $total,
                        'time_hp' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                } else {
                    $up_guerreiro = array(
                        'hp' => $valor_hp,
                        'time_hp' => time()
                    );

                    $where_guerreiro = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
                }
            }
        } else {
            $campos = array(
                'time_hp' => time()
            );

            $where = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $campos, $where);
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
