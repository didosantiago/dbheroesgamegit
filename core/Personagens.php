<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Personagens
 *
 * @author Felipe Faciroli
 */
class Personagens {
    public $id;
    public $idUsuario;
    public $planeta;
    public $idPlaneta;
    public $persona;
    public $data_cadastro;
    public $boneco;
    public $nome;
    public $raca;
    public $foto;
    public $hp;
    public $mana;
    public $ki_usado;
    public $energia;
    public $energia_usada;
    public $graduacao;
    public $graduacao_id;
    public $nivel;
    public $gold;
    public $gold_total;
    public $forca;
    public $agilidade;
    public $habilidade;
    public $resistencia;
    public $sorte;
    public $gold_guardados;
    public $vitorias_pvp;
    public $derrotas;
    public $pp_creditos;
    public $pontos;
    public $exp;
    public $tam;
    public $time_stamina;
    public $time_ki;
    public $time_hp;
    public $time_invasao;
    
    public function getList(){        
        $sql = "SELECT * FROM personagens WHERE liberado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $row .= '<label dataFoto="'.$value->foto.'" class="item-personagem" for="per_'.$value->nome.'">
                        <input type="radio" id="per_'.$value->nome.'" name="idPersonagem" value="'.$value->id.'" required />
                        <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                        <h3>'.$value->nome.'</h3>
                        <span><strong>Raça: </strong>'.$value->raca.'</span>
                     </label>';
        }
        
        echo $row;
    }
    
    public function getPlanetas(){        
        $sql = "SELECT * FROM planetas";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $row .= '<label class="item-planetas" for="planeta_'.$value->nome.'">
                        <input type="radio" id="planeta_'.$value->nome.'" name="idPlaneta" value="'.$value->id.'" required />
                        <img src="'.BASE.'assets/'.$value->imagem.'" alt="'.$value->nome.'" />
                        <h3>'.$value->nome.'</h3>
                     </label>';
        }
        
        echo $row;
    }
     public function getMeusPersonagens($idUsuario){
    // Check if user ID is valid
    if($idUsuario == null || $idUsuario == '' || $idUsuario == 0){
        echo '<div class="no-characters">
                <p>Você ainda não criou nenhum guerreiro.</p>
                <a href="'.BASE.'criar-personagem" class="bts-form">Criar Primeiro Guerreiro</a>
            </div>';
        return;
    }

    // Fetch ALL characters for the user (NO LIMIT)
    $sql = "SELECT up.id, up.nome, up.foto, p.raca
            FROM usuarios_personagens as up
            INNER JOIN personagens as p ON p.id = up.idPersonagem
            WHERE up.idUsuario = ?";
    
    $stmt = DB::prepare($sql);
    $stmt->execute([$idUsuario]);
    $item = $stmt->fetchAll();
    
    $row = '';
    
    foreach ($item as $key => $value) {
        $foto = str_replace('cards/', '', $value->foto);
        $row .= '<label class="item-personagem meu-personagem" dataid="'.$value->id.'">
                    <img src="'.BASE.'assets/cards/'.$foto.'" alt="'.$value->nome.'" />
                    <h3>'.$value->nome.'</h3>
                    <span><strong>Raça: </strong>'.$value->raca.'</span>
                 </label>';
    }
    
    echo $row;
}
    
    public function getGuerreiroInfo($id){
        
        $treino = new Treino();
        
        if($id != ''){
            $sql = "SELECT up.*, pn.nome as planeta, p.raca "
                 . "FROM usuarios_personagens as up "
                 . "INNER JOIN personagens as p ON p.id = up.idPersonagem "
                 . "INNER JOIN planetas as pn ON pn.id = up.idPlaneta "
                 . "WHERE up.id = '$id'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            $this->id = $row->id;
            $this->idUsuario = $row->idUsuario;
            $this->planeta = $row->planeta;
            $this->idPlaneta = $row->idPlaneta;
            $this->persona = $row->idPersonagem;
            $this->data_cadastro = $row->data_cadastro;
            $this->boneco = $row->idPersonagem;
            $this->nome = $row->nome;
            $this->raca = $row->raca;
            $this->foto = $row->foto;
            $this->hp = $row->hp;
            $this->mana = $row->mana;
            $this->ki_usado = $row->ki_usado;
            $this->energia = $row->energia;
            $this->energia_usada = $row->energia_usada;
            $this->graduacao = $this->getGraduacaoName($row->nivel);
            $this->graduacao_id = $row->graduacao;
            $this->nivel = $row->nivel;
            $this->gold = $row->gold;
            $this->gold_total = $row->gold_total;
            $this->forca = $row->forca;
            $this->agilidade = $row->agilidade;
            $this->habilidade = $row->habilidade;
            $this->resistencia = $row->resistencia;
            $this->sorte = $row->sorte;
            $this->gold_guardados = $row->gold_guardados;
            $this->vitorias_pvp = $row->vitorias_pvp;
            $this->derrotas = $row->derrotas;
            $this->pp_creditos = $row->pp_creditos;
            $this->pontos = $row->pontos;
            $this->exp = $row->exp;
            $this->tam = $row->tam;
            $this->time_stamina = $row->time_stamina;
            $this->time_ki = $row->time_ki;
            $this->time_hp = $row->time_hp;
            $this->time_invasao = $row->time_invasao;
        }
        
        $foto = str_replace('cards/', '', $this->foto);
        
        if($this->nivel > 1){
            $nivel_hp = 150 + ((intval($this->nivel) - 1) * 50);
        } else {
            $nivel_hp = 150;
        }

        $porcentagem_hp = $treino->getPorcentagemHP($nivel_hp, $nivel_hp - $this->hp);
        $porcentagem_ki = $treino->getPorcentagemKI($this->mana, $this->ki_usado);
        $porcentagem_energia = $treino->getPorcentagemEnergia($this->energia, $this->energia_usada);
        
        $result_ki = intval($this->mana) - intval($this->ki_usado);
        $result_energia = intval($this->energia) - intval($this->energia_usada);
        
        $rows = '<div class="foto-personagem">
                    <a href="'.BASE.'profile">
                        <img src="'.BASE.'assets/cards/'.$foto.'" alt="'.$this->nome.'" />
                    </a>
                </div>
                <div class="info">
                    <h3>'.$this->nome.'</h3>
                    <div class="atributos raca">
                        <strong>Raça: </strong>
                        '.$this->raca.'
                    </div>
                    <div class="atributos planeta">
                        <strong>Planeta: </strong>
                        '.$this->planeta.'
                    </div>
                    <div class="atributos graduacao">
                        <strong>Graduação: </strong>
                        '.$this->graduacao.'
                    </div>
                    <div class="atributos nivel">
                        <strong>Nível: </strong>
                        '.$this->nivel.'
                    </div>
                    <div class="atributos nivel">
                        <strong>Gold: </strong>
                        <i class="fas fa-coins"></i> '.$this->gold.'
                    </div>
                    <div class="atributos hp at-meter">
                    <strong>HP </strong>
                        <div class="meter animate red">
                            <em>'.$this->hp.' / <strong>'.$nivel_hp.'</strong></em>
                            <span style="width: '.$porcentagem_hp.'%"><span></span></span>
                        </div>
                    </div>
                    <div class="atributos mana at-meter">
                        <strong>KI </strong>
                        <div class="meter animate blue">
                            <em>'.$result_ki.' / <strong>'.$this->mana.'</strong></em>
                            <span style="width: '.$porcentagem_ki.'%"><span></span></span>
                        </div>
                    </div>
                    <div class="atributos energia at-meter">
                        <strong>Energia </strong>
                        <div class="meter animate">
                            <em>'.$result_energia.' / <strong>'.$this->energia.'</strong></em>
                            <span style="width: '.$porcentagem_energia.'%"><span></span></span>
                        </div>
                    </div>
                    <a href="javascript:void(0);" class="bts-form bt-jogar" data-character-id="'.$this->id.'" onclick="switchCharacter('.$this->id.')">▶ JOGAR</a>
                </div>';
        
        echo $rows;
    }
    
    public function getGuerreiro($id){
        
        if($id != ''){
            $sql = "SELECT up.*, pn.nome as planeta, p.raca "
                 . "FROM usuarios_personagens as up "
                 . "INNER JOIN personagens as p ON p.id = up.idPersonagem "
                 . "INNER JOIN planetas as pn ON pn.id = up.idPlaneta "
                 . "WHERE up.id = '$id'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            $this->id = $row->id;
            $this->idUsuario = $row->idUsuario;
            $this->planeta = $row->planeta;
            $this->idPlaneta = $row->idPlaneta;
            $this->persona = $row->idPersonagem;
            $this->data_cadastro = $row->data_cadastro;
            $this->boneco = $row->idPersonagem;
            $this->nome = $row->nome;
            $this->raca = $row->raca;
            $this->foto = $row->foto;
            $this->hp = $row->hp;
            $this->mana = $row->mana;
            $this->ki_usado = $row->ki_usado;
            $this->energia = $row->energia;
            $this->energia_usada = $row->energia_usada;
            $this->graduacao = $this->getGraduacaoName($row->nivel);
            $this->graduacao_id = $row->graduacao;
            $this->nivel = $row->nivel;
            $this->gold = $row->gold;
            $this->gold_total = $row->gold_total;
            $this->forca = $row->forca;
            $this->agilidade = $row->agilidade;
            $this->habilidade = $row->habilidade;
            $this->resistencia = $row->resistencia;
            $this->sorte = $row->sorte;
            $this->gold_guardados = $row->gold_guardados;
            $this->vitorias_pvp = $row->vitorias_pvp;
            $this->derrotas = $row->derrotas;
            $this->pp_creditos = $row->pp_creditos;
            $this->pontos = $row->pontos;
            $this->exp = $row->exp;
            $this->tam = $row->tam;
            $this->time_stamina = $row->time_stamina;
            $this->time_ki = $row->time_ki;
            $this->time_hp = $row->time_hp;
            $this->time_invasao = $row->time_invasao;
        }
    }
    
    public function getOponente($id){
        if($id != ''){
            $sql = "SELECT up.*, pn.nome as planeta, p.raca "
                 . "FROM usuarios_personagens as up "
                 . "INNER JOIN personagens as p ON p.id = up.idPersonagem "
                 . "INNER JOIN planetas as pn ON pn.id = up.idPlaneta "
                 . "WHERE up.id = '$id'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row;
        }
    }
       
    public function existsGuerreiro($idUsuario){
        if($idUsuario != ''){
            $sql = "SELECT COUNT(*) as total FROM usuarios_personagens WHERE idUsuario = :idUsuario";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return ($result && $result->total > 0);
        }
        return false;
    }

    public function esgotado($idUsuario){
        if($idUsuario != ''){
            $sql = "SELECT COUNT(*) as total FROM usuarios_personagens WHERE idUsuario = :idUsuario";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return ($result && $result->total >= 20);
        }
        return false;
    }
    
    public function getEquipamentos($idPersonagem){
        $sql = "SELECT i.* "
             . "FROM personagens_itens_equipados as pie "
             . "INNER JOIN itens as i ON i.id = pie.idItem "
             . "WHERE pie.idPersonagem = $idPersonagem "
             . "AND pie.vazio = 0";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';

        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();
            
            foreach ($item as $key => $value) {
                $row .= '<li>';
                    $row .= '<img src="'.BASE.'assets/'.$value->foto.'" />';
                    $row .= '<div class="info">
                        <h3>'.$value->nome.'</h3>';
                    
                        if($value->hp > 0){
                            $row .= '<span><strong>HP: </strong> +'.$value->hp.'</span>';
                        }

                        if($value->mana > 0){
                            $row .= '<span><strong>KI: </strong> +'.$value->mana.'</span>';
                        }

                        if($value->energia > 0){
                            $row .= '<span><strong>Energia: </strong> +'.$value->energia.'</span>';
                        }

                        if($value->forca > 0){
                            $row .= '<span><strong>Força: </strong> +'.$value->forca.'</span>';
                        }

                        if($value->agilidade > 0){
                            $row .= '<span><strong>Agilidade: </strong> +'.$value->agilidade.'</span>';
                        }

                        if($value->habilidade > 0){
                            $row .= '<span><strong>Habilidade:</strong>+ '.$value->habilidade.'</span>';
                        }

                        if($value->resistencia > 0){
                            $row .= '<span><strong>Resistência: </strong> +'.$value->resistencia.'</span>';
                        }

                        if($value->sorte > 0){
                            $row .= '<span><strong>Sorte: </strong> +'.$value->sorte.'</span>';
                        }
                    $row .= '</div>';                    
                $row .= '</li>';
            }
        }
        
        return $row;
    }
    
    public function getExistsEquipamentos($idPersonagem){
        $sql = "SELECT i.* "
             . "FROM personagens_itens_equipados as pie "
             . "INNER JOIN itens as i ON i.id = pie.idItem "
             . "WHERE pie.idPersonagem = $idPersonagem "
             . "AND pie.vazio = 0";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';

        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getInfoPersonagem($id){
        if($id != ''){
            $sql = "SELECT * FROM personagens WHERE id = '$id'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            return $item;
        }
    }
    
    public function nomeGuerreiroExists($nome){
        
        if($nome != ''){
            $sql = "SELECT * FROM usuarios_personagens WHERE UPPER(nome) = '$nome' && nome = '$nome'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function calculaCacada($idUsuario, $dados, $idPlaneta, $idPersonagem, $vip, $exp, $nivel_exp){
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $tempo = $dados['tempo'];
        $qtd_exp = 0;
        
        $sql = "SELECT * FROM cacadas_gold";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $table_exp = $stmt->fetchAll();
        $vl_gold = 0;
        
        foreach ($table_exp as $key => $value) {
            if($exp >= $value->exp_inicial && $exp <= $value->exp_final){
                $porcentagem_tempo = $tempo * (10 / 100);
                $qtd_exp = intval($value->ganho_exp) * intval($porcentagem_tempo);

                if($tempo == 10){
                    $vl_gold = $value->time_10;
                } else if($tempo == 20){
                    $vl_gold = $value->time_20;
                } else if($tempo == 30){
                    $vl_gold = $value->time_30;
                } else if($tempo == 40){
                    $vl_gold = $value->time_40;
                } else if($tempo == 50){
                    $vl_gold = $value->time_50;
                } else if($tempo == 60){
                    $vl_gold = $value->time_60;
                }
            } 
        }
        
        if($config->teste == 1){
            $qtd_exp = 200;
            $segundos = 10;
        } else {
            if($vip == 1){
                $time_vip = (50 / 100) * intval($tempo);
            } else {
                $time_vip = intval($tempo);
            }
            
            $segundos = $time_vip * 6;
        }
        
        $tempo_atual = time();
        $tempo_final = time() + $segundos;

        $campos = array(
            'idPersonagem' => $idPersonagem,
            'idPlaneta' => $idPlaneta,
            'idUsuario' => $idUsuario,
            'tempo_inicial' => $tempo_atual,
            'tempo_final' => $tempo_final,
            'gold' => $vl_gold,
            'tempo' => $tempo,
            'data' => date('Y-m-d'),
            'exp' => $qtd_exp,
            'concluida' => 0,
            'cancelada' => 0
        );
        
        $core->insert('cacadas', $campos);

        $sql = "SELECT * FROM cacadas WHERE idUsuario = '$idUsuario' ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $dados_cacada = $stmt->fetch();

        // Set session variables
        $_SESSION['cacada'] = true;
        $_SESSION['cacada_id'] = $dados_cacada->id;

        // Force session write before redirect
        session_write_close();

        // Reopen session for next request
        session_start();

        // Redirect
        header('Location: '.BASE.'portal');
        exit;
    }
    
    public function somaCacada($idUsuario, $idCacada){
        $core = new Core();
        $treino = new Treino();
        
        $sql = "SELECT * FROM cacadas WHERE idUsuario = '$idUsuario' AND id = $idCacada AND concluida = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $cacada = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($cacada->tempo_final < time()){
                $campos = array(
                    'concluida' => 1
                );

                $where = 'id = "'.$cacada->id.'"';

                $core->update('cacadas', $campos, $where);

                $gold_per = $this->getPontosPersonagem($cacada->idPersonagem);
                $exp_per = $this->getExpPersonagem($cacada->idPersonagem);
                
                $sql = "SELECT * FROM usuarios_personagens WHERE id = '$cacada->idPersonagem'";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $up = $stmt->fetch();

                $campos_personagem = array(
                    'gold' => intval($up->gold) + intval($cacada->gold),
                    'gold_total' => intval($up->gold_total) + intval($cacada->gold),
                    'exp' => $cacada->exp + $exp_per
                );

                $where_personagem = 'id = "'.$cacada->idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
                
                $campos_ganho = array(
                    'idPersonagem' => $cacada->idPersonagem,
                    'gold' => intval($cacada->gold),
                    'exp' => $cacada->exp
                );

                $core->insert('personagens_new_valores', $campos_ganho);
                
                $person = $core->getDados('usuarios_personagens', "WHERE id = ".$cacada->idPersonagem);
                
                $treino->viewNewLevel($person->id, $person->nivel, $person->exp);
            }
        }
    }
    
    public function cacadaEsgotada($idPersonagem, $tempo, $vip){
    $core = new Core();
    
    // Define daily limits
    if($vip == 1){
        $limite_diario = 120; // 2 hours for VIP
    } else {
        $limite_diario = 60; // 1 hour for non-VIP
    }
    
    // Get total hunt time used TODAY for THIS specific character
    $sql = "SELECT SUM(tempo) as total_usado 
            FROM cacadas 
            WHERE idPersonagem = $idPersonagem 
            AND data = CURDATE() 
            AND cancelada = 0";
    $stmt = DB::prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch();
    
    $tempo_usado = $resultado->total_usado ? intval($resultado->total_usado) : 0;
    $tempo_solicitado = intval($tempo);
    
    // Check if requested time + used time exceeds daily limit
    if(($tempo_usado + $tempo_solicitado) > $limite_diario){
        return true; // Exhausted
    }
    
    return false; // OK to hunt
}
    
    public function cacadasRun($idUsuario, $idPersonagem){
    $core = new Core();
    
    // Only get active hunts for THIS specific character
    $sql = "SELECT * FROM cacadas 
            WHERE idUsuario = '$idUsuario' 
            AND idPersonagem = $idPersonagem 
            AND concluida = 0 
            AND cancelada = 0
            AND tempo_final > ".time();
    $stmt = DB::prepare($sql);
    $stmt->execute();
    
    if($stmt->rowCount() > 0){
        $cacada = $stmt->fetch();
        
        // Set session for active hunt
        $_SESSION['cacada'] = true;
        $_SESSION['cacada_id'] = $cacada->id;
    } else {
        // No active hunt for this character, clear session
        if(isset($_SESSION['cacada'])){
            unset($_SESSION['cacada']);
        }
        if(isset($_SESSION['cacada_id'])){
            unset($_SESSION['cacada_id']);
        }
    }
}

    public function missoesRun($idUsuario, $idPersonagem){
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
    
    public function pvpRun($idUsuario, $idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $pvp = $stmt->fetch();
            $_SESSION['pvp'] = true;
            $_SESSION['pvp_id'] = $pvp->id;  
        }
    }
    
    public function npcRun($idUsuario, $idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM npc_batalhas WHERE idPersonagem = $idPersonagem AND concluida = 0 AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $npc = $stmt->fetch();
            $_SESSION['npc'] = true;
            $_SESSION['npc_id'] = $npc->id;  
        }
    }
    
    
    public function verificaCacadaCancelada($idCacada){
        $core = new Core();
        
        $sql = "SELECT * FROM cacadas WHERE id = '$idCacada' AND cancelada = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function contadorCacada($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM cacadas WHERE idPersonagem = $idPersonagem AND concluida = 0 AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $cacada = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($cacada->tempo_final > time()){
                $restante = $cacada->tempo_final - time();
                echo $restante;
            }
        }
    }
    
    public function contadorPVP($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $pvp = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($pvp->time_final > time()){
                $restante = $pvp->time_final - time();
                echo $restante;
            }
        }
    }
    
    public function contadorBatalha($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $pvp = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($pvp->time_ataque > time()){
                $restante = $pvp->time_ataque - time();
                echo $restante;
            }
        }
    }
    
    public function contadorPunicao($idAdversario){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idAdversario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $pvp = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($pvp->time_defesa > time()){
                $restante = $pvp->time_defesa - time();
                echo $restante;
            }
        }
    }
    
    public function getPontosPersonagem($id){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return intval($item->gold);
    }
    
    public function getExpPersonagem($id){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return intval($item->exp);
    }
    
    public function getGraduacao($nivel, $new_lv = 0){   
             
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();
        
        $class = '';
        
        if($new_lv == 1){
            $class = 'pulse';
        }

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                echo '<img class="'.$class.'" src="'.BASE.'assets/'.$value->emblema.'" alt="'.$value->graduacao.'" />';
            } 
        }
    }
    
    public function getGraduacaoNumber($nivel){        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                return $value->id;
            } 
        }
    }
    
    public function getGraduacaoBatalha($nivel){        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                return '<img src="'.BASE.'assets/'.$value->emblema.'" alt="'.$value->graduacao.'" />';
            } 
        }
    }
    
    public function getGraduacaoTexto($nivel){        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                echo '<span>'.$value->graduacao.'</span>';
            } 
        }
    }
    
    public function getGraduacaoTextoBatalha($nivel){        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                return '<span>'.$value->graduacao.'</span>';
            } 
        }
    }
    
    public function getGraduacaoTextoByID($id){        
        $sql = "SELECT * FROM graduacoes WHERE id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetch();

        return $graduacao->graduacao;
    }
    
    public function getGraduacaoName($nivel){        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                return $value->graduacao;
            } 
        }
    }
    
    public function upaGraduacao($graduacao_anterior, $idPersonagem, $nivel, $vip){
        $core = new Core();
        $inventario = new Inventario();
        
        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();

        foreach ($graduacao as $key => $value) {
            if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
                $grad = $value->id;
            } 
        }
        
        if($graduacao_anterior < $grad){
            $campos = array(
                'graduacao' => $grad
            );

            $where = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $campos, $where);
            
            $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $meu_personagem = $stmt->fetch();
            
            if($grad > 1 && $grad <= 3){
                $bau = 36;
            } else if($grad > 3 && $grad <= 6){
                $bau = 37;
            } else if($grad > 6 && $grad <= 9){
                $bau = 38;
            } else if($grad > 9 && $grad <= 12){
                $bau = 39;
            } else if($grad > 12 && $grad <= 15){
                $bau = 41;
            } else if($grad > 15 && $grad <= 18){
                $bau = 40;
            }
            
            $sql = "SELECT * FROM itens WHERE id = $bau";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados_bau = $stmt->fetch();
            
            if($vip == 1){
                if($inventario->verificaItemIgual($dados_bau->nome, $idPersonagem)){
                    $slot_1 = $inventario->verificaItemIgual($dados_bau->nome, $idPersonagem);
                }
                
                if($inventario->verificaItemIgual($dados_bau->nome, $idPersonagem)){
                    $slot_2 = $inventario->verificaItemIgual($dados_bau->nome, $idPersonagem);
                }
                
                $campos_insert_bau_1 = array(
                    'idPersonagem' => $idPersonagem,
                    'idSlot' => $slot_1,
                    'idItem' => $bau
                );

                $core->insert('personagens_inventario_itens', $campos_insert_bau_1);
                
                $campos_insert_bau_2 = array(
                    'idPersonagem' => $idPersonagem,
                    'idSlot' => $slot_2,
                    'idItem' => $bau
                );

                $core->insert('personagens_inventario_itens', $campos_insert_bau_2);
                
                $campos_insert_premio = array(
                    'idMissao' => 1,
                    'idItem' => $bau,
                    'idPersonagem' => $idPersonagem,
                    'visualizado' => 0   
                );

                $core->insert('personagens_missoes_premios', $campos_insert_premio);
                
                $core->insert('personagens_missoes_premios', $campos_insert_premio);
            } else {
                if($inventario->verificaItemIgual($dados_bau->nome, $idPersonagem)){
                    $slot_1 = $inventario->verificaItemIgual($dados_bau->nome, $idPersonagem);
                }
                
                $campos_insert_bau_1 = array(
                    'idPersonagem' => $idPersonagem,
                    'idSlot' => $slot_1,
                    'idItem' => $bau
                );

                $core->insert('personagens_inventario_itens', $campos_insert_bau_1);
                
                $campos_insert_premio = array(
                    'idMissao' => 1,
                    'idItem' => $bau,
                    'idPersonagem' => $idPersonagem,
                    'visualizado' => 0   
                );

                $core->insert('personagens_missoes_premios', $campos_insert_premio);
            }
        }
    }
    
    public function verificaFoto($foto, $idPersonagem){
        $sql = "SELECT * FROM usuarios_personagens_fotos WHERE foto = '$foto' AND idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }


    public function getNewValores($idPersonagem){
        $sql = "SELECT * FROM personagens_new_valores WHERE idPersonagem = '$idPersonagem' AND visualizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getListaNewValores($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_new_valores WHERE idPersonagem = '$idPersonagem' AND visualizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '<div class="infos">';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                if($value->gold > 0){
                    $row .= '<span>Você Ganhou + <strong>'.$value->gold.'</strong> de Gold.</span>';
                }

                if($value->exp > 0){
                    $row .= '<span>Você Aumentou em <strong>'.$value->exp.'</strong> sua Experiência.</span>';
                }
            }
        }
        $row .= '</div>';
        
        $row .= '<div style="margin-top: 20px; text-align: center;">
            <button id="confirmarGanho" class="bts-form">CONFIRMAR</button>
        </div>';
        
        echo $row;
    }
    
    public function confirmaGanho($idPersonagem, $vip){
        $core = new Core();
        
        $campos = array(
            'visualizado' => 1
        );

        $where = 'idPersonagem = "'.$idPersonagem.'"';

        $core->update('personagens_new_valores', $campos, $where);
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $up = $stmt->fetch();
        
        $this->upaGraduacao($up->graduacao, $idPersonagem, $up->nivel, $vip);
    }
    
    public function getListNewPhotos($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios_personagens_fotos WHERE idPersonagem = $idPersonagem AND visualizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $identificador = str_replace('.', '-', $value->foto);
            
            $row .= '<div class="backdrop-game"></div>
                     <div class="nova-foto">
                        <img id="'.$identificador.'" src="'.BASE.'assets/cards/'.$value->foto.'" />
                        <span>Parabéns, o item foi adquirido com sucesso!</span>
                        <button id="confirmarFoto">OK</button>
                     </div>';
        }
        
        echo $row;
    }
    
    public function setViewFotos($idPersonagem){
        $core = new Core();
        
        $campos = array(
            'visualizado' => '1'
        );
            
        $where = "idPersonagem = ".$idPersonagem;

        $core->update('usuarios_personagens_fotos', $campos, $where);
    }
    
    public function getRanking($tipo, $planeta, $pc, $qtd_resultados){
        $user = new Usuarios();
        $user->getUserInfo($_SESSION['username']);
        $core = new Core();

        //Paginando os Resultados
        $counter = $core->counterRegisters("usuarios_personagens", "WHERE nivel > 1");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql_planeta = '';
        $sql_missoes = '';
        $orderBY = "ORDER BY up.nivel DESC, up.vitorias_pvp DESC, up.tam DESC, up.gold_total DESC";
        
        if($planeta != null){
            $sql_planeta = "AND up.idPlaneta = $planeta ";
        }
        
        if($tipo != ''){
            if($tipo == 2){
                $orderBY = "ORDER BY up.vitorias_pvp DESC, up.nivel DESC, up.tam DESC, up.gold_total DESC";
            } else if($tipo == 4){
                $orderBY = "ORDER BY up.tam DESC, up.vitorias_pvp DESC, up.nivel DESC, up.gold_total DESC";
            }
        }
        
        $sql = "SELECT "
            . "up.*, up.id as idP, up.foto as foto_personagem, "
            . "u.*, "
            .$sql_missoes
            . "up.nome as nome_guerreiro, "
            . "p.nome as planeta, p.imagem as img_planeta "
            . "FROM usuarios_personagens as up "
            . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
            . "INNER JOIN planetas as p ON up.idPlaneta = p.id "
            . "WHERE nivel > 1 "
            . $sql_planeta
            . $orderBY." LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($pc == 1){
            $rank = 0;
        } else {
            $rank = $inicio;
        }
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();
            
            foreach ($item as $key => $value) {
                
                $rank++;
                
                if($rank == 1){
                    $top = 'top-player';
                } else {
                    $top = '';
                }
                
                $ft = str_replace('cards/', '', $value->foto_personagem);

                $row .= '<tr class="'.$top.'">
                            <td><strong>'.$rank.'º</strong></td>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$value->nome_guerreiro.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <strong>'.$value->nome_guerreiro.'</strong>
                                </a>
                            </td>
                            <td width="250" style="text-align: center; vertical-align: middle;">
            '.$this->getGraduacaoBadgeRanking($value->nivel).'
         </td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->vitorias_pvp.'</td>
                            <td>'.$this->getDerrotasPVP($value->idP).'</td>
                            <td>'.$value->tam.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$user->isGuerreiroOnline($value->idP).'</td>
                            <td class="planeta">
                                <img src="'.BASE.'assets/'.$value->img_planeta.'" alt="'.$value->planeta.'" />
                                <span>'.$value->planeta.'</span>
                            </td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="11" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
           $row .= '<tr>'
                   . '<td colspan="9">Ranking não encontrado com o filtro selecionado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getDerrotasPVP($idPersonagem){
        $total = 0;
        
        $sql = "SELECT count(*) as total FROM pvp WHERE idPersonagem = $idPersonagem AND vencedor = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $desafiou = $stmt->fetch();
        
        $total = $desafiou->total;
        
        $sql = "SELECT count(*) as total FROM pvp WHERE idDesafiado = $idPersonagem AND vencedor = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $desafiado = $stmt->fetch();
        
        $total += $desafiado->total;
        
        return $total;
    }

    public function getRankingFront(){
        $user = new Usuarios();
        $core = new Core();
        
        $orderBY = "ORDER BY up.nivel DESC, up.vitorias_pvp DESC, up.tam DESC, up.gold_total DESC";
        
        $sql = "SELECT "
            . "up.*, up.id as idP, up.foto as foto_personagem, "
            . "u.*, "
            . "up.nome as nome_guerreiro, "
            . "p.nome as planeta, p.imagem as img_planeta "
            . "FROM usuarios_personagens as up "
            . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
            . "INNER JOIN planetas as p ON up.idPlaneta = p.id "
            . $orderBY." LIMIT 10";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';

        $rank = 0;
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();
            
            foreach ($item as $key => $value) {
                
                $rank++;
                
                if($rank == 1){
                    $top = 'top-player';
                } else {
                    $top = '';
                }
                
                $ft = str_replace('cards/', '', $value->foto_personagem);

                $row .= '<tr class="'.$top.'">
                            <td class="rank"><strong>'.$rank.'º</strong></td>
                            <td>
                                <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$value->nome_guerreiro.'" />
                            </td>
                            <td width="250">
                                <strong>'.$value->nome_guerreiro.'</strong>
                            </td>
                            <td width="250">'.$this->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td class="planeta">
                                <img src="'.BASE.'assets/'.$value->img_planeta.'" alt="'.$value->planeta.'" />
                                <span>'.$value->planeta.'</span>
                            </td>
                         </tr>';
            }
            
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Nenhum guerreiro cadastrado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }

    public function getGraduacaoBadgeRanking($nivel){        
    $sql = "SELECT * FROM graduacoes";
    $stmt = DB::prepare($sql);
    $stmt->execute();
    $graduacao = $stmt->fetchAll();

    foreach ($graduacao as $key => $value) {
        if($nivel >= $value->level_inicial && $nivel <= $value->level_final){
            return '<img src="'.BASE.'assets/'.$value->emblema.'" 
                         alt="'.$value->graduacao.'" 
                         title="'.$value->graduacao.'"
                         style="width: 70px; height: 70px; object-fit: contain; 
                                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
                                transition: transform 0.2s;"
                         onmouseover="this.style.transform=\'scale(1.1)\'" 
                         onmouseout="this.style.transform=\'scale(1)\'" />';
        } 
    }
    
    return '';
}

    
    public function verificaGraduacao($level){
        $core = new Core();

        $sql = "SELECT * FROM graduacoes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $graduacao = $stmt->fetchAll();
        
        $txt_graduacao = '';

        foreach ($graduacao as $key => $value) {
            if($level >= $value->level_inicial && $level <= $value->level_final){
                $txt_graduacao = $value->graduacao;
            } 
        }
        
        return $txt_graduacao;
    }
    
    public function printGoldsCacada($nivel){
        $sql = "SELECT * FROM cacadas_gold";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $table_exp = $stmt->fetchAll();

        foreach ($table_exp as $key => $value) {
            $exp = $value->ganho_exp;
            
            if($nivel >= $value->exp_inicial && $nivel <= $value->exp_final){  
                echo '<strong>10 minutos (Membros VIP 5 minutos)</strong> = Você ganha '.$value->time_10.' de Gold e '.$exp.' de Exp.';
                echo '<br>';
                echo '<strong>20 minutos (Membros VIP 10 minutos)</strong> = Você ganha '.$value->time_20.' de Gold e '.($exp * 2).' de Exp.';
                echo '<br>';
                echo '<strong>30 minutos (Membros VIP 15 minutos)</strong> = Você ganha '.$value->time_30.' de Gold e '.($exp * 3).' de Exp.';
                echo '<br>';
                echo '<strong>40 minutos (Membros VIP 20 minutos)</strong> = Você ganha '.$value->time_40.' de Gold e '.($exp * 4).' de Exp.';
                echo '<br>';
                echo '<strong>50 minutos (Membros VIP 25 minutos)</strong> = Você ganha '.$value->time_50.' de Gold e '.($exp * 5).' de Exp.';
                echo '<br>';
                echo '<strong>60 minutos (Membros VIP 30 minutos)</strong> = Você ganha '.$value->time_60.' de Gold e '.($exp * 6).' de Exp.';
            } 
        }
    }
    
    public function getAllFotosPersonagem($idPersonagem, $foto_atual, $vip, $graduacao, $boneco, $idUsuario){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_fotos WHERE idPersonagem = $boneco AND status = 1 ORDER BY free DESC, raridade ASC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $fotos_personagem = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($fotos_personagem as $key => $value) {
            $class = '';
            $bloqueada = '';
            
            if($value->raridade == 1){
                $class = 'verde';
            } else if($value->raridade == 2){
                $class = 'azul';
            } else if($value->raridade == 3){
                $class = 'roxo';
            } else if($value->raridade == 4){
                $class = 'laranja';
            }
            
            if($value->free == 1){
                $identificador = str_replace('.', '-', $value->foto);
                
                $row .= '<li dataImage="'.$value->foto.'" id="'.$identificador.'-1" class="'.$class.'">';

                    if($foto_atual == $value->foto){
                        $row .= '<i class="fas fa-check-circle"></i>';
                    }
                $row .= '<img src="'.BASE.'assets/cards/'.$value->foto.'" alt="Foto" />
                         </li>';
            } else {
                $sql = "SELECT * FROM usuarios_personagens_fotos WHERE idUsuario = $idUsuario AND foto = '$value->foto'";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $encontrouVip = $stmt->fetch();
                
                if($encontrouVip == false){
                    $bloqueada = 'bloqueado';
                }
                
                $identificador = str_replace('.', '-', $value->foto);
                
                $row .= '<li dataImage="'.$value->foto.'" id="'.$identificador.'-1" class="'.$bloqueada.' '.$class.'">'; 
                
                            if($encontrouVip == false){
                                $row .= '<a href="'.BASE.'loja">
                                            <div class="imagem-bloqueada">
                                                <i class="fas fa-lock"></i>
                                                Em Breve na Loja de itens
                                                <span class="txt-graduacao">Verifique a Disponibilidade</span>
                                            </div>
                                        </a>';
                            }
                
                            if($foto_atual == $value->foto){
                                $row .= '<i class="fas fa-check-circle"></i>';
                            }
                $row .= '<img src="'.BASE.'assets/cards/'.$value->foto.'" alt="Foto" />
                         </li>';
            }
        } 
        
        echo $row;
    }
    
    public function getListCharacters($perfil){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens ORDER BY nome ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $row .= '<div class="guerreiro">
                        <div class="info">
                            <h3>'.$value->titulo.' <span>Publicado em '.$core->dataTimeBR($value->data_hora).'</span></h3>
                            <p>'.$value->descricao.'</p>';
                            if($perfil == 3){
                                $row .= '<a href="'.BASE.'noticias/edit/'.$value->id.'">[Editar]</a>';
                            }
            $row .= '</div>
                        <img src="'.BASE.'assets/news.jpg" alt="DB Heroes - Notícias" />
                     </div>';
        }
        
        echo $row;
    }
    
    public function getSaldo($valor, $idPersonagem){        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->gold >= $valor){
            return true;
        } else {
            return false;
        }
    }
    
    public function getByName($nome, $planeta){
        $sql_planeta = "";
        
        if($planeta != 4){
            $sql_planeta = "AND idPlaneta = $planeta";
        }
        
        $sql = "SELECT * FROM usuarios_personagens WHERE nome = '$nome' ".$sql_planeta;
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            if($item->time_defesa < time()){
                $id = $item->id;
            } else {
                $id = 0;
            }
        } else {
            $id = 0;
        }
        
        return $id;
    }
    
    public function getAleatorio($tipo, $planeta, $nivel, $id_personagem, $idUser){
        $equipes = new Equipes();
        
        $aux = "";
        
        if($tipo == 1){
            $aux = "AND nivel = $nivel";
        } else {
            if($nivel >= 10){
                $aux = "AND nivel >= 10";
            }
        }
        
        $sql_planeta = "";
        
        if($planeta != 4){
            $sql_planeta = "AND idPlaneta = $planeta";
        }
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id != $id_personagem ".$aux." AND idUsuario != $idUser ".$sql_planeta." ORDER BY RAND() LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        foreach ($item as $key => $value) {
            if(!$equipes->verificaMembrosEquipe($id_personagem, $value->id)){
                if($value->time_defesa < time()){
                    $id = $value->id;
                } else {
                    $id = 0;
                }
            } else {
                $id = 0;
            }
        }
        
        return $id;
    }
    
    public function verificaPersonagem($idPersonagem, $idUsuario){        
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem AND idUsuario = $idUsuario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getAmigos($idPersonagem){
        $user = new Usuarios();
        
        $orderBY = "ORDER BY up.nivel DESC, up.vitorias_pvp DESC, up.tam DESC, up.gold_total DESC";
        
        $sql = "SELECT "
            . "up.*, up.id as idP, pa.id as idAmizade, up.foto as foto_personagem, "
            . "u.*, "
            . "up.nome as nome_guerreiro, "
            . "pa.aceitou, "
            . "p.nome as planeta, p.imagem as img_planeta "
            . "FROM personagens_amigos as pa "
            . "INNER JOIN usuarios_personagens as up ON up.id = pa.idAmigo "
            . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
            . "INNER JOIN planetas as p ON up.idPlaneta = p.id "
            . "WHERE pa.idPersonagem = $idPersonagem "
            . $orderBY;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $ft = str_replace('cards/', '', $value->foto_personagem);

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$value->nome_guerreiro.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <strong>'.$value->nome_guerreiro.'</strong>
                                </a>
                            </td>
                            <td width="250">'.$this->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->vitorias_pvp.'</td>
                            <td>'.$value->derrotas_pvp.'</td>
                            <td>'.$value->tam.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$user->isGuerreiroOnline($value->idP).'</td>';
                            if($value->aceitou == 1){
                                $row .= '<td class="pendente" title="Desfazer Amizade">
                                            <form id="deletarAmizade" method="post">
                                                <input type="hidden" name="deletar" value="'.$value->idAmizade.'" />
                                                <button type="submit" style="border: 0; background: none;" title="Desfazer Amizade?">
                                                    <i class="fa fa-trash"></i>
                                                </button> 
                                            </form>
                                         </td>';
                            } else {
                                $row .= '<td class="pendente" title="Pendente">
                                            <i class="fas fa-exclamation"></i>
                                         </td>';
                            }
                            $row .= '</tr>';
            }
        }
        
        $sql = "SELECT "
            . "up.*, up.id as idP, pa.id as idAmizade, up.foto as foto_personagem, "
            . "u.*, "
            . "up.nome as nome_guerreiro, "
            . "pa.aceitou, "
            . "p.nome as planeta, p.imagem as img_planeta "
            . "FROM personagens_amigos as pa "
            . "INNER JOIN usuarios_personagens as up ON up.id = pa.idPersonagem "
            . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
            . "INNER JOIN planetas as p ON up.idPlaneta = p.id "
            . "WHERE pa.idAmigo = $idPersonagem "
            . $orderBY;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $item_amigos = $stmt->fetchAll();

            foreach ($item_amigos as $key => $value) {
                $ft = str_replace('cards/', '', $value->foto_personagem);

                $row .= '<tr class="'.$top.'">
                            <td>
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$value->nome_guerreiro.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <strong>'.$value->nome_guerreiro.'</strong>
                                </a>
                            </td>
                            <td width="250">'.$this->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->vitorias_pvp.'</td>
                            <td>'.$value->derrotas_pvp.'</td>
                            <td>'.$value->tam.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$user->isGuerreiroOnline($value->idP).'</td>';
                            if($value->aceitou == 0){
                                $row .= '<td class="aprovado">
                                            <form id="confirmarAmizade" method="post">
                                                <input type="hidden" name="aceitar" value="'.$value->idAmizade.'" />
                                                <button type="submit" style="border: 0; background: none;" title="Confirmar Amizade?">
                                                    <i class="fas fa-check"></i>
                                                </button> 
                                             </form>
                                         </td>';
                            } else {
                                $row .= '<td class="pendente">
                                            <form id="deletarAmizade" method="post">
                                                <input type="hidden" name="deletar" value="'.$value->idAmizade.'" />
                                                <button type="submit" style="border: 0; background: none;" title="Desfazer Amizade?">
                                                    <i class="fa fa-trash"></i>
                                                </button> 
                                             </form>
                                         </td>';
                            }
                         $row .= '</tr>';
            }
        }
        
        if($row == ''){
            $row .= '<tr>'
                   . '<td colspan="10">Nenhum amigo adicionado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getAmigosPending($idPersonagem){
        $sql = "SELECT count(*) as total FROM personagens_amigos WHERE idAmigo = $idPersonagem AND aceitou = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $amigos = 0;
        
        if($stmt->rowCount() > 0){
            $am = $stmt->fetch();
            $amigos = $am->total;
        } else {
            $amigos = 0;
        }
        
        return $amigos;
    }
    
    public function getExisteAmizade($idPersonagem, $idAmigo){
        $sql = "SELECT * FROM personagens_amigos WHERE idAmigo = $idAmigo AND idPersonagem = $idPersonagem AND aceitou = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            $sql = "SELECT * FROM personagens_amigos WHERE idAmigo = $idPersonagem AND idPersonagem = $idAmigo AND aceitou = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getExisteSolicitacaoAmizade($idPersonagem, $idAmigo){
        $sql = "SELECT * FROM personagens_amigos WHERE idAmigo = $idAmigo AND idPersonagem = $idPersonagem AND aceitou = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            $sql = "SELECT * FROM personagens_amigos WHERE idAmigo = $idPersonagem AND idPersonagem = $idAmigo AND aceitou = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getListGraduacoes($pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("graduacoes");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM graduacoes LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        $lida = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                
                $row .= '<tr style="text-align: center;">
                            <td><img src="'.BASE.'assets/'.$value->emblema.'" /></td>
                            <td>'.$value->graduacao.'</td>
                            <td>'.$value->level_inicial.'</td>
                            <td style="display: inline-block; vertical-align: middle;font-size: 18px; margin-top: 35px; padding: 5px;color: #00911d;border: 1px solid #00911d;">+ '.$value->status_extra.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="4" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="4" class="not">Nenhuma graduação cadastrada.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function getListExperiencia($pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("level");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM level LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        $lida = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<tr>
                            <td class="level">'.$value->level.'</td>
                            <td>'.$value->exp.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="2" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="2" class="not">Nenhum level cadastrado.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function setLog($idUsuario, $idPersonagem, $idProduto, $log, $valor){
        $core = new Core();
        
        $campos = array(
            'idUsuario' => $idUsuario,
            'idPersonagem' => $idPersonagem,
            'idProduto' => $idProduto,
            'data' => date('Y-m-d H:i:s'),
            'valor' => $valor,
            'log' => $log
        );
        
        $core->insert('usuarios_personagens_log', $campos);
    }
    
    public function getTotalPvpIndividual($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT sum(vitorias_pvp) as total FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCacadaRunning($idPersonagem, $idCacada){
        $core = new Core();
        
        // Get hunt details
        $sql = "SELECT * FROM cacadas 
                WHERE id = $idCacada 
                AND idPersonagem = $idPersonagem
                AND concluida = 0 
                AND cancelada = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $cacada = $stmt->fetch();
            $tempo_restante = $cacada->tempo_final - time();
            
            if($tempo_restante > 0){
                // Hunt still running - ORIGINAL HORIZONTAL LAYOUT
                $row = '<div class="cacada-running">
                            <span>Você está em uma caçada, aguarde o tempo terminar para iniciar missões, arena e caçadas.</span>
                            <button id="cancelarCacada" data-cacada-id="'.$idCacada.'">CANCELAR CAÇADA</button>
                            <div class="contador"></div>
                        </div>';
                return $row;
            } else {
                // Hunt finished! Complete it now
                
                // Mark as completed
                $campos_cacada = array('concluida' => 1);
                $where_cacada = 'id = ' . $cacada->id;
                $core->update('cacadas', $campos_cacada, $where_cacada);
                
                // Get character data
                $sql_char = "SELECT * FROM usuarios_personagens WHERE id = " . $cacada->idPersonagem;
                $stmt_char = DB::prepare($sql_char);
                $stmt_char->execute();
                $char_data = $stmt_char->fetch();
                
                // Calculate rewards
                $gold_ganho = intval($cacada->gold);
                $exp_ganho = intval($cacada->exp);
                
                // Update character gold and exp
                $novo_gold = intval($char_data->gold) + $gold_ganho;
                $novo_gold_total = intval($char_data->gold_total) + $gold_ganho;
                $novo_exp = intval($char_data->exp) + $exp_ganho;
                
                $campos_personagem = array(
                    'gold' => $novo_gold,
                    'gold_total' => $novo_gold_total,
                    'exp' => $novo_exp
                );
                $where_personagem = 'id = ' . $cacada->idPersonagem;
                $core->update('usuarios_personagens', $campos_personagem, $where_personagem);
                
                // Insert rewards notification
                $campos_valor = array(
                    'idPersonagem' => $cacada->idPersonagem,
                    'gold' => $gold_ganho,
                    'exp' => $exp_ganho,
                    'visualizado' => 0
                );
                $core->insert('personagens_new_valores', $campos_valor);
                
                // Check for level up
                $this->checkLevelUp($cacada->idPersonagem);
                
                // Clear session
                unset($_SESSION['cacada']);
                unset($_SESSION['cacada_id']);
                
                // Return empty (rewards popup will show on page reload)
                return '';
            }
        }
        
        return '';
    }

    
    public function getMissaoRunning($idPersonagem, $idMissao){
        $core = new Core();
        
        // Get mission details
        $sql = "SELECT * FROM missoes 
                WHERE id = $idMissao 
                AND idPersonagem = $idPersonagem
                AND status = 'ativa'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $missao = $stmt->fetch();
            
            $time_atual = time();
            $tempo_restante = $missao->tempo_final - $time_atual;
            
        // CHECK IF MISSION ALREADY FINISHED (missed completion)
        if($tempo_restante <= 0){
            // Mission finished! Process rewards NOW
            
            // Mark mission as completed
            $campos = array('status' => 'concluida', 'data_conclusao' => date('Y-m-d H:i:s'));
            $where = 'id = ' . $missao->id;
            $core->update('missoes', $campos, $where);
            
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
                
                // Calculate rewards
                $gold_ganho = intval($mission_data->recompensa_ouro ?? 100);
                $exp_ganho = intval($mission_data->experiencia ?? 50);
                
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
                
                // Insert rewards notification
                $campos_valor = array(
                    'idPersonagem' => $missao->idPersonagem,
                    'gold' => $gold_ganho,
                    'exp' => $exp_ganho,
                    'tipo' => 'missao',
                    'visualizado' => 0
                );
                $core->insert('personagens_new_valores', $campos_valor);

                
                // Check for level up
                $this->checkLevelUp($missao->idPersonagem);
            }
            
            // Clear session
            unset($_SESSION['missao']);
            unset($_SESSION['missao_id']);
            
            // DON'T reload - just return empty (mission finished)
            return '';
        }

            
            // SAME STYLE AS CACADA - Full width banner at top
            $row = '<div class="missao-running">
                        <input type="hidden" id="idMissao" value="'.$idMissao.'" />
                        <span>Você está em uma missão, aguarde o tempo terminar para iniciar outras missões, arena e caçadas.</span>
                        <button class="bts-form" id="cancelarMissao">CANCELAR</button>
                        <div class="contador">00:00:00</div>
                    </div>';
            
            return $row;
        }
        
        return '';
    }


    public function createInventorySlots($idPersonagem){
        try {
            // 30 inventory slots
            for ($i = 1; $i <= 30; $i++) {
                $sql = "INSERT INTO personagens_inventario (idPersonagem, slot, vazio) VALUES (:idPersonagem, :slot, 1)";
                $stmt = DB::prepare($sql);
                $stmt->bindParam(':idPersonagem', $idPersonagem, PDO::PARAM_INT);
                $stmt->bindParam(':slot', $i, PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    error_log("Failed to create inventory slot $i: " . implode(', ', $stmt->errorInfo()));
                    throw new Exception("Failed inventory slot $i");
                }
            }
            // 8 equipped slots
            for ($i = 1; $i <= 8; $i++) {
                $emblema = ($i <= 3) ? 1 : 0;
                $sql = "INSERT INTO personagens_itens_equipados (idPersonagem, slot, vazio, adesivo, emblema) VALUES (:idPersonagem, :slot, 1, 0, :emblema)";
                $stmt = DB::prepare($sql);
                $stmt->bindParam(':idPersonagem', $idPersonagem, PDO::PARAM_INT);
                $stmt->bindParam(':slot', $i, PDO::PARAM_INT);
                $stmt->bindParam(':emblema', $emblema, PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    error_log("Failed to create equipped slot $i: " . implode(', ', $stmt->errorInfo()));
                    throw new Exception("Failed equipped slot $i");
                }
            }
            // 10 sticker slots
            for ($i = 9; $i <= 18; $i++) {
                $sql = "INSERT INTO personagens_itens_equipados (idPersonagem, slot, vazio, adesivo, emblema) VALUES (:idPersonagem, :slot, 1, 1, 0)";
                $stmt = DB::prepare($sql);
                $stmt->bindParam(':idPersonagem', $idPersonagem, PDO::PARAM_INT);
                $stmt->bindParam(':slot', $i, PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    error_log("Failed to create sticker slot $i: " . implode(', ', $stmt->errorInfo()));
                    throw new Exception("Failed sticker slot $i");
                }
            }
            error_log("Created all inventory/equipment/sticker slots for character $idPersonagem");
            return true;
        } catch (Exception $e) {
            error_log("Error creating inventory slots: " . $e->getMessage());
            return false;
        }
    }

    
    public function checkLevelUp($idPersonagem){
        $core = new Core();
        
        // Get character current data
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $char = $stmt->fetch();
            
            // Get required EXP for next level
            $sql_level = "SELECT * FROM level WHERE level = " . ($char->nivel + 1);
            $stmt_level = DB::prepare($sql_level);
            $stmt_level->execute();
            
            if($stmt_level->rowCount() > 0){
                $next_level = $stmt_level->fetch();
                
                // Check if character has enough EXP to level up
                if($char->exp >= $next_level->exp){
                    // Level up!
                    $novo_nivel = $char->nivel + 1;
                    
                    // Update character level and reset EXP
                    $campos = array(
                        'nivel' => $novo_nivel,
                        'exp' => $char->exp - $next_level->exp,
                        'hp' => intval($char->hp) + 50,
                        'mana' => intval($char->mana) + 50,
                        'forca' => intval($char->forca) + 1,
                        'agilidade' => intval($char->agilidade) + 1,
                        'habilidade' => intval($char->habilidade) + 1,
                        'resistencia' => intval($char->resistencia) + 1,
                        'sorte' => intval($char->sorte) + 1
                    );
                    $where = 'id = ' . $idPersonagem;
                    $core->update('usuarios_personagens', $campos, $where);
                    
                    // Set session flag for level up notification
                    $_SESSION['novo_nivel'] = true;
                    $_SESSION['nivel_atual'] = $novo_nivel;
                    
                    return true;
                }
            }
        }
        
        return false;
    }
}
