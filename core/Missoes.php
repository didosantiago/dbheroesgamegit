<?php

/**
 * Description of Missoes
 *
 * @author Felipe Faciroli
 */
class Missoes {
    public function getList($idPersonagem, $vip){ 
        
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dadosPersonagem = $stmt->fetch();
        
        $sql = "SELECT * FROM missoes_lista";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $dados_personagem = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        
        $row = '';
        
        if($vip == 1){
            $options_time = '<option disabled placeholder="Tempo"></option>
                            <option value="1">30 Minutos</option>
                            <option value="2">1 Hora</option>
                            <option value="3">1 Hora e 30 Minutos</option>
                            <option value="4">2 Horas</option>
                            <option value="5">2 Horas e 30 minutos</option>
                            <option value="6">3 Horas</option>
                            <option value="7">3 Horas e 30 minutos </option>
                            <option value="8">4 Horas</option>
                            <option value="9">4 Horas e 30 minutos</option>
                            <option value="10">5 Horas</option>
                            <option value="11">5 Horas e 30 minutos</option>
                            <option value="12">6 Horas</option>
                            <option value="24">12 Horas</option>'; 
        } else {
            $options_time = '<option disabled placeholder="Tempo"></option>
                            <option value="1">1 Hora</option>
                            <option value="2">2 Horas</option>
                            <option value="3">3 Horas</option>
                            <option value="4">4 Horas</option>
                            <option value="5">5 Horas</option>
                            <option value="6">6 Horas</option>
                            <option value="7">7 Horas</option>
                            <option value="8">8 Horas</option>
                            <option value="9">9 Horas</option>
                            <option value="10">10 Horas</option>
                            <option value="11">11 Horas</option>
                            <option value="12">12 Horas</option>
                            <option value="24" disabled>24 Horas (Somente VIP)</option>';  
        }
        
        foreach ($item as $key => $value) {
            
            $row .= '<li class="missao missao-'.$core->slug($value->titulo).'" >
                        <div class="box-img">
                            <div class="content-img">
                                <img src="'.BASE.'assets/missoes/'.$value->foto.'" alt="'.$value->titulo.'" />
                            </div>
                            <h3>'.$value->titulo.'</h3>
                        </div>
                        <span class="golds">'.$value->recompensa_ouro.' Golds</span>
                        <form class="formMissao" method="post">
                            <select name="tempo">
                                '.$options_time.'
                            </select>
                            <input type="hidden" name="idMissao" value="'.$value->id.'" />
                            <input type="submit" id="iniciar-missao" '.$this->verificaMissao($value->id, $idPersonagem, $value->total, $value->nivel_minimo, $dadosPersonagem->nivel, $value->qtd_vitorias, $dadosPersonagem->tam).' class="bts-form" name="iniciar" value="Começar" />
                        </form>';
                        if($value->id != 1){
                            $row .= '<div class="especificacoes">
                                    <h4>Conquistas Necessárias</h4>';
                        }
                        
                        if($value->id != 1 && $value->nivel_minimo > 0){
                            $row .= '<div class="indicador level '.$this->verificaLevel($idPersonagem, $value->nivel_minimo).'">
                                        <strong>'.$value->nivel_minimo.'</strong>
                                        <span>Nível</span>
                                        <i class="far fa-check-circle"></i>
                                    </div>';
                        }
                        
                        if($value->qtd_vitorias > 0){
                            $row .= '<div class="indicador tam '.$this->verificaTAM($idPersonagem, $value->qtd_vitorias).'">
                                        <strong><em>'.$dados_personagem->tam.'</em> / '.$value->qtd_vitorias.'</strong>
                                        <span>Vitórias no TAM <br/>(Torneio de Artes Marciais)</span>
                                        <i class="far fa-check-circle"></i>
                                    </div>';
                        }
                        
                        if($value->id != 1){
                            foreach ($item as $key2 => $value2) {
                                if($value2->id < $value->id){
                                    $row .= $this->validaEtapasMissoes($idPersonagem, $value2->id, $value->total, $value2->titulo);
                                }
                            }
                        }
                        
                        if($value->id != 1){
                            $row .= '</div>';
                        }
                    $row .= '</li>';
        }
        
        echo $row;
    }

    
    public function verificaTAM($idPersonagem, $vitorias){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->tam >= $vitorias){
            return 'completed';
        }
    }
    
    public function verificaLevel($idPersonagem, $level){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->nivel >= $level){
            return 'completed';
        }
    }
    
    public function verificaMissoes($idPersonagem, $idMissao, $vitorias){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        $total = $this->getCountTotalMissoes($idPersonagem, $idMissao);
        
        if($total >= $vitorias){
            return 'completed';
        }
    }
    
    public function verificaMissao($idMissao, $idPersonagem, $total, $level_missao, $level, $vitorias_missao, $vitorias){        
        // CHANGE: Query from missoes_lista
        $sql = "SELECT * FROM missoes_lista WHERE id < $idMissao";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $missao = $stmt->fetchAll();
        
        $validado = false;
        $level_validado = false;
        $vitorias_validado = false;

        if($idMissao == 1){
            $validado = true;
        } else {
            foreach ($missao as $key => $value) {
                $sql = "SELECT count(*) as total FROM personagens_missoes WHERE idPersonagem = $idPersonagem AND idMissao = $value->id AND concluida = 1 AND cancelada = 0";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $personagens_missoes = $stmt->fetch();

                if($personagens_missoes->total >= $total){
                    $validado = true;
                } else {
                    $validado = false;
                    break;
                }
            }
        }
        
        if($level >= $level_missao){
            $level_validado = true;
        }
        
        if($vitorias >= $vitorias_missao){
            $vitorias_validado = true;
        }
        
        if($validado && $level_validado && $vitorias_validado){
            return '';
        } else {
            return 'disabled';
        }
    }
    


    public function iniciaMissao($idUsuario, $dados, $idPersonagem, $vip){
        $core = new Core();
        
        // Sanitize and cast inputs
        $tempo = intval($dados['tempo']);
        $missao = intval($dados['idMissao']);
        
        // Gold calculation map based on mission ID
        if($missao == 1){
            $golds = 500;
        } else if($missao == 2){
            $golds = 700;
        } else if($missao == 3){
            $golds = 800;
        } else if($missao == 4){
            $golds = 1000;
        } else if($missao == 5){
            $golds = 1500;
        } else if($missao == 6){
            $golds = 2000;
        } else if($missao == 7){
            $golds = 2500;
        } else if($missao == 8){
            $golds = 3000;
        } else if($missao == 9){
            $golds = 4000;
        } else if($missao == 10){
            $golds = 5000;
        } else if($missao == 11){
            $golds = 6000;
        } else if($missao == 12){
            $golds = 7000;
        } else if($missao == 13){
            $golds = 8500;
        } else if($missao == 14){
            $golds = 10000;
        } else if($missao == 15){
            $golds = 11500;
        } else if($missao == 16){
            $golds = 13000;
        } else if($missao == 17){
            $golds = 15000;
        } else if($missao == 18){
            $golds = 20000;
        } else {
            $golds = 500; // Default
        }
        
        $config = $core->getConfiguracoes();
        
        // Calculate mission duration in seconds
        if($config->teste == 1){
            // TEST MODE: 3 seconds
            $segundos = intval(3);
        } else {
            // NORMAL MODE: Calculate based on VIP status
            if($vip == 1){
                // VIP gets 50% time reduction
                $time_vip = (50 / 100) * intval($tempo);
            } else {
                // Non-VIP: full time
                $time_vip = intval($tempo);
            }
            
            // Convert hours to seconds
            $segundos = $time_vip * 60 * 60;
        }
        
        // Calculate end time (Unix timestamp)
        $tempo_agora = time();
        $tempo_final = $tempo_agora + $segundos;
        
        // Calculate total gold reward
        $calculo_golds = intval($tempo) * intval($golds);
        
        // Prepare fields for database insert - ONLY FIELDS THAT EXIST
        $campos = array(
            'idPersonagem' => $idPersonagem,
            'idUsuario' => $idUsuario,
            'idMissao' => $missao,
            'tempo' => $tempo_agora,           // Mission start time (Unix timestamp)
            'tempo_final' => $tempo_final,     // Mission end time (Unix timestamp)
            'gold' => $calculo_golds
        );
        
        // Insert mission into database
        $insercao = $core->insert('personagens_missoes', $campos);
        
        if($insercao){
            // Get the newly created mission using core method
            $missao_criada = $core->getDados('personagens_missoes', 
                'WHERE idUsuario = '.intval($idUsuario).' AND idPersonagem = '.intval($idPersonagem).' ORDER BY id DESC LIMIT 1');
            
            if($missao_criada){
                // Set session variables for mission tracking
                $_SESSION['missao'] = true;
                $_SESSION['missao_id'] = $missao_criada->id;
            }
            
            // Redirect to portal
            header('Location: '.BASE.'portal');
            exit;
        } else {
            // Mission insert failed
            echo "Erro ao criar missão!";
            exit;
        }
    }


    
    public function contadorMissao($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_missoes WHERE idPersonagem = $idPersonagem AND concluida = 0 AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $missao = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($missao->tempo_final > time()){
                $restante = $missao->tempo_final - time();
                echo $restante;
            }
        }
    }


    public function completarMissaoComRecompensa($idPersonagem, $idMissao){
        try {
            // Get mission details
            $sql = "SELECT * FROM missoes_lista WHERE id = " . intval($idMissao);
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $missao = $stmt->fetch();
            
            if(!$missao){
                return false;
            }
            
            // Update mission as completed
            $sql = "UPDATE personagens_missoes 
                    SET concluida = 1, data_conclusao = NOW() 
                    WHERE idPersonagem = " . intval($idPersonagem) . " 
                    AND idMissao = " . intval($idMissao);
            DB::prepare($sql)->execute();
            
            // Get character current stats
            $sql = "SELECT * FROM usuarios_personagens WHERE id = " . intval($idPersonagem);
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $personagem = $stmt->fetch();
            
            if(!$personagem){
                return false;
            }
            
            // Calculate rewards
            $exp_ganho = intval($missao->experiencia ?? 100);
            $ouro_ganho = intval($missao->ouro ?? 50);
            
            // Add EXP and update character
            $nova_exp = intval($personagem->experiencia) + $exp_ganho;
            $novo_ouro = intval($personagem->ouro) + $ouro_ganho;
            
            $sql = "UPDATE usuarios_personagens 
                    SET experiencia = " . $nova_exp . ", 
                        ouro = " . $novo_ouro . " 
                    WHERE id = " . intval($idPersonagem);
            DB::prepare($sql)->execute();
            
            // Log reward notification
            $conteudo = "Missão concluída! Você ganhou " . $exp_ganho . " de EXP e " . $ouro_ganho . " de ouro!";
            $this->core->setNotification($conteudo, 'sucesso', $idPersonagem);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erro ao completar missão: " . $e->getMessage());
            return false;
        }
    }

    
    public function MissoesRun($idUsuario, $idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_missoes WHERE idUsuario = '$idUsuario' AND idPersonagem = $idPersonagem AND concluida = 0 AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $missao = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $_SESSION['missao'] = true;
            $_SESSION['missao_id'] = $missao->id;  
        }
    }
    
    public function verificaMissaoCancelada($idMissao){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_missoes WHERE id = '$idMissao' AND cancelada = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function somaMissao($idUsuario, $idMissao, $vip){
        $core = new Core();
        $personagem = new Personagens();

        $sql = "SELECT * FROM personagens_missoes WHERE idUsuario = '$idUsuario' AND id = $idMissao AND concluida = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $missao = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($missao->tempo_final < time()){
                $campos = array(
                    'concluida' => 1
                );

                $where = 'id = "'.$missao->id.'"';

                $core->update('personagens_missoes', $campos, $where);

                $gold_per = $personagem->getPontosPersonagem($missao->idPersonagem);
                $personagem->getGuerreiro($missao->idPersonagem);
                
                $sql = "SELECT * FROM graduacoes WHERE id = $missao->idMissao";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $graduacoes = $stmt->fetch();
                
                $graduacao_missao = $graduacoes->id;
                $idBau = $graduacoes->idBau;
                
                $sql = "SELECT * FROM usuarios_personagens WHERE id = $missao->idPersonagem";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $up = $stmt->fetch();
                
                if($vip == 1){
                    $extra_vip_gold = intval($missao->gold) * (20 / 100);
                } else {
                    $extra_vip_gold = 0;
                }
                
                $campos_personagem = array(
                    'gold' => intval($missao->gold) + $up->gold + $extra_vip_gold,
                    'gold_total' => intval($up->gold_total) + intval($missao->gold) + $extra_vip_gold
                );

                $where_personagem = 'id = "'.$missao->idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
                
                $this->drop($missao->idPersonagem, $idMissao, $idBau);
            }
        }
    }
    
    public function drop($idPersonagem, $idMissao, $idBau){
        $inventario = new Inventario();
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $sql = "SELECT * FROM itens WHERE id = $idBau";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $market = $stmt->fetch();
        
        $sorteio = $inventario->getSorteioBau();
        
        if($sorteio == 1){
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

            $campos_insert = array(
                'idMissao' => $idMissao,
                'idItem' => $market->id,
                'idPersonagem' => $idPersonagem,
                'visualizado' => 0   
            );

            $core->insert('personagens_missoes_premios', $campos_insert);
        } else {
            $conteudo = '<p>Infelizmente você não obteve êxito em ganhar um baú nesta missão, porém não desanime, realize novas missões e boa sorte!</p>';
            $core->setNotification($conteudo, 'erro', $idPersonagem);
        }
    }
    
    public function getCountMissoes($idPersonagem){
        // CHANGE: Query from missoes_lista
        $sql = "SELECT * FROM missoes_lista";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($itens as $key => $value) {
            $sql = "SELECT count(*) as total FROM personagens_missoes WHERE idPersonagem = $idPersonagem AND idMissao = $value->id AND concluida = 1 AND cancelada = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            // CHANGE: Use titulo instead of nome
            $row .= '<li>
                        <strong>'.$item->total.'</strong>
                        <span>Missões '.$value->titulo.'</span>
                     </li>';
        }
        
        return $row;
    }
    
    public function getCountTotalMissoes($idPersonagem, $idMissao){
        $sql = "SELECT count(*) as total FROM personagens_missoes WHERE idPersonagem = $idPersonagem AND idMissao = $idMissao AND concluida = 1 AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }

    
    public function validaEtapasMissoes($idPersonagem, $idMissao, $total, $nome_missao){
        $total_alcancado = $this->getCountTotalMissoes($idPersonagem, $idMissao);
        
        return '<div class="indicador '.$this->verificaMissoes($idPersonagem, $idMissao, $total).'">
                    <strong><em>'.$total_alcancado.'</em> / '.$total.'</strong>
                    <span>Missões <br/>'.$nome_missao.'</span>
                    <i class="far fa-check-circle"></i>
                </div>';
    }

    /**
     * Complete mission and grant rewards
     * AUTO-CALLED when timer reaches 0
     */
    public function completeMissao($idMissao, $idPersonagem) {
        global $core;
        
        try {
            $missao = $core->getDados('personagens_missoes', 'WHERE id = ' . intval($idMissao));
            if (!$missao) {
                return false;
            }
            
            $sql = "UPDATE personagens_missoes SET concluida = 1 WHERE id = ?";
            $stmt = DB::prepare($sql);
            if (!$stmt->execute([intval($idMissao)])) {
                return false;
            }
            
            $missaoLista = $core->getDados('missoes_lista', 'WHERE id = ' . intval($missao->idMissao));
            $personagem = $core->getDados('usuarios_personagens', 'WHERE id = ' . intval($idPersonagem));
            
            if ($missaoLista && $personagem) {
                $experiencia = intval($missaoLista->experiencia);
                $ouro = intval($missaoLista->ouro);
                
                $campos = array(
                    'experiencia' => intval($personagem->experiencia) + $experiencia,
                    'ouro' => intval($personagem->ouro) + $ouro
                );
                
                $core->update('usuarios_personagens', $campos, 'id = ' . intval($idPersonagem));
                
                if (isset($_SESSION['missao'])) {
                    unset($_SESSION['missao']);
                }
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get active mission for timer display
     * CALLED in index.php to get mission data
     */
    public function getMissaoAtiva($idPersonagem) {
        try {
            $sql = "SELECT 
                        pm.id, 
                        pm.tempo, 
                        pm.duracao, 
                        pm.concluida, 
                        ml.titulo, 
                        ml.experiencia, 
                        ml.ouro
                    FROM personagens_missoes pm
                    INNER JOIN missoes_lista ml ON pm.idMissao = ml.id
                    WHERE pm.idPersonagem = ? 
                    AND pm.cancelada = 0 
                    AND pm.concluida = 0
                    LIMIT 1";
            
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($idPersonagem)]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            return null;
        }
    }

    public function completarMissao($idPersonagem, $idMissao){
        // Get mission data
        $sql = "SELECT * FROM missoes_lista WHERE id = " . intval($idMissao);
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $missao = $stmt->fetch();
        
        if($missao){
            // Update mission as complete
            $sql = "UPDATE personagens_missoes SET concluida = 1, data_conclusao = NOW() 
                    WHERE idPersonagem = " . intval($idPersonagem) . " AND idMissao = " . intval($idMissao);
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            // Get player character
            $sql = "SELECT * FROM usuarios_personagens WHERE id = " . intval($idPersonagem);
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $personagem = $stmt->fetch();
            
            // Calculate and give rewards
            $exp_ganho = $missao->experiencia ?? 100;
            $ouro_ganho = $missao->ouro ?? 50;
            
            // Add EXP
            $nova_exp = $personagem->experiencia + $exp_ganho;
            $sql = "UPDATE usuarios_personagens SET experiencia = " . intval($nova_exp) . " 
                    WHERE id = " . intval($idPersonagem);
            DB::prepare($sql)->execute();
            
            // Add Gold
            $novo_ouro = $personagem->ouro + $ouro_ganho;
            $sql = "UPDATE usuarios_personagens SET ouro = " . intval($novo_ouro) . " 
                    WHERE id = " . intval($idPersonagem);
            DB::prepare($sql)->execute();
            
            return true;
        }
        
        return false;
    }



}

?>
