<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        
        $sql = "SELECT * FROM missoes";
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
            
            $row .= '<li class="missao missao-'.$core->slug($value->nome).'" >
                        <div class="box-img">
                            <div class="content-img">
                                <img src="'.BASE.'assets/missoes/'.$value->foto.'" alt="'.$value->nome.'" />
                            </div>
                            <h3>'.$value->nome.'</h3>
                        </div>
                        <span class="golds">'.$value->golds.' Golds</span>
                        <form class="formMissao" method="post">
                            <select name="tempo">
                                '.$options_time.'
                            </select>
                            <input type="hidden" name="idMissao" value="'.$value->id.'" />
                            <input type="submit" id="iniciar-missao" '.$this->verificaMissao($value->id, $idPersonagem, $value->total, $value->level_inicial, $dadosPersonagem->nivel, $value->qtd_vitorias, $dadosPersonagem->tam).' class="bts-form" name="iniciar" value="Começar" />
                        </form>';
                        if($value->id != 1){
                            $row .= '<div class="especificacoes">
                                    <h4>Conquistas Necessárias</h4>';
                        }
                        
                        if($value->id != 1 && $value->level_inicial > 0){
                            $row .= '<div class="indicador level '.$this->verificaLevel($idPersonagem, $value->level_inicial).'">
                                        <strong>'.$value->level_inicial.'</strong>
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
                            $sql = "SELECT * FROM missoes WHERE id < $value->id";
                            $stmt = DB::prepare($sql);
                            $stmt->execute();
                            $itens_menores = $stmt->fetchAll();

                            foreach ($item as $key2 => $value2) {
                                if($value2->id < $value->id){
                                    $row .= $this->validaEtapasMissoes($idPersonagem, $value2->id, $value->total, $value2->nome);
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
        $sql = "SELECT * FROM missoes WHERE id < $idMissao";
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
        
        $tempo = $dados['tempo'];
        $missao = $dados['idMissao'];
        
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
        }
        
        $config = $core->getConfiguracoes();
        
        if($config->teste == 1){
            $segundos = intval(3);
        } else {
            if($vip == 1){
                $time_vip = (50 / 100) * intval($tempo);
            } else {
                $time_vip = intval($tempo);
            }
            
            $segundos = $time_vip * 3600;
        }
        
        $tempo_atual = time();
        $tempo_final = time() + $segundos;
        
        $calculo_golds = intval($tempo) * intval($golds);
        
        $campos = array(
            'idPersonagem' => $idPersonagem,
            'idUsuario' => $idUsuario,
            'idMissao' => $missao,
            'tempo' => $tempo_atual,
            'tempo_final' => $tempo_final,
            'gold' => $calculo_golds
        );
        
        $core->insert('personagens_missoes', $campos);
        
        $sql = "SELECT * FROM personagens_missoes WHERE idUsuario = '$idUsuario' ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_missao = $stmt->fetch();
        
        $_SESSION['missao'] = true;
        $_SESSION['missao_id'] = $dados_missao->id;        
        header('Location: '.BASE.'portal');
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
        $sql = "SELECT * FROM missoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($itens as $key => $value) {
            $sql = "SELECT count(*) as total FROM personagens_missoes WHERE idPersonagem = $idPersonagem AND idMissao = $value->id AND concluida = 1 AND cancelada = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            $row .= '<li>
                        <strong>'.$item->total.'</strong>
                        <span>Missões '.$value->nome.'</span>
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
}
