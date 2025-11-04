<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Npc
 *
 * @author Felipe Faciroli
 */
class Npc {
    public function npcRun($idPersonagem, $idGuerreiro){
        $core = new Core();
        
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $idGuerreiro AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_npc = $stmt->fetch();
            $_SESSION['npc'] = true;
            $_SESSION['npc_id'] = $dados_npc->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function getOponenteNPC($id){
        if($id != ''){
            $sql = "SELECT g.*, p.nome, p.raca, p.foto "
                 . "FROM guerreiros_arena as g "
                 . "INNER JOIN guerreiros as p ON p.id = g.idGuerreiro "
                 . "WHERE g.id = '$id'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row;
        }
    }
    
    public function saveBatalhaNPC($idPersonagem, $idOponente){
        $core = new Core();
        $personagem = new Personagens();
        
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

        $core->insert('npc', $campos);
        
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_npc = $stmt->fetch();
        
        $personagem->getGuerreiro($idPersonagem);
        
        $up_guerreiro = array(
            'energia_usada' => intval($personagem->energia_usada) + 10,
            'time_stamina' => time()
        );
        
        $where_guerreiro = 'id = "'.$idPersonagem.'"';

        $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
        
        $_SESSION['npc'] = true;
        $_SESSION['npc_id'] = $dados_npc->id;
    }
    
    public function getGuerreiroNPCAtacado($idPVP){
        $sql = "SELECT * FROM npc WHERE id = $idPVP";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->atacado == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function atack($idAtack, $meuID, $idAdversario, $desafiante, $finalizado = 0){
        $core = new Core();
        $equipes = new Equipes();
        $inventario = new Inventario();
        
        //STATUS EXTRA DAS EQUIPES
        $status_extra = intval($equipes->getStatusExtra($meuID));
        $status_extra_graduacao = 0;
        
        if($desafiante == 0){
            $dados_atacante = $this->getDadosAdv($meuID);
            $dados_atacado = $core->getDados('usuarios_personagens', 'WHERE id = '.$idAdversario);
            
            $sql = "SELECT * FROM npc WHERE idPersonagem = $idAdversario AND idDesafiado = $meuID AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_npc = $stmt->fetch();
            
            $sql = "SELECT sum(ki_usado) as total FROM npc_historico WHERE idNPC = $dados_npc->id";
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
                        $ki_usado_npc = $dados_ataque->ki;
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
            $dados_atacado = $this->getDadosAdv($idAdversario);
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
            $sql = "SELECT * FROM npc WHERE idPersonagem = $idAdversario AND idDesafiado = $meuID AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_npc = $stmt->fetch();
        } else {
            $sql = "SELECT * FROM npc WHERE idPersonagem = $meuID AND idDesafiado = $idAdversario AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_npc = $stmt->fetch();

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
        
        if($desafiante == 0){
            $resistenciaCalculada = $this->calculaResistenciaExtra($idAdversario);
            $defesa_atacado = $dados_atacado->resistencia + $resistenciaCalculada;
        } else {
            $defesa_atacado = $dados_atacado->resistencia;
        }
        
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
        if($dados_npc->atacou == 0){
            $campos_up_ki = array(
                'ki_usado' => intval($dados_atacante->ki_usado) + $dados_ataque->ki
            );

            $where_up_ki = 'id = "'.$meuID.'"';

            $core->update('usuarios_personagens', $campos_up_ki, $where_up_ki);
        }
        
        if($dados_npc->atacou == 0){
            $dados_history = $core->getDados('npc_historico', "WHERE idNPC = $dados_npc->id ORDER BY id DESC LIMIT 1");
            $round = $dados_history->round + 1;
        } else {
            $dados_history = $core->getDados('npc_historico', "WHERE idNPC = $dados_npc->id ORDER BY id DESC LIMIT 1");
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
                    'idNPC' => $dados_npc->id,
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
                    'idNPC' => $dados_npc->id,
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
                    'idNPC' => $dados_npc->id,
                    'dano_personagem' => $dano_final,
                    'ki_usado' => $ki_usado_npc,
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
                    'idNPC' => $dados_npc->id,
                    'dano_personagem' => $dano_final,
                    'ki_usado' => $ki_usado_npc,
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

        $core->insert('npc_historico', $campos);
        
        //ATUALIZA O TEMPO FINAL DO ROUND
        $contador = time() + 30;
        
        if($desafiante == 0){
            $campos_npc = array(
                'atacou' => 0,
                'atacado' => 1,
                'time_final' => $contador
            );

            $where_npc = 'id = "'.$dados_npc->id.'"';

            $core->update('npc', $campos_npc, $where_npc);
        } else {
            $campos_npc = array(
                'atacou' => 1,
                'atacado' => 0,
                'time_final' => $contador
            );

            $where_npc = 'id = "'.$dados_npc->id.'"';

            $core->update('npc', $campos_npc, $where_npc);
        }
        
        
        if($desafiante == 0){
            $dados_npc = $core->getDados('npc', 'WHERE idPersonagem = '.$idAdversario.' AND idDesafiado = '.$meuID.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
        } else {
            $dados_npc = $core->getDados('npc', 'WHERE idPersonagem = '.$meuID.' AND idDesafiado = '.$idAdversario.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
        }
        
        $lifes = $this->getLifeRestante($dados_npc->id);
        $kis = $this->getKiRestante($dados_npc->id);
        
        if($desafiante == 0){
            $life_oponente = $dados_atacante->hp - $lifes->dano_atacante;
            $ki_oponente = $dados_atacante->ki - $kis->ki_npc;
        } else {
            $life_oponente = $dados_atacado->hp - $lifes->dano_atacado;
        }
        
        //ADVERSARIO ATACA NOVAMENTE
        if($dados_npc->atacou == 1 && $dados_npc->atacado == 0){
            if($life_oponente <= 0){
                $finalizado = 1;
            }
            
            if($finalizado == 0){
                $this->atack(4, $idAdversario, $meuID, 0);

                $campos_npc = array(
                    'atacou' => 0,
                    'atacado' => 1
                );

                $where_npc = 'id = "'.$dados_npc->id.'"';

                $core->update('npc', $campos_npc, $where_npc);

                $_SESSION['npc_atacado'] = true;
            }
            
        }
    }
    
    public function getLifeRestante($idNPC){
        $sql = "SELECT sum(dano_personagem) as dano_atacante, sum(dano_adversario) as dano_atacado FROM npc_historico WHERE idNPC = $idNPC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getKiRestante($idNPC){
        $sql = "SELECT sum(ki_usado) as ki_npc FROM npc_historico WHERE idNPC = $idNPC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function printConfronto($idAdversario, $idPersonagem, $life, $life_oponente, $ki, $vip, $ki_oponente){
        $core = new Core();
        $personagem = new Personagens();
        $treino = new Treino();
        $batalha = new Batalha();
        
        $dados_atacado = $this->getDadosAdv($idAdversario);
        $dados_atacante = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        
        $energia = $dados_atacante->energia - $dados_atacante->energia_usada;
        
        $foto = str_replace('cards/', '', $dados_atacante->foto);
        
        $graduacao_atacante = $personagem->getGraduacaoBatalha($dados_atacante->nivel);
        $graduacao_texto_atacante = $personagem->getGraduacaoTextoBatalha($dados_atacante->nivel);
        $graduacao_atacado = $personagem->getGraduacaoBatalha($dados_atacado->nivel);
        $graduacao_texto_atacado = $personagem->getGraduacaoTextoBatalha($dados_atacado->nivel);
        
        $equipamentos_atacante = $personagem->getEquipamentos($idPersonagem);
        
        $ki_restante = $ki - $dados_atacante->ki_usado;
        
        $log = $this->getLogNPC($idPersonagem, $idAdversario);
        
        if($vip == 1){
            $porcentagemVip = (40 / 100) * intval($dados_atacado->hp);
            $hp_npc = $dados_atacado->hp - $porcentagemVip;
        } else {
            $porcentagemFree = (20 / 100) * intval($dados_atacado->hp);
            $hp_npc = $dados_atacado->hp - $porcentagemFree;
        }
        
        $ki_oponente_restante = $dados_atacado->mana - $ki_oponente;

        if($dados_atacante->nivel > 1){
            $nivel_hp_atacante = 150 + ((intval($dados_atacante->nivel) - 1) * 50);
        } else {
            $nivel_hp_atacante = 150;
        }

        $porcentagem_hp_atacante = $treino->getPorcentagemHP($nivel_hp_atacante, $nivel_hp_atacante - $life);

        $row = '';
        
        $row .= '<div class="guerreiro">
                    <div class="info">
                        <h3>'.$dados_atacante->nome.'</h3>
                        <div class="foto">
                            <img src="'.BASE.'assets/cards/'.$foto.'" class="ft-guerreiro" alt="'.$dados_atacante->nome.'" />';
                        $row .= '</div>
                        <div class="graduacao">';
                            $row .= $graduacao_atacante.$graduacao_texto_atacante;
                        $row .= '</div>
                        <ul class="status">
                            <li class="atributos hp at-meter">
                                <strong>HP </strong>
                                <div class="meter animate red">
                                    <em>'.$life . ' / ' . $nivel_hp_atacante.'</em>
                                    <span style="width: '.$porcentagem_hp_atacante.'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>KI </strong>
                                <div class="meter animate blue">
                                    <em>'.$ki_restante.'</em>
                                    <span style="width: '.$treino->getPorcentagemKI($dados_atacante->mana, $dados_atacante->ki_usado).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos energia at-meter">
                                <strong>Energia </strong>
                                <div class="meter animate">
                                    <em>'.$energia.'</em>
                                    <span style="width: '.$treino->getPorcentagemEnergia($dados_atacante->energia, $dados_atacante->energia_usada).'%"><span></span></span>
                                </div>
                            </li>
                        </ul>
                    </div>';
                        
                    $row .= '<div class="equip" style="margin: 0 0.4%;">';
                    
                    if(!isset($_SESSION['npc_vitoria']) && !isset($_SESSION['npc_derrota'])){
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
                    
                $row .= '</div>';

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
                        </div>
                
                <div class="guerreiro">
                    <div class="info">
                        <h3>'.$dados_atacado->nome.'</h3>
                        <input type="hidden" id="idOponente" value="'.$dados_atacado->id.'" />
                        <div class="foto">
                            <img src="'.BASE.'assets/guerreiros/'.$dados_atacado->foto.'" class="ft-guerreiro" alt="'.$dados_atacado->nome.'" />';
                            $row .= '</div>
                        <div class="graduacao">';
                            $row .= $graduacao_atacado.$graduacao_texto_atacado;
                        $row .= '</div>
                        <ul class="status">
                            <li class="atributos hp at-meter">
                                <strong>HP </strong>
                                <div class="meter animate red">
                                    <em>'.$life_oponente.'</em>
                                    <span style="width: '.$this->getPorcentagemLife($hp_npc, $life_oponente).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos mana at-meter">
                                <strong>KI </strong>
                                <div class="meter animate blue">
                                    <em>'.$ki_oponente_restante.'</em>
                                    <span style="width: '.$treino->getPorcentagemKI($dados_atacado->mana, $ki_oponente).'%"><span></span></span>
                                </div>
                            </li>
                            <li class="atributos energia at-meter">
                                <strong>Energia </strong>
                                <div class="meter animate">
                                    <em>'.$dados_atacado->energia.'</em>
                                    <span style="width: 98%"><span></span></span>
                                </div>
                            </li>
                        </ul>
                    </div>';
                $row .= '</div>';
                
        echo $row;
    }
    
    public function getLogNPC($idPersonagem, $idGuerreiro){
        $sql = "SELECT ph.* "
             . "FROM npc_historico as ph "
             . "INNER JOIN npc as p ON ph.idNPC = p.id "
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
    
    public function contadorNPC($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($item->time_final > time()){
                $restante = $item->time_final - time();
                echo $restante;
            } else {
                // Time expired, return 0
                echo 0;
            }
        } else {
            // No battle found, return 0
            echo 0;
        }
    }
    
    public function contadorBatalhaNPC($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($item->time_ataque > time()){
                $restante = $item->time_ataque - time();
                echo $restante;
            }
        }
    }
    
    public function npcExecuting($idPersonagem){
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_pvp = $stmt->fetch();
            $_SESSION['npc'] = true;
            $_SESSION['npc_id'] = $dados_pvp->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function npcPausado($idPersonagem){        
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND pausado = 1 AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $dados_pvp = $stmt->fetch();
            $_SESSION['npc'] = true;
            $_SESSION['npc_id'] = $dados_pvp->id;
        
            return true;
        } else {
            return false;
        }
    }
    
    public function getPorcentagemLife($life, $life_usada){
        $total = $life_usada / intval($life);
        
        $resultado = intval($total * 98);

        return $resultado;
    }
    
    public function getDadosAdv($idAdversario){
        $sql = "SELECT ga.*, g.nome, g.raca, g.foto "
             . "FROM guerreiros_arena as ga "
             . "INNER JOIN guerreiros as g ON g.id = ga.idGuerreiro "
             . "WHERE ga.id = $idAdversario ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function npcEsgotado($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND concluido = 0 AND pausado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        $count = $stmt->rowCount();
        
        if($count > 0){
            $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_personagem = $stmt->fetch();

            $sql = "SELECT * FROM guerreiros_arena WHERE id = $item->idDesafiado";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $oponente = $stmt->fetch();

            if($item->time_pausado > time()){
                $campos_npc = array(
                    'vencedor' => 0,
                    'concluido' => 1,
                    'pausado' => 0
                );

                $where_npc = 'id = "'.$item->id.'"';

                $core->update('npc', $campos_npc, $where_npc);
                
                unset($_SESSION['npc_atacado']);
                unset($_SESSION['npc']);
                unset($_SESSION['npc_id']);
                unset($_SESSION['npc_vitoria']);
                unset($_SESSION['npc_derrota']);
                unset($_SESSION['npc_desafiador']);
            }
        }
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
