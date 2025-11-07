<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invasao
 *
 * @author Felipe Faciroli
 */
class Invasao {
    public function getDadosInvasao(){
        $sql = "SELECT * FROM adm_invasao WHERE status = 1 LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $invasao_semana = $stmt->fetch();
        
        return $invasao_semana;
    }
    
    public function getInvasaoSemanal(){
        $sql = "SELECT * FROM adm_invasao WHERE status = 1 LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $invasao_semana = $stmt->fetch();
        
        // Check if invasion exists
        if(!$invasao_semana || !isset($invasao_semana->id)) {
            return null;
        }
        
        $sql = "SELECT * FROM adm_invasao_boss WHERE id = " . intval($invasao_semana->idBoss);
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $invasor = $stmt->fetch();
        $ativo = true; // Always active regardless of day

        
        // Merge invasion data with boss data
        if($invasor) {
            $invasor->invasion_id = $invasao_semana->id;
        }
        
        return $invasor;
    }
    
    public function dia_certo($dia_semana) {
        if (empty($dia_semana)) {
            return true; // If no specific day, allow all days
        }
        
        $dias = array(
            'domingo' => 0,
            'segunda' => 1,
            'terca' => 2,
            'quarta' => 3,
            'quinta' => 4,
            'sexta' => 5,
            'sabado' => 6
        );
        
        $hoje = date('w'); // 0 (Sunday) through 6 (Saturday)
        
        return (isset($dias[$dia_semana]) && $dias[$dia_semana] == $hoje);
    }
    
    public function getLogInvasao($idInvasor){
        $sql = "SELECT ai.*, up.nome as nome_personagem, at.nome as nome_ataque, i.nome as nome_invasor, atb.nome as nome_ataque_boss "
             . "FROM adm_invasao_ataques as ai "
             . "INNER JOIN adm_invasao_batalhas as ib ON ib.id = ai.idBatalha "
             . "INNER JOIN adm_invasao_boss as i ON i.id = ib.idInvasao "
             . "INNER JOIN usuarios_personagens as up ON up.id = ib.idPersonagem "
             . "INNER JOIN ataques as at ON at.id = ai.idGolpe "
             . "INNER JOIN ataques as atb ON atb.id = ai.idGolpeBoss "
             . "WHERE i.id = $idInvasor "
             . "ORDER BY round DESC LIMIT 50";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $log = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($log as $key => $value) {
            $row .= '<li>
                        <div class="dados">
                            <p><strong class="atacando">'.$value->nome_personagem.'</strong> atacou <strong class="boss">'.$value->nome_invasor.'</strong> com um <strong class="ataque">'.$value->nome_ataque.'</strong></p>
                            <span class="dano">Causou <strong class="causado">'.$value->dano_adversario.'</strong> de dano no Invasor</span>
                            <span class="dano_sofrido">Sofreu <strong class="sofrido">'.$value->dano_personagem.'</strong> de dano com um <strong class="ataque">'.$value->nome_ataque_boss.'</strong></span>
                        </div>
                        <div class="golpe">
                            <strong>'.$value->round.'º</strong>
                            <span>Golpe</span>
                        </div>
                     </li>';
        }
        
        return $row;
    }
    
    public function getMeuLog($idInvasor, $idPersonagem){
        $sql = "SELECT ai.*, up.nome as nome_personagem, at.nome as nome_ataque, i.nome as nome_invasor, atb.nome as nome_ataque_boss "
             . "FROM adm_invasao_ataques as ai "
             . "INNER JOIN adm_invasao_batalhas as ib ON ib.id = ai.idBatalha "
             . "INNER JOIN adm_invasao_boss as i ON i.id = ib.idInvasao "
             . "INNER JOIN usuarios_personagens as up ON up.id = ib.idPersonagem "
             . "INNER JOIN ataques as at ON at.id = ai.idGolpe "
             . "INNER JOIN ataques as atb ON atb.id = ai.idGolpeBoss "
             . "WHERE i.id = $idInvasor "
             . "AND ai.idPersonagem = $idPersonagem "
             . "ORDER BY round DESC LIMIT 50";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $log = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($log as $key => $value) {
            $row .= '<li>
                        <div class="dados">
                            <p><strong class="atacando">'.$value->nome_personagem.'</strong> atacou <strong class="boss">'.$value->nome_invasor.'</strong> com um <strong class="ataque">'.$value->nome_ataque.'</strong></p>
                            <span class="dano">Causou <strong class="causado">'.$value->dano_adversario.'</strong> de dano no Invasor</span>
                            <span class="dano_sofrido">Sofreu <strong class="sofrido">'.$value->dano_personagem.'</strong> de dano com um <strong class="ataque">'.$value->nome_ataque_boss.'</strong></span>
                        </div>
                        <div class="golpe">
                            <strong>'.$value->round.'º</strong>
                            <span>Golpe</span>
                        </div>
                     </li>';
        }
        
        return $row;
    }
    
    public function getRecompensas($idInvasao){
        $row = '';
        
        $sql = "SELECT * FROM adm_invasao_gold WHERE idInvasao = $idInvasao";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $gold = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $row .= '<div class="recompensas recompensa-gold">
                        <img src="'.BASE.'assets/icones/gold.png" />
                        <span>'.$gold->gold.'</span>
                     </div>';
        }
        
        $sql = "SELECT ai.*, i.imagem as foto, i.nome "
            . "FROM adm_invasao_item as ai "
            . "INNER JOIN itens as i ON i.id = ai.idItem "
            . "WHERE ai.idInvasao = $idInvasao";

        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $row .= '<div class="recompensas recompensa-item">
                        <img src="'.BASE.'assets/itens/'.$item->foto.'" />
                        <span>'.$item->nome.'</span>
                     </div>';
        }
        
        $sql = "SELECT * FROM adm_invasao_foto WHERE idInvasao = $idInvasao";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $foto = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $row .= '<div class="recompensas recompensa-foto">
                        <img src="'.BASE.'assets/cards/'.$foto->foto.'" />
                        <span>Foto de Perfil</span>
                     </div>';
        }
        
        return $row;
    }
    
    public function getAtaques($graduacao, $ki, $level, $idPersonagem){
        $core = new Core();
        
        $dadosPersonagem = $core->getDados('usuarios_personagens', "WHERE id = ".$idPersonagem);
        $ki_restante = intval($ki - $dadosPersonagem->ki_usado);
        
        $sql = "SELECT * FROM personagens_golpes WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $golpes_personagem = $stmt->fetchAll();
        
        $lista_ataques = array();
        
        foreach ($golpes_personagem as $key => $value) {
            array_push($lista_ataques, $value->idGolpe);
        }
        
        $row = '';
        
        if(count($lista_ataques) > 0){
            $sql = "SELECT * FROM ataques WHERE graduacao <= $graduacao AND id in(".implode(",", array_map('intval', $lista_ataques)).") ORDER BY ki ASC";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $itens = $stmt->fetchAll();

            foreach ($itens as $key2 => $value2) {
                $inativo = '';
                $estado = 1;
                $disabled = '';

                if($ki_restante < $value2->ki){
                    $inativo = 'inativo';
                    $estado = 0;
                    $disabled = 'disabled';
                }

                if($level < $value2->level){
                    $inativo = 'inativo';
                    $estado = 0;
                    $disabled = 'disabled';
                }

                $row .= '<li dataid="'.$value2->id.'" class="'.$inativo.'">
                            <input type="submit" '.$disabled.' dataid="'.$value2->id.'" dataestado="'.$estado.'" class="bt-atacar" '.$disabled.' value="" style="background-image: url('.BASE.'assets/ataques/'.$value2->imagem.');" />
                            <div class="info">
                                <h3>'.$value2->nome.'</h3>
                                <p>'.$value2->descricao.'</p>
                                <span class="ataque"><strong>Ataque: </strong> +'.$value2->dano.'</span>
                                <span class="level"><strong>Level Necesário: </strong> '.$value2->level.'</span>
                                <span class="consome"><strong>Consome </strong> '.$value2->ki.' <strong>de KI</strong></span>
                            </div>
                         </li>';
            }
        }
        
        return $row;
    }
    
    public function atacar($idPersonagem, $idInvasor, $idGolpe, $idBatalha){
        $core = new Core();
        $equipes = new Equipes();
        $inventario = new Inventario();
        
        $config = $core->getConfiguracoes();
        
        $esgotado = 0;
        
        $data = date('Y-m-d H:i:s');
        
        $dadosBoss = $core->getDados('adm_invasao_boss', "WHERE id = ".$idInvasor);
        $dadosPersonagem = $core->getDados('usuarios_personagens', "WHERE id = ".$idPersonagem);
        $dadosAtaqueInvasor = $core->getDados('adm_invasao_ataques', "WHERE idBatalha = $idBatalha ORDER BY id DESC LIMIT 1");
        
        if($dadosPersonagem->hp > 0){
            $tempo = $dadosAtaqueInvasor->time_ataque;

            if($dadosAtaqueInvasor){
                if(!$this->verificaDanoHP($dadosPersonagem->hp)){
                    $esgotado = 1;
                }
            }

            if($esgotado == 0){
                $sql = "SELECT count(*) as total FROM adm_invasao_ataques";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $dadosLog = $stmt->fetch();

                //STATUS EXTRA DAS EQUIPES
                $status_extra = intval($equipes->getStatusExtra($idPersonagem));
                $status_extra_graduacao = intval($core->getStatusGraduacao($dadosPersonagem->graduacao));

                $status_equipados = $inventario->getStatusEquipados($idPersonagem);

                $forca_equipados = intval($status_equipados['forca']);
                $agilidade_equipados = intval($status_equipados['agilidade']);
                $habilidade_equipados = intval($status_equipados['habilidade']);
                $resistencia_equipados = intval($status_equipados['resistencia']);
                $sorte_equipados = intval($status_equipados['sorte']);

                $dados_ataque = $core->getDados('ataques', 'WHERE id = '.$idGolpe);

                $bonus = rand(0, 5);

                // ✅ FIXED: Changed $dados_atacante to $dadosPersonagem
                $forca = intval($dadosPersonagem->forca) + $status_extra + $status_extra_graduacao + $forca_equipados;
                $defesa = intval($dadosPersonagem->resistencia) + $status_extra + $status_extra_graduacao + $resistencia_equipados;
                $agilidade = intval($dadosPersonagem->agilidade) + $status_extra + $status_extra_graduacao + $agilidade_equipados;
                $habilidade = intval($dadosPersonagem->habilidade) + $status_extra + $status_extra_graduacao + $habilidade_equipados;

                $calc_agilidade = $agilidade / 100;
                $calc_habilidade = $habilidade / 100;

                $dano = ((($forca * (($forca * 100) / $dadosBoss->resistencia)) / 100) / 4) + intval($bonus);
                $dano_final = intval($dano) + intval($dados_ataque->dano);

                $bonus_boss = rand(0, 5);
                $ataques_rand = rand(0, 10);

                if($ataques_rand < 8){
                    $dados_ataque_boss = $core->getDados('ataques', "WHERE id = 4");
                } else {
                    $dados_ataque_boss = $core->getDados('ataques', "ORDER BY RAND()");
                }

                $forca_boss = intval($forca) + intval((intval($forca) * (50 / 100)));
                $defesa_boss = intval($defesa) + intval((intval($defesa) * (50 / 100)));
                $agilidade_boss = intval($agilidade) + intval((intval($agilidade) * (50 / 100)));
                $habilidade_boss = intval($habilidade) + intval((intval($habilidade) * (50 / 100)));

                $calc_agilidade_boss = $agilidade_boss / 100;
                $calc_habilidade_boss = $habilidade_boss / 100;

                $dano_boss = ((($forca_boss * (($forca_boss * 100) / $defesa)) / 100) / 4) + intval($bonus_boss);
                $dano_final_boss = intval($dano_boss) + intval($dados_ataque_boss->dano);

                $campos_up_ki = array(
                    'ki_usado' => intval($dadosPersonagem->ki_usado) + $dados_ataque->ki
                );

                $where_up_ki = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_up_ki, $where_up_ki);

                $campos_boss = array(
                    'hp_usado' => intval($dadosBoss->hp_usado) + intval($dano_final)
                );

                $where_boss = 'id = "'.$idInvasor.'"';

                $core->update('adm_invasao_boss', $campos_boss, $where_boss);

                $round = intval($dadosLog->total) + 1;
                
                if($config->teste == 1){
                    $tempo_atualiza = time() + 10;
                } else {
                    $tempo_atualiza = time() + 600;
                }

                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'idBatalha' => $idBatalha,
                    'idGolpe' => $idGolpe,
                    'idGolpeBoss' => $dados_ataque_boss->id,
                    'dano_personagem' => $dano_final_boss,
                    'dano_adversario' => $dano_final,
                    'ki_usado' => $dados_ataque->ki,
                    'data' => $data,
                    'round' => $round,
                    'time_ataque' => $tempo_atualiza
                );

                $core->insert('adm_invasao_ataques', $campos);
                
                if($this->getDerrotado($idInvasor)){
                    $this->premiaVencedor($dadosPersonagem->idUsuario, $idPersonagem, $idInvasor);
                }
                
                $hp_restante = $dadosPersonagem->hp - $dano_final_boss;
                
                if($hp_restante <= 0){
                    $hp_atualiza = 0;
                } else {
                    $hp_atualiza = $dadosPersonagem->hp - $dano_final_boss;
                }
                
                $campos_personagem = array(
                    'hp' => $hp_atualiza
                );

                $where_personagem = 'id = '.$dadosPersonagem->id;

                $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
                
                $this->drop($dadosPersonagem->id, $dadosPersonagem->nivel);
            }
        }
    }
    
    public function premiaVencedor($idUsuario, $idPersonagem, $idInvasor){
        $core = new Core();
        $inventario = new Inventario();
        
        $existe_gold = 0;
        $existe_item = 0;
        $existe_foto = 0;
        
        $sql = "SELECT * FROM adm_invasao_gold WHERE idInvasao = $idInvasor";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $gold = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $existe_gold = 1;
        }
        
        $sql = "SELECT * FROM adm_invasao_item WHERE idInvasao = $idInvasor";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $existe_item = 1;
        }
        
        $sql = "SELECT * FROM adm_invasao_foto WHERE idInvasao = $idInvasor";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $foto = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $existe_foto = 1;
        }
        
        if($existe_item == 1){
            $sql = "SELECT * FROM itens WHERE id = $item->idItem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dadosItem = $stmt->fetch();

            if($inventario->verificaItemIgual($dadosItem->nome, $idPersonagem)){
                $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $idPersonagem);
            }

            $campos_add_item = array(
                'idItem' => $dadosItem->id,
                'idSlot' => $slot_recebido,
                'idPersonagem' => $idPersonagem
            );

            $core->insert('personagens_inventario_itens', $campos_add_item);
            
            $conteudo = '<p>Parabéns, você ganhou o item '.$dadosItem->nome.', confira em seu inventário!</p>';
            $core->setNotification($conteudo, 'sucesso', $idPersonagem, 'inventario');
        }
        
        if($existe_foto == 1){
            if(!$core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$foto->foto."' AND id = ".$idPersonagem)){
                $campos_add_foto = array(
                    'idUsuario' => $idUsuario,
                    'idPersonagem' => $idPersonagem,
                    'foto' => $foto->foto,
                    'visualizado' => 0
                );

                $core->insert('usuarios_personagens_fotos', $campos_add_foto);
            } else {
                $conteudo = '<p>Você já possui esta foto em sua coleção de fotos!</p>';
                $core->setNotification($conteudo, 'erro', $idPersonagem, 'portal');
            }
        }
        
        if($existe_gold == 1){
            $dadosPersonagem = $core->getDados('usuarios_personagens', "WHERE id = ".$idPersonagem);
            
            $campos = array(
                'gold' => intval($dadosPersonagem->gold) + intval($gold->gold),
                'gold_total' => intval($dadosPersonagem->gold_faturado) + intval($gold->gold)
            );

            $where = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $campos, $where);
            
            $conteudo = '<p>Parabéns, você ganhou '.$gold->gold.' golds!</p>';
            $core->setNotification($conteudo, 'sucesso', $idPersonagem, 'portal');
        }
        
        $campos = array(
            'vencedor' => $idPersonagem
        );

        $where = 'id = "'.$idInvasor.'"';

        $core->update('adm_invasao_boss', $campos, $where);
    }

    public function getDerrotado($idInvasor){
        $sql = "SELECT * FROM adm_invasao_boss WHERE id = $idInvasor";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item && $item->hp_usado >= $item->hp_total){  // ✅ Changed hp to hp_total
            return true;
        } else {
            return false;
        }
    }

    
    public function getBatalhaRunning($idInvasor, $idPersonagem){
        $sql = "SELECT * FROM adm_invasao_batalhas WHERE idInvasao = $idInvasor AND idPersonagem = $idPersonagem AND finalizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getInfoBatalhaRunning($idInvasor, $idPersonagem){
        $sql = "SELECT * FROM adm_invasao_batalhas WHERE idInvasao = $idInvasor AND idPersonagem = $idPersonagem AND finalizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getExistsAtaque($idBatalha){
        $sql = "SELECT * FROM adm_invasao_ataques WHERE idBatalha = $idBatalha";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getExistsLastBatalha($idInvasor, $idPersonagem){
        $sql = "SELECT * FROM adm_invasao_batalhas WHERE idInvasao = $idInvasor AND idPersonagem = $idPersonagem AND finalizado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaDanoHP($hp){
        if($hp > 10){
            return true;
        } else {
            return false;
        }
    }
    
    public function drop($idPersonagem, $level){
        $inventario = new Inventario();
        $core = new Core();
        
        $sql = "SELECT * FROM itens WHERE bau = 1 AND graduacao_inicial <= $level AND (id != 42 && id != 49)  ORDER BY graduacao_final DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $market = $stmt->fetch();
        
        $sorteio = $this->getSorteioBau();
        
        if($sorteio == 1 && $market){
            if($inventario->verificaItemIgual($market->nome, $idPersonagem)){
                $slot_recebido = $inventario->verificaItemIgual($market->nome, $idPersonagem);
            }

            $campos = array(
                'novo' => 1
            );

            $where = 'id = "'.$slot_recebido.'"';

            $core->update('personagens_inventario', $campos, $where);

            $campos_add = array(
                'idItem' => $market->id,
                'idSlot' => $slot_recebido,
                'idPersonagem' => $idPersonagem
            );

            $core->insert('personagens_inventario_itens', $campos_add);
            
            $conteudo = '<p>Parabéns, você ganhou o item '.$market->nome.', confira em seu inventário!</p>';
            $core->setNotification($conteudo, 'sucesso', $idPersonagem, 'invasao/boss');
        }
    }
    
    public function getSorteioBau(){
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $numeros = array(2, 3, 7, 10, 1, 4, 5, 6, 18, 23, 20, 35, 40);
 
        $qtdNumeros = sizeof($numeros);
         
        // Sorteando
        $sorteado[1] = $numeros[rand(0,$qtdNumeros - 1)];
        $randon = rand(1, 100);

        $total =  $randon - $sorteado[1];

        if($total <= 0){
            return 1;
        } else {
            return 0;
        }
    }
}
