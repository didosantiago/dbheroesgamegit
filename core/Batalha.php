<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Batalha
 *
 * @author Felipe Faciroli
 */
class Batalha {
    public function getAtaques($graduacao, $ki, $level, $idPersonagem, $inimigo){
        $sql = "SELECT * FROM personagens_golpes WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $golpes_personagem = $stmt->fetchAll();
        
        $lista_ataques = array();
        
        foreach ($golpes_personagem as $key => $value) {
            array_push($lista_ataques, $value->idGolpe);
        }
        
        $row = '';
        
        // ✅ FIX: Check if player has any attacks before querying
        if(empty($lista_ataques)){
            return '<li class="inativo">
                        <div class="info">
                            <h3>Nenhum ataque disponível</h3>
                            <p>Você precisa aprender ataques primeiro em Golpes.</p>
                        </div>
                    </li>';
        }
        
        $sql = "SELECT * FROM ataques WHERE graduacao <= $graduacao AND id in(".implode(",", array_map('intval', $lista_ataques)).") ORDER BY ki ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();

        foreach ($itens as $key2 => $value2) {
            $inativo = '';
            $estado = 1;
            $disabled = '';

            if($ki < $value2->ki){
                $inativo = 'inativo';
                $estado = 0;
                $disabled = 'disabled';
            }

            if($level < $value2->level){
                $inativo = 'inativo';
                $estado = 0;
                $disabled = 'disabled';
            }

            if($inimigo == 1){
                $nameButton = 'notAtaque';
                $disabled = 'disabled';
            } else {
                $nameButton = 'atacar';
                $disabled = '';
            }

            $row .= '<li dataid="'.$value2->id.'" class="'.$inativo.'">
                        <form id="Ataque'.$value2->id.'" class="form-ataque" method="post">
                            <input type="hidden" name="idAtack" value="'.$value2->id.'" />
                            <input type="hidden" name="estado" value="'.$estado.'" />
                            <input type="submit" '.$disabled.' name="'.$nameButton.'" class="bt-atacar" '.$disabled.' value="" style="background-image: url('.BASE.'assets/ataques/'.$value2->imagem.');" />
                        </form>
                        <div class="info">
                            <h3>'.$value2->nome.'</h3>
                            <p>'.$value2->descricao.'</p>
                            <span class="ataque"><strong>Ataque: </strong> +'.$value2->dano.'</span>
                            <span class="level"><strong>Level Necesário: </strong> '.$value2->level.'</span>
                            <span class="consome"><strong>Consome </strong> '.$value2->ki.' <strong>de KI</strong></span>
                        </div>
                    </li>';
        }
        
        return $row;
    }
    
    public function atack($idAtack, $meuID, $idAdversario, $desafiante, $finalizado = 0){
        $core = new Core();
        $equipes = new Equipes();
        $inventario = new Inventario();
        
        if($desafiante == 0){
            //STATUS EXTRA DAS EQUIPES
            $status_extra = intval($equipes->getStatusExtra($meuID));
        
            $dados_atacante = $core->getDados('usuarios_personagens', 'WHERE id = '.$meuID);
            $dados_atacado = $core->getDados('usuarios_personagens', 'WHERE id = '.$idAdversario);
            
            $status_extra_graduacao = intval($core->getStatusGraduacao($dados_atacante->graduacao));
            
            $sql = "SELECT * FROM pvp WHERE idPersonagem = $idAdversario AND idDesafiado = $meuID AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_pvp = $stmt->fetch();
            
            $sql = "SELECT sum(ki_usado) as total FROM pvp_historico WHERE idPVP = $dados_pvp->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_confronto = $stmt->fetch();
            
            $ataque_sorteado = rand(1, 20);
            
            if($core->isExists('ataques', "WHERE id = ".$ataque_sorteado)){
                if($dados_confronto->total != null){
                    $dados_ataque = $core->getDados('ataques', 'WHERE id = '.$ataque_sorteado);
                    $total_ki_restante = $dados_atacante->mana - $dados_confronto->total;
                } else {
                    $dados_ataque = $core->getDados('ataques', 'WHERE id = 4');
                    $total_ki_restante = $dados_atacante->mana;
                }
                
                if($total_ki_restante >= $dados_ataque->ki){
                    $ataques_liberado = 1;
                } else {
                    $ataques_liberado = 0;
                }
                
                if($ataques_liberado == 1){
                    if($dados_atacante->nivel > $dados_ataque->level){
                        $ki_usado_pvp = $dados_ataque->ki;
                    } else {
                        $dados_ataque = $core->getDados('ataques', 'WHERE id = 4');
                    }
                } else {
                    $dados_ataque = $core->getDados('ataques', 'WHERE id = 4');
                }
            } else {
                $dados_ataque = $core->getDados('ataques', 'WHERE id = 4');
            }
        } else {
            //STATUS EXTRA DAS EQUIPES
            $status_extra = intval($equipes->getStatusExtra($meuID));
        
            $dados_atacado = $core->getDados('usuarios_personagens', 'WHERE id = '.$idAdversario);
            $dados_atacante = $core->getDados('usuarios_personagens', 'WHERE id = '.$meuID);
            $dados_ataque = $core->getDados('ataques', 'WHERE id = '.$idAtack);
            
            $status_extra_graduacao = intval($core->getStatusGraduacao($dados_atacante->graduacao));
        }
        
        $forca_equipados = 0;
        $agilidade_equipados = 0;
        $habilidade_equipados = 0;
        $resistencia_equipados = 0;
        $sorte_equipados = 0;
        
        if($desafiante == 0){
            $dados_pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$idAdversario.' AND idDesafiado = '.$meuID.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
            $status_equipados = $inventario->getStatusEquipados($meuID);
        } else {
            $dados_pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$meuID.' AND idDesafiado = '.$idAdversario.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
            $status_equipados = $inventario->getStatusEquipados($meuID);
        }
        
        $forca_equipados = intval($status_equipados['forca']);
        $agilidade_equipados = intval($status_equipados['agilidade']);
        $habilidade_equipados = intval($status_equipados['habilidade']);
        $resistencia_equipados = intval($status_equipados['resistencia']);
        $sorte_equipados = intval($status_equipados['sorte']);
        
        if($desafiante == 0){
            $sql = "SELECT * FROM pvp WHERE idPersonagem = $idAdversario AND idDesafiado = $meuID AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_pvp = $stmt->fetch();
        } else {
            $sql = "SELECT * FROM pvp WHERE idPersonagem = $meuID AND idDesafiado = $idAdversario AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_pvp = $stmt->fetch();

            $status_equipados = $inventario->getStatusEquipados($meuID);
    
            $forca_equipados = intval($status_equipados['forca']);
            $agilidade_equipados = intval($status_equipados['agilidade']);
            $habilidade_equipados = intval($status_equipados['habilidade']);
            $resistencia_equipados = intval($status_equipados['resistencia']);
            $sorte_equipados = intval($status_equipados['sorte']);
        }
        
        $bonus = rand(0, 5);
        
        $forca = intval($dados_atacante->forca) + $status_extra + $status_extra_graduacao + $forca_equipados;
        $defesa = intval($dados_atacante->resistencia) + $status_extra + $status_extra_graduacao + $resistencia_equipados;
        $agilidade = intval($dados_atacante->agilidade) + $status_extra + $status_extra_graduacao + $agilidade_equipados;
        $habilidade = intval($dados_atacante->habilidade) + $status_extra + $status_extra_graduacao + $habilidade_equipados;
        
        $calc_agilidade = $agilidade / 100;
        $calc_habilidade = $habilidade / 100;
        
        $dano_critico = 0;
        
        if($core->sortearPorcentagem($calc_agilidade)) {
            $desviou = 1;
        } else {
            $desviou = 0;
        }
        
        if($core->sortearPorcentagem($calc_habilidade)) {
            $critico = 1;
            $dano_critico = $habilidade;
        } else {
            $critico = 0;
        }
        
        $defesaCalculada = $this->calculaResistenciaExtra($idAdversario);
        $defesa_atacado = $dados_atacado->resistencia + $defesaCalculada;
        
        //CALCULA O DANO CAUSADO
        if($desafiante == 0){
            if($desviou == 0){
                $dano = ((($forca * (($forca * 100) / $defesa_atacado)) / 100) / 4) + intval($bonus);
                $dano_final = intval($dano) + intval($dados_ataque->dano) + intval($dano_critico);
            } else {
                $dano_final = 0;
            }
        } else {
            if($desviou == 0){
                $dano = ((($forca * (($forca * 100) / $defesa_atacado)) / 100) / 4) + intval($bonus);
                $dano_final = intval($dano) + intval($dados_ataque->dano) + intval($dano_critico);
            } else {
                $dano_final = 0;
            }
        }
        
        //DESCONTA DO KI
        if($dados_pvp->atacou == 0){
            $campos_up_ki = array(
                'ki_usado' => intval($dados_atacante->ki_usado) + $dados_ataque->ki
            );

            $where_up_ki = 'id = "'.$meuID.'"';

            $core->update('usuarios_personagens', $campos_up_ki, $where_up_ki);
        }
        
        if($dados_pvp->atacou == 0){
            $dados_history = $core->getDados('pvp_historico', "WHERE idPVP = $dados_pvp->id ORDER BY id DESC LIMIT 1");
            $round = $dados_history->round + 1;
        } else {
            $dados_history = $core->getDados('pvp_historico', "WHERE idPVP = $dados_pvp->id ORDER BY id DESC LIMIT 1");
            $round = $dados_history->round;
        }
        
        //ADICIONA AO HISTÓRICO
        if($desafiante == 1){
            if($desviou == 0){
                if($critico == 1){
                    $class_critico = 'critico';
                    $acertou_critico = ' de dano crítico';
                } else {
                    $class_critico = '';
                    $acertou_critico = ' de dano';
                }
                $campos = array(
                    'idPVP' => $dados_pvp->id,
                    'dano_adversario' => $dano_final,
                    'round' => $round,
                    'log' => "<div class='line atacante ".$class_critico."'>"
                                . "<div class='description'>"
                                    . "<strong>".$dados_atacante->nome."</strong> atacou <strong>".$dados_atacado->nome."</strong> com um <strong>".$dados_ataque->nome."</strong> e causou <strong>".$dano_final."</strong> ".$acertou_critico.". "
                                . "</div>"
                                . "<em class='round'>Round <strong>".$round."</strong></em>"
                           . "</div>"
                );
            } else {
                $campos = array(
                    'idPVP' => $dados_pvp->id,
                    'dano_adversario' => $dano_final,
                    'round' => $round,
                    'log' => "<div class='line desviou'>"
                                . "<div class='description'>"
                                    . "<strong>".$dados_atacado->nome."</strong> desviou do <strong>".$dados_ataque->nome."</strong> de <strong>".$dados_atacante->nome."</strong> e não sofreu dano "
                                . "</div>"
                                . "<em class='round'>Round <strong>".$round."</strong></em>"
                           . "</div>"
                );
            }
        } else {
            if($desviou == 0){
                if($critico == 1){
                    $class_critico = 'critico';
                    $acertou_critico = ' de dano crítico';
                } else {
                    $class_critico = '';
                    $acertou_critico = ' de dano';
                }
                
                $campos = array(
                    'idPVP' => $dados_pvp->id,
                    'dano_personagem' => $dano_final,
                    'ki_usado' => $ki_usado_pvp,
                    'round' => $round,
                    'log' => "<div class='line atacado ".$class_critico."'>"
                                ."<div class='description'>"
                                    . "<strong>".$dados_atacante->nome."</strong> atacou <strong>".$dados_atacado->nome."</strong> com um <strong>".$dados_ataque->nome."</strong> e causou <strong>".$dano_final."</strong> ".$acertou_critico.". "
                                . "</div>"
                                . "<em class='round'>Round <strong>".$round."</strong></em>"
                           . "</div>"
                );
            } else {
                $campos = array(
                    'idPVP' => $dados_pvp->id,
                    'dano_personagem' => $dano_final,
                    'ki_usado' => $ki_usado_pvp,
                    'round' => $round,
                    'log' => "<div class='line desviou'>"
                                . "<div class='description'>"
                                    . "<strong>".$dados_atacado->nome."</strong> desviou do <strong>".$dados_ataque->nome."</strong> de <strong>".$dados_atacante->nome."</strong> e não sofreu dano "
                                . "</div>"
                                . "<em class='round'>Round <strong>".$round."</strong></em>"
                            . "</div>"
                );
            }
        }

        $core->insert('pvp_historico', $campos);
        
        //ATUALIZA O TEMPO FINAL DO ROUND
        $contador = time() + 30;
        
        if($desafiante == 0){
            $campos_pvp = array(
                'atacou' => 0,
                'atacado' => 1,
                'time_final' => $contador
            );

            $where_pvp = 'id = "'.$dados_pvp->id.'"';

            $core->update('pvp', $campos_pvp, $where_pvp);
        } else {
            $campos_pvp = array(
                'atacou' => 1,
                'atacado' => 0,
                'time_final' => $contador
            );

            $where_pvp = 'id = "'.$dados_pvp->id.'"';

            $core->update('pvp', $campos_pvp, $where_pvp);
        }
        
        
        if($desafiante == 0){
            $dados_pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$idAdversario.' AND idDesafiado = '.$meuID.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
        } else {
            $dados_pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$meuID.' AND idDesafiado = '.$idAdversario.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
        }
        
        $lifes = $this->getLifeRestante($dados_pvp->id);
        $kis = $this->getKiRestante($dados_pvp->id);
        
        if($desafiante == 0){
            $life_oponente = $dados_atacante->hp - $lifes->dano_atacante;
            $ki_oponente = $dados_atacante->ki - $kis->ki_pvp;
        } else {
            $life_oponente = $dados_atacado->hp - $lifes->dano_atacado;
        }
        
        //ADVERSARIO ATACA NOVAMENTE
        if($dados_pvp->atacou == 1 && $dados_pvp->atacado == 0){
            if($life_oponente <= 0){
                $finalizado = 1;
            }
            
            if($finalizado == 0){
                $this->atack(4, $idAdversario, $meuID, 0);

                $campos_pvp = array(
                    'atacou' => 0,
                    'atacado' => 1
                );

                $where_pvp = 'id = "'.$dados_pvp->id.'"';

                $core->update('pvp', $campos_pvp, $where_pvp);

                $_SESSION['pvp_atacado'] = true;
            }
            
        }
    }
    
    public function getAtacado($idGuerreiro){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idGuerreiro";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_desafiado = $stmt->fetch();
        
        if($dados_desafiado->time_defesa > time()){
            return true;
        } else {
            return false;
        }
    }
    
    public function getAtacouRecente($idPersonagem, $idAtacando){
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND idDesafiado = $idAtacando AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() <= 0){
            $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_desafiado = $stmt->fetch();

            if($dados_desafiado->time_ataque > time()){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function playerAtacadoDAY($idPersonagem, $idOponente){
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND idDesafiado = $idOponente AND data = CURDATE() AND concluido = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function saveBatalha($idPersonagem, $idOponente){
        $core = new Core();
        
        $tempo_atual = time();
        $tempo_final = time() + 30;
        
        $campos = array(
            'idPersonagem' => $idPersonagem,
            'idDesafiado' => $idOponente,
            'time_inicial' => $tempo_atual,
            'time_final' => $tempo_final,
            'data' => date('Y-m-d'),
            'concluido' => 0,
            'vencedor' => 0
        );

        $core->insert('pvp', $campos);

        $tempo_defesa = time() + 1800;

        $campos_desafiado = array(
            'time_defesa' => $tempo_defesa
        );

        $where_desafiado = 'id = "'.$idOponente.'"';

        $core->update('usuarios_personagens', $campos_desafiado, $where_desafiado);
        
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_desafiador = $stmt->fetch();
        
        $tempo_ataque = time() + 600;
        
        $campos_desafiador = array(
            'time_ataque' => $tempo_ataque,
            'gold' => intval($dados_desafiador->gold) - 15
        );

        $where_desafiador = 'id = "'.$idPersonagem.'"';

        $core->update('usuarios_personagens', $campos_desafiador, $where_desafiador);
        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_pvp = $stmt->fetch();
        
        $_SESSION['pvp'] = true;
        $_SESSION['pvp_id'] = $dados_pvp->id;
    }
    
    public function getLogPVP($idPersonagem, $idGuerreiro){
        $sql = "SELECT ph.* "
             . "FROM pvp_historico as ph "
             . "INNER JOIN pvp as p ON ph.idPVP = p.id "
             . "WHERE p.idPersonagem = $idPersonagem AND p.idDesafiado = $idGuerreiro "
             . "AND p.concluido = 0 "
             . "ORDER BY ph.id DESC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';

        foreach ($item as $key => $value) {
            $row .= $value->log;
        }
        
        return $row;
    }
    
    public function pvpRun($idPersonagem, $idGuerreiro){
        $core = new Core();
        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND idDesafiado = $idGuerreiro AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_pvp = $stmt->fetch();
            $_SESSION['pvp'] = true;
            $_SESSION['pvp_id'] = $dados_pvp->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function pvpExecuting($idPersonagem){
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_pvp = $stmt->fetch();
            $_SESSION['pvp'] = true;
            $_SESSION['pvp_id'] = $dados_pvp->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function pvpEsgotado($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND concluido = 0 AND pausado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        $count = $stmt->rowCount();
        
        if($count > 0){
            $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_personagem = $stmt->fetch();

            $sql = "SELECT * FROM usuarios_personagens WHERE id = $item->idDesafiado";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $oponente = $stmt->fetch();

            if($item->time_pausado > time()){
                $gold_recebido = intval((intval($dados_personagem->gold) * 10) / 100);
                
                $campos_adv = array(
                    'vitorias_pvp' => intval($oponente->vitorias_pvp) + 1,
                    'gold' => intval($oponente->gold) + $gold_recebido,
                    'gold_total' => intval($oponente->gold_total) + $gold_recebido
                );

                $where_adv = 'id = "'.$oponente->id.'"';

                $core->update('usuarios_personagens', $campos_adv, $where_adv);
            
                $campos_usuario = array(
                    'derrotas_pvp' => intval($dados_personagem->derrotas_pvp) + 1,
                    'gold' => intval($dados_personagem->gold) - $gold_recebido
                );

                $where_usuario = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_usuario, $where_usuario);

                $campos_adversario = array(
                    'vitorias_pvp' => intval($oponente->vitorias_pvp) + 1,
                    'gold' => intval($oponente->gold) + $gold_recebido,
                    'gold_total' => intval($oponente->gold_total) + $gold_recebido
                );

                $where_adversario = 'id = "'.$oponente->id.'"';

                $core->update('usuarios_personagens', $campos_adversario, $where_adversario);

                $campos_pvp = array(
                    'vencedor' => 0,
                    'concluido' => 1,
                    'pausado' => 0
                );

                $where_pvp = 'id = "'.$item->id.'"';

                $core->update('pvp', $campos_pvp, $where_pvp);
                
                unset($_SESSION['atacado']);
                unset($_SESSION['pvp']);
                unset($_SESSION['pvp_id']);
                unset($_SESSION['pvp_vitoria']);
                unset($_SESSION['pvp_derrota']);
                unset($_SESSION['pvp_desafiador']);
            }
        }
    }
    
    public function pvpPausado($idPersonagem){        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND pausado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_pvp = $stmt->fetch();
            $_SESSION['pvp'] = true;
            $_SESSION['pvp_id'] = $dados_pvp->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaBatalhaRun($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM pvp WHERE idDesafiado = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getHistorico($idPersonagem, $pc, $qtd_resultados){
        $core = new Core();

        $status = '';
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("pvp", "WHERE concluido = 1 AND (idDesafiado = $idPersonagem) OR (idPersonagem = $idPersonagem)");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pv.*, "
                . "up.nome as nome_desafiado, "
                . "up.id as id_desafiado, "
                . "upd.nome as nome_guerreiro, "
                . "up.id as id_guerreiro "
             . "FROM pvp as pv "
             . "INNER JOIN usuarios_personagens as up ON up.id = pv.idDesafiado "
             . "INNER JOIN usuarios_personagens as upd ON upd.id = pv.idPersonagem "
             . "WHERE pv.concluido = 1 "
             . "AND (pv.idDesafiado = $idPersonagem) "
             . "OR (pv.idPersonagem = $idPersonagem) "
             . "ORDER BY pv.id DESC LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $total_ataquei = $stmt->rowCount();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($total_ataquei > 0){
            foreach ($item as $key => $value) {
                
                $ganhou = '';

                if($value->vencedor == 1 && $value->idPersonagem == $idPersonagem){
                    $ganhou = 'vitoria';
                    $status = $value->nome_guerreiro;
                } else if($value->vencedor == 0 && $value->idPersonagem == $idPersonagem){
                    $ganhou = 'derrota';
                    $status = $value->nome_desafiado;
                } else if($value->vencedor == 1 && $value->idPersonagem != $idPersonagem) {
                    $ganhou = 'derrota';
                    $status = $value->nome_guerreiro;
                } else if($value->vencedor == 0 && $value->idPersonagem != $idPersonagem) {
                    $ganhou = 'vitoria';
                    $status = $value->nome_desafiado;
                }
                
                $row .= '<tr class="'.$ganhou.'">
                            <td><a href="'.BASE.'publico/'.$value->id_guerreiro.'">'.$value->nome_guerreiro.'</a></td>
                            <td><a href="'.BASE.'publico/'.$value->id_desafiado.'">'.$value->nome_desafiado.'</a></td>
                            <td>'.$core->dataBR($value->data).'</td>
                            <td>'.$status.'</td>
                            <td>
                                <a href="'.BASE.'log/'.$value->id.'">
                                    <i class="far fa-eye"></i>
                                </a>
                            </td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="5" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="5" style="test-align: center;">Nenhuma batalha efetuada ainda.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getHistoricoBatalha($idPVP, $pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("pvp_historico", "WHERE idPVP = $idPVP");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM pvp_historico WHERE idPVP = $idPVP LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= $value->log;
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="9" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
            $row .= '<p class="atacante">Nenhum Log Gerado para esta batalha</p>';
        }
        
        echo $row;
    }

    public function getGuerreiroAtacado($idPVP){
        $sql = "SELECT * FROM pvp WHERE id = $idPVP";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->atacado == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function printConfronto($idAdversario, $idPersonagem, $life, $life_oponente, $ki, $vip, $ki_oponente){
        $core = new Core();
        $personagem = new Personagens();
        $treino = new Treino();
        $batalha = new Batalha();
        $equipes = new Equipes();
        $inventario = new Inventario();
        
        $dados_atacado = $core->getDados('usuarios_personagens', 'WHERE id = '.$idAdversario);
        $dados_atacante = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        
        $status_equipados_atacante = $inventario->getStatusEquipados($dados_atacante->id);
        $status_equipados_atacado = $inventario->getStatusEquipados($dados_atacado->id);
        
        $status_extra_atacante = intval($equipes->getStatusExtra($dados_atacante->id));
        $status_extra_atacado = intval($equipes->getStatusExtra($dados_atacado->id));
        
        $status_extra_graduacao_atacante = intval($core->getStatusGraduacao($dados_atacante->graduacao));
        $status_extra_graduacao_atacado = intval($core->getStatusGraduacao($dados_atacado->graduacao));
        
        $forca_equipados_atacante = intval($dados_atacante->forca) + intval($status_equipados_atacante['forca']) + $status_extra_atacante + $status_extra_graduacao_atacante;
        $agilidade_equipados_atacante = intval($dados_atacante->agilidade) + intval($status_equipados_atacante['agilidade']) + $status_extra_atacante + $status_extra_graduacao_atacante;
        $habilidade_equipados_atacante = intval($dados_atacante->habilidade) + intval($status_equipados_atacante['habilidade']) + $status_extra_atacante + $status_extra_graduacao_atacante;
        $resistencia_equipados_atacante = intval($dados_atacante->resistencia) + intval($status_equipados_atacante['resistencia']) + $status_extra_atacante + $status_extra_graduacao_atacante;
        
        $forca_equipados_atacado = intval($dados_atacado->forca) + intval($status_equipados_atacado['forca']) + $status_extra_atacado + $status_extra_graduacao_atacado;
        $agilidade_equipados_atacado = intval($dados_atacado->agilidade) + intval($status_equipados_atacado['agilidade']) + $status_extra_atacado + $status_extra_graduacao_atacado;
        $habilidade_equipados_atacado = intval($dados_atacado->habilidade) + intval($status_equipados_atacado['habilidade']) + $status_extra_atacado + $status_extra_graduacao_atacado;
        $resistencia_equipados_atacado = intval($dados_atacado->resistencia) + intval($status_equipados_atacado['resistencia']) + $status_extra_atacado + $status_extra_graduacao_atacado;
        
        $energia = $dados_atacante->energia - $dados_atacante->energia_usada;
        
        $foto = str_replace('cards/', '', $dados_atacante->foto);
        
        $graduacao_atacante = $personagem->getGraduacaoBatalha($dados_atacante->nivel);
        $graduacao_texto_atacante = $personagem->getGraduacaoTextoBatalha($dados_atacante->nivel);
        $graduacao_atacado = $personagem->getGraduacaoBatalha($dados_atacado->nivel);
        $graduacao_texto_atacado = $personagem->getGraduacaoTextoBatalha($dados_atacado->nivel);
        
        $equipamentos_atacante = $personagem->getEquipamentos($idPersonagem);
        $equipamentos_atacado = $personagem->getEquipamentos($idAdversario);
        
        $ki_restante = $ki - $dados_atacante->ki_usado;
        $ki_restante_atacado = $dados_atacado->mana - $dados_atacado->ki_usado;
        
        $log = $this->getLogPVP($idPersonagem, $idAdversario);
        
        if($vip == 1){
            $porcentagemVip = (40 / 100) * intval($dados_atacado->hp);
            $hp_pvp = $dados_atacado->hp - $porcentagemVip;
        } else {
            $porcentagemFree = (20 / 100) * intval($dados_atacado->hp);
            $hp_pvp = $dados_atacado->hp - $porcentagemFree;
        }
        
        $ki_oponente_restante = $dados_atacado->mana - $ki_oponente;

        $row = '';
        
        $row .= '
                <div class="guerreiros-batalha">
                    <div class="guerreiro guerreiro-1">
                        <h3>'.$dados_atacante->nome.'</h3>
                            
                        <div class="foto">
                            <img src="'.BASE.'assets/cards/'.$foto.'" class="ft-guerreiro" alt="'.$dados_atacante->nome.'" />';
                        $row .= '</div>
                            
                        <div class="graduacao-p">
                            '.$graduacao_atacante.'
                            '.$graduacao_texto_atacante.'
                        </div>
                            
                        <ul class="status">
                            <li class="atributos hp at-meter">
                                <strong>HP </strong>
                                <div class="meter animate red">
                                    <em>'.intval($life).'</em>
                                    <span style="width: '.$this->getPorcentagemLife(intval($dados_atacante->hp), intval($life)).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>KI </strong>
                                <div class="meter animate blue">
                                    <em>'.intval($ki_restante).'</em>
                                    <span style="width: '.$treino->getPorcentagemKI(intval($dados_atacante->mana), intval($dados_atacante->ki_usado)).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>FORÇA </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($forca_equipados_atacante).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>RESISTÊNCIA </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($resistencia_equipados_atacante).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>AGILIDADE </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($agilidade_equipados_atacante).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>HABILIDADE </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($habilidade_equipados_atacante).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                        </ul>';
                        
                        $row .= '<div class="equip">';
                            if(!isset($_SESSION['pvp_vitoria']) && !isset($_SESSION['pvp_derrota'])){
                                $row .= '<ul class="ataques">
                                            <h3>Ataques</h3>
                                            '.$batalha->getAtaques($dados_atacante->graduacao, $ki_restante, $dados_atacante->nivel, $idPersonagem, 0).'
                                        </ul>';
                            }

                            if($personagem->getExistsEquipamentos($idPersonagem)){
                                $row .= '<ul class="equipamentos">
                                            <h3>Equipamentos</h3>';
                                            $row .= $equipamentos_atacante;
                                        $row .= '</ul>';
                            }
                        $row .= '</div>';
                    $row .= '</div>
                        
                    <div class="guerreiro guerreiro-2">
                        <input type="hidden" id="idOponente" value="'.$dados_atacado->id.'" />
                        
                        <h3>'.$dados_atacado->nome.'</h3>
                            
                        <div class="foto">
                            <img src="'.BASE.'assets/cards/'.$dados_atacado->foto.'" class="ft-guerreiro" alt="'.$dados_atacado->nome.'" />';
                        $row .= '</div>

                        <div class="graduacao-p">
                            '.$graduacao_atacado.'
                            '.$graduacao_texto_atacado.'
                        </div>

                        <ul class="status">
                            <li class="atributos hp at-meter">
                                <strong>HP </strong>
                                <div class="meter animate red">
                                    <em>'.intval($life_oponente).'</em>
                                    <span style="width: '.$this->getPorcentagemLife(intval($hp_pvp), intval($life_oponente)).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>KI </strong>
                                <div class="meter animate blue">
                                    <em>'.intval($ki_oponente_restante).'</em>
                                    <span style="width: '.$treino->getPorcentagemKI(intval($dados_atacado->mana), intval($ki_oponente)).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>FORÇA </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($forca_equipados_atacado).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>RESISTÊNCIA </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($resistencia_equipados_atacado).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>AGILIDADE </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($agilidade_equipados_atacado).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>HABILIDADE </strong>
                                <div class="meter animate roxo">
                                    <em>'.intval($habilidade_equipados_atacado).'</em>
                                    <span style="width: 100%"><span></span></span>
                                </div>
                            </li>
                        </ul>';
                        
                        $row .= '<div class="equip">';
                            if($personagem->getExistsEquipamentos($idAdversario)){
                                $row .= '<ul class="equipamentos">
                                            <h3>Equipamentos</h3>';
                                            $row .= $equipamentos_atacado;
                                        $row .= '</ul>';
                            }
                        $row .= '</div>';
                    $row .= '</div>
                </div>';
                
                $row .= '<div class="versus">
                    <div class="placar">
                        <div class="contador-batalha">
                            <div class="cronometro"></div>
                        </div>
                        <img src="'.BASE.'assets/batalha.jpg" alt="Batalha DB Heroes" />
                        <div class="log">
                            '.$log.'
                        </div>
                    </div>
                </div>';
        echo $row;
    }
    
    public function getLifeRestante($idPVP){
        $sql = "SELECT sum(dano_personagem) as dano_atacante, sum(dano_adversario) as dano_atacado FROM pvp_historico WHERE idPVP = $idPVP";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getPorcentagemLife($life, $life_usada){
        $total = $life_usada / intval($life);
        
        $resultado = intval($total * 98);

        return $resultado;
    }
    
    public function existsPunicao($idPersonagem){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        if($item->time_ataque > time()){
            return true;
        } else {
            return false;
        }
    }
    
    public function getListaGolpes($ki, $level){        
        $sql = "SELECT * FROM ataques ORDER BY ki ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';

        foreach ($item as $key => $value) {
            
            $sql = "SELECT * FROM personagens_golpes WHERE idGolpe = $value->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                $ativo = 'ativo';
                $checked = 'checked';
            } else {
                $ativo = '';
                $checked = '';
            }
            
            if($ki >= $value->ki){
                if($level >= $value->level){
                    $classe = '';
                    $inativo = '';
                } else {
                    $classe = 'inativo';
                    $inativo = 'disabled';
                }
            } else {
                $classe = 'inativo';
                $inativo = 'disabled';
            }
            
            if($value->ki == 0){
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }
            
            $row .= '<li class="'.$classe.' '.$ativo.'">
                        <img src="'.BASE.'assets/ataques/'.$value->imagem.'" />
                        <span class="nome">Nome: <strong>'.$value->nome.'</strong></span>
                        <span class="ki">KI Necessário: <strong>'.$value->ki.'</strong></span>
                        <span class="dano">Dano do Ataque: <strong>'.$value->dano.'</strong></span>
                        <span class="level">Level Necessário: <strong>'.$value->level.'</strong></span>
                        <input type="checkbox" name="golpe[]" '.$disabled.' value="'.$value->id.'" '.$inativo.' '.$checked.' />
                        <p class="descricao">'.$value->descricao.'</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListaGolpesIDs($idPersonagem){        
        $sql = "SELECT pg.*, a.ki "
             . "FROM personagens_golpes as pg "
            . "INNER JOIN ataques as a ON a.id = pg.idGolpe "
             . "ORDER BY a.ki ASC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
            return $itens;
        }
    }
    
    public function getGolpeExiste($golpe, $idPersonagem){        
        $sql = "SELECT * FROM personagens_golpes WHERE idGolpe = $golpe AND idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getKiRestante($idPVP){
        $sql = "SELECT sum(ki_usado) as ki_pvp FROM pvp_historico WHERE idPVP = $idPVP";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function calculaResistenciaExtra($idPersonagem){
        $core = new Core();
        $equipes = new Equipes();
        $inventario = new Inventario();
        
        $status_extra = 0;
        $resistencia_equipados = 0;
        $status_extra_graduacao = 0;
        
        $dadosGuerreiro = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);

        $status_extra = intval($equipes->getStatusExtra($idPersonagem));

        $status_equipados = $inventario->getStatusEquipados($idPersonagem);
        $resistencia_equipados = intval($status_equipados['resistencia']);

        $status_extra_graduacao = intval($core->getStatusGraduacao($dadosGuerreiro->graduacao));
        
        return $status_extra + $resistencia_equipados + $status_extra_graduacao;
    }
}
