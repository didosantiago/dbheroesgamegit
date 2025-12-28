<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Equipes
 *
 * @author Dido Santiago
 */
class Equipes {
    /**
     * Retorna todas as bandeiras disponíveis para equipes
     * @param string $tipo - 'all', 'gratis', 'premium', 'vip'
     * @return array
     */
    public function getBandeiras($tipo = 'all') {
        try {
            if($tipo == 'all') {
                $sql = "SELECT * FROM equipes_bandeiras WHERE ativo = 1 ORDER BY tipo, preco";
                $stmt = DB::prepare($sql);
            } else {
                $sql = "SELECT * FROM equipes_bandeiras WHERE tipo = :tipo AND ativo = 1 ORDER BY preco";
                $stmt = DB::prepare($sql);
                $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $bandeiras = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $row = "";
            
            if(count($bandeiras) > 0) {
                foreach($bandeiras as $index => $bandeira) {
                    $checked = ($index == 0) ? 'checked' : '';
                    $preco_display = $bandeira->preco == 0 ? 'Grátis' : $bandeira->preco.' coins';
                    $tipo_class = $bandeira->tipo;
                    
                    // Update the radio value to send: "id|imagem"
                    $valor = $bandeira->id . '|' . $bandeira->imagem;

                    $row .= '
                    <label class="bandeira-item '.$tipo_class.'">
                        <input type="radio" name="idBandeira" value="'.$valor.'" '.$checked.' required>
                        <div class="bandeira-card">
                            <img src="'.BASE.'assets/equipes/'.$bandeira->imagem.'" alt="'.$bandeira->nome.'">
                            <span class="bandeira-nome">'.$bandeira->nome.'</span>
                            <span class="bandeira-preco">'.$preco_display.'</span>
                        </div>
                    </label>';

                }
            } else {
                $row = '<p style="color: #fff; text-align: center;">Nenhuma bandeira disponível</p>';
            }
            
            echo $row;
            return $bandeiras;
            
        } catch(PDOException $e) {
            error_log("Erro getBandeiras: " . $e->getMessage());
            echo '<p style="color: #ff6b6b;">Erro ao carregar bandeiras: '.$e->getMessage().'</p>';
            return [];
        }
    }




    
    public function existsSigla($sigla){
        $sql = "SELECT * FROM equipes WHERE sigla = '$sigla'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function existsEquipe($idPersonagem){
        if($idPersonagem != ''){
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idPersonagem AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();

            $existe = 0;

            if($stmt->rowCount() > 0){
                $existe = 1;
            }

            $sql = "SELECT * FROM equipes WHERE idCriador = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $existe = 1;
            }

            if($existe == 1){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function dadosEquipeAtual($idMembro, $idEquipe = '') {
        // Add NULL check to prevent SQL syntax error
        if($idMembro === NULL || $idMembro === '') {
            return 0;
        }

        if($idEquipe == '') {
            // FIXED: Use idPersonagem instead of idMembro
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = :idMembro AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':idMembro', $idMembro, PDO::PARAM_INT);
            $stmt->execute();
            $equipeMembros = $stmt->fetch(PDO::FETCH_OBJ);
            
            $idE = '';
            if($stmt->rowCount() > 0) {
                $idE = $equipeMembros->idEquipe;
            } else {
                // Check if user is the creator
                $sql = "SELECT * FROM equipes WHERE idCriador = :idCriador";
                $stmt = DB::prepare($sql);
                $stmt->bindParam(':idCriador', $idMembro, PDO::PARAM_INT);
                $stmt->execute();
                $equipeDados = $stmt->fetch(PDO::FETCH_OBJ);
                
                if($stmt->rowCount() > 0) {
                    $idE = $equipeDados->id;
                }
            }
        } else {
            $idE = $idEquipe;
        }

        if($idE != '') {
            $sql = "SELECT * FROM equipes WHERE id = :id";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $idE, PDO::PARAM_INT);
            $stmt->execute();
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            return $dados;
        } else {
            return 0;
        }
    }


    // Calculate team bonuses based on level
    public function getTeamBonuses($level) {
        $bonuses = [
            'forca' => floor($level * 1.0),      // 0.5 per level
            'agilidade' => floor($level * 1.0),  // 0.5 per level  
            'habilidade' => floor($level * 1.0), // 0.5 per level
            'resistencia' => floor($level * 1.0), // 0.5 per level
            'sorte' => floor($level * 1.0)       // 0.3 per level
        ];
        return $bonuses;
    }


    
    public function existsInEquipe($idMembro){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            $sql = "SELECT * FROM equipes WHERE idCriador = $idMembro ";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function existsInEquipeAtual($id, $idMembro){
        $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $id AND idPersonagem = $idMembro AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            $sql = "SELECT * FROM equipes WHERE id = $id AND idCriador = $idMembro ";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function existsNome($nome){
        $sql = "SELECT * FROM equipes WHERE nome = '$nome'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getInfoEquipe($idEquipe){
        $sql = "SELECT e.*, b.bandeira, u.nome as lider "
             . "FROM equipes as e "
             . "INNER JOIN equipes_bandeiras as b ON b.id = e.idBandeira "
             . "INNER JOIN equipes_membros as m ON m.idEquipe = e.id "
             . "INNER JOIN usuarios_personagens as u ON u.id = m.idPersonagem "
             . "WHERE e.id = $idEquipe";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getTotalMembros($idEquipe){
        $sql = "SELECT count(*) as total FROM equipes_membros WHERE idEquipe = $idEquipe AND status = 1";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getTotalMembrosConvidados($idEquipe){
        $sql = "SELECT count(*) as total FROM equipes_membros WHERE idEquipe = $idEquipe";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getDadosCriador($idCriador){
        $sql = "SELECT * FROM usuarios_personagens WHERE id = $idCriador";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getTotalVitoriasPVP($idEquipe){
        $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $idEquipe AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membros = $stmt->fetchAll();
        
        // Check if there are any members
        if(count($membros) == 0) {
            return 0; // Return 0 if no members
        }
        
        $lista_membros = array();
        foreach ($membros as $chave => $d_membro) {
            array_push($lista_membros, $d_membro->idPersonagem);
        }
        
        $sql = "SELECT sum(vitorias_pvp) as total FROM usuarios_personagens WHERE id in (".implode(",", array_map('intval', $lista_membros)).") ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $vitorias = $stmt->fetch();
        
        return $vitorias->total ?? 0;
    }

    
    public function getTotalVitoriasTAM($idEquipe){
        $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $idEquipe AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membros = $stmt->fetchAll();
        
        // Check if there are any members
        if(count($membros) == 0) {
            return 0; // Return 0 if no members
        }
        
        $lista_membros = array();
        foreach ($membros as $chave => $d_membro) {
            array_push($lista_membros, $d_membro->idPersonagem);
        }
        
        $sql = "SELECT sum(tam) as total FROM usuarios_personagens WHERE id in (".implode(",", array_map('intval', $lista_membros)).") ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $vitorias = $stmt->fetch();
        
        return $vitorias->total ?? 0;
    }

        
    /**
     * Get total gold donated FOR CURRENT LEVEL ONLY
     */
    public function getTotalGold($idEquipe) {
        try {
            // Get current team level
            $sql = "SELECT level FROM equipes WHERE id = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($idEquipe)]);
            $equipe = $stmt->fetch(PDO::FETCH_OBJ);
            
            if(!$equipe) {
                return 0;
            }
            
            // Get gold donated AT current level only
            $sql = "SELECT SUM(valor) as total FROM equipes_doacoes 
                    WHERE idEquipe = ? AND level_quando_doado = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($idEquipe), intval($equipe->level)]);
            $item = $stmt->fetch(PDO::FETCH_OBJ);
            
            if(!$item || !$item->total) {
                return 0;
            }
            
            return intval($item->total);
            
        } catch (PDOException $e) {
            error_log("Error in getTotalGold: " . $e->getMessage());
            return 0;
        }
    }

    
    public function getRanking($idEquipe){
        global $core;
        
        // Validate input
        $idEquipe = intval($idEquipe);
        if($idEquipe <= 0) {
            echo '<tr><td colspan="7" style="text-align:center; padding: 20px;">ID de equipe inválido.</td></tr>';
            return;
        }
        
        $user = new Usuarios();
        $personagem = new Personagens();
        
        try {
            // Get active members
            $sql = "SELECT idPersonagem FROM equipes_membros WHERE idEquipe = :idEquipe AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->bindValue(':idEquipe', $idEquipe, PDO::PARAM_INT);
            $stmt->execute();
            $membros = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            if(empty($membros)) {
                echo '<tr><td colspan="7" style="text-align:center; padding: 20px;">Nenhum membro encontrado.</td></tr>';
                return;
            }
            
            // Build list of member IDs
            $lista_membros = array();
            foreach ($membros as $d_membro) {
                $lista_membros[] = intval($d_membro->idPersonagem);
            }
            
            if(empty($lista_membros)) {
                echo '<tr><td colspan="7" style="text-align:center; padding: 20px;">Nenhum membro encontrado.</td></tr>';
                return;
            }
            
            // Create placeholders for IN clause
            $placeholders = implode(',', array_fill(0, count($lista_membros), '?'));
            
            $orderBY = "ORDER BY up.nivel DESC, up.vitorias_pvp DESC, up.tam DESC, up.gold_total DESC";
            
            $sql = "SELECT 
                        up.*, 
                        up.id as idP, 
                        up.foto as foto_personagem,
                        up.nome as nome_guerreiro,
                        u.*,
                        p.nome as planeta, 
                        p.imagem as img_planeta
                    FROM usuarios_personagens as up
                    INNER JOIN usuarios as u ON u.id = up.idUsuario
                    INNER JOIN planetas as p ON up.idPlaneta = p.id
                    WHERE up.id IN ($placeholders)
                    $orderBY";
            
            $stmt = DB::prepare($sql);
            
            // Bind each member ID
            foreach ($lista_membros as $index => $memberId) {
                $stmt->bindValue($index + 1, $memberId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            $row = '';
            
            if($stmt->rowCount() > 0){
                $item = $stmt->fetchAll(PDO::FETCH_OBJ);
                $rank = 0;
                
                foreach ($item as $value) {
                    $rank++;
                    
                    $top = ($rank == 1) ? 'top-player' : '';
                    
                    // Clean photo path
                    $ft = str_replace('cards/', '', $value->foto_personagem);
                    
                    // Safe output with htmlspecialchars
                    $nomeGuerreiro = htmlspecialchars($value->nome_guerreiro, ENT_QUOTES, 'UTF-8');
                    $goldTotal = number_format($value->gold_total, 0, ',', '.');
                    
                    $row .= '<tr class="'.$top.'">
                                <td><strong>'.$rank.'º</strong></td>
                                <td>
                                    <a href="'.BASE.'publico/'.$value->idP.'">
                                        <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$nomeGuerreiro.'" />
                                    </a>
                                </td>
                                <td width="250">
                                    <a href="'.BASE.'publico/'.$value->idP.'">
                                        <strong>'.$nomeGuerreiro.'</strong>
                                    </a>
                                </td>
                                <td width="250">'.$personagem->verificaGraduacao($value->nivel).'</td>
                                <td>'.$value->nivel.'</td>
                                <td>'.$goldTotal.'</td>
                                <td>'.$user->isGuerreiroOnline($value->idP).'</td>
                            </tr>';
                }
            } else {
            $row .= '<tr>
                        <td colspan="7" style="text-align:center; padding: 20px;">Nenhum membro encontrado.</td>
                        </tr>'; 
            }
            
            echo $row;
            
        } catch (PDOException $e) {
            // Log error and show user-friendly message
            error_log("Error in getRanking(): " . $e->getMessage());
            echo '<tr><td colspan="7" style="text-align:center; padding: 20px; color: #ff6b6b;">
                    <i class="fas fa-exclamation-triangle"></i> Erro ao carregar ranking. Tente novamente.
                </td></tr>';
        }
    }

    
    public function getPendentes($idEquipe, $pc, $qtd_resultados, $idPersonagem){
        $user = new Usuarios();
        $core = new Core();
        $personagem = new Personagens();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes_membros", "WHERE idEquipe = $idEquipe AND status = 0");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT d.*, up.id as idP, up.nome as nome_guerreiro, up.nivel, up.gold_total, up.foto "
             . "FROM equipes_membros as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "WHERE d.idEquipe = $idEquipe "
             . "AND d.status = 0 "
             . "ORDER BY id DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome_guerreiro.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <strong>'.$value->nome_guerreiro.'</strong>
                                </a>
                            </td>
                            <td width="250">'.$personagem->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>';
                                if($this->isLider($idPersonagem, $idEquipe)){ 
                                    $row .= '<a href="'.BASE.'equipes/remove_convite/'.$value->id.'" class="bt-recusar" title="Remover Convite" style="margin: 0 10px;">
                                                <i class="fas fa-minus-circle"></i>
                                             </a>';
                                }
                            $row .= '</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Nenhum Membro Pendente</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getAceitos($idEquipe, $pc, $qtd_resultados, $idPersonagem){
        $user = new Usuarios();
        $core = new Core();
        $personagem = new Personagens();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes_membros", "WHERE idEquipe = $idEquipe AND status = 1");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT d.*, up.id as idP, up.nome as nome_guerreiro, up.nivel, up.gold_total, up.foto "
             . "FROM equipes_membros as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "WHERE d.idEquipe = $idEquipe "
             . "AND d.status = 1 "
             . "ORDER BY id DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome_guerreiro.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->idP.'">
                                    <strong>'.$value->nome_guerreiro.'</strong>
                                </a>
                            </td>
                            <td width="250">'.$personagem->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>';
                                if($this->isLider($idPersonagem, $idEquipe)){ 
                                    $row .= '<a href="'.BASE.'equipes/remove_membro/'.$value->id.'" class="bt-recusar" title="Remover Membro" style="margin: 0 10px;">
                                                <i class="fas fa-minus-circle"></i>
                                             </a>';
                                }
                            $row .= '</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Nenhum Membro Pendente</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getModeradores($idEquipe, $pc, $qtd_resultados){
        $user = new Usuarios();
        $core = new Core();
        $personagem = new Personagens();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes_membros", "WHERE idEquipe = $idEquipe AND status = 1");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT d.*, up.id as idP, up.nome as nome_guerreiro, up.nivel, up.foto, e.idCriador "
             . "FROM equipes_membros as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "INNER JOIN equipes as e ON e.id = d.idEquipe "
             . "WHERE d.idEquipe = $idEquipe "
             . "AND d.status = 1 "
             . "ORDER BY id DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                if($value->idPersonagem != $value->idCriador){
                    $row .= '<tr>
                                <td>
                                    <a href="'.BASE.'publico/'.$value->idP.'">
                                        <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome_guerreiro.'" />
                                    </a>
                                </td>
                                <td width="250">
                                    <a href="'.BASE.'publico/'.$value->idP.'">
                                        <strong>'.$value->nome_guerreiro.'</strong>
                                    </a>
                                </td>
                                <td>'.$value->nivel.'</td>
                                <td>'.$this->isViceLider($value->idPersonagem).'</td>
                                <td>';
                                    if($this->isViceLider($value->idPersonagem) == 'Não'){
                                        $row .= '<a href="'.BASE.'equipes/add_lider/'.$value->id.'" class="bt-aceitar" title="Adicionar como Vice Líder" style="margin-right: 0;">
                                                    <i class="fas fa-check"></i>
                                                 </a>';
                                    } else {
                                        $row .= '<a href="'.BASE.'equipes/remove_lider/'.$value->id.'" class="bt-recusar" title="Remover Vice Líder">
                                                    <i class="fas fa-minus-circle"></i>
                                                 </a>';
                                    }
                                $row .= '</td>
                             </tr>';
                }
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Nenhum Membro Pendente</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function isViceLider($idMembro){        
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1 AND vicelider = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return '<div class="lideranca">
                        <i class="fas fa-crown"></i>
                    </div>';
        } else {
            return 'Não';
        }
    }
    
    public function getMembros($idEquipe){
        $user = new Usuarios();
        $personagem = new Personagens();
        
        $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $idEquipe AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membros = $stmt->fetchAll();
        
        $lista_membros = array();
        
        foreach ($membros as $chave => $d_membro) {
            array_push($lista_membros, $d_membro->idPersonagem);
        }
        
        $sql = "SELECT * FROM usuarios_personagens WHERE id in(".implode(",", array_map('intval', $lista_membros)).") ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $ft = str_replace('cards/', '', $value->foto);

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'publico/'.$value->id.'">
                                    <img src="'.BASE.'assets/cards/'.$ft.'" alt="'.$value->nome.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'publico/'.$value->id.'">
                                    <strong>'.$value->nome.'</strong>
                                </a>
                            </td>
                            <td>'.$user->isGuerreiroOnline($value->id).'</td>
                         </tr>';
            }
        } else {
           $row .= '<tr>'
                   . '<td colspan="3">Nenhum membro encontrado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function getDoacoes($idEquipe, $pc, $qtd_resultados){
        $core = new Core();

        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes_doacoes", "WHERE idEquipe = $idEquipe");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT d.*, up.nome "
             . "FROM equipes_doacoes as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "WHERE d.idEquipe = $idEquipe "
             . "ORDER BY id DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idPersonagem.'">
                                    '.$value->nome.'
                                </a>
                            </td>
                            <td width="150">
                                '.$core->dataBR($value->data).'
                            </td>
                            <td>'.$value->valor.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="3" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="3">Nenhuma doação feita.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function isMembro($id, $idEquipe) {
        // ✅ Add NULL check
        if($id === NULL || $id === '' || $id === 0) {
            return false;
        }
        
        // ✅ FIXED: Check membership in SPECIFIC team when idEquipe is provided
        if($idEquipe) {
            $sql = "SELECT * FROM equipes_membros
                    WHERE idPersonagem = :id
                    AND idEquipe = :idEquipe
                    AND status = 1";

            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idEquipe', $idEquipe, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Check if user is a member of ANY team
            $sql = "SELECT * FROM equipes_membros
                    WHERE idPersonagem = :id
                    AND status = 1";

            $stmt = DB::prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    
    public function isLider($id, $idEquipe) {
        // ✅ Add NULL check to prevent SQL syntax errors
        if($id === NULL || $id === '' || $id === 0) {
            return false;
        }
        
        $sql = "SELECT * FROM equipes_membros 
                WHERE idPersonagem = :id 
                AND idEquipe = :idEquipe 
                AND (lider = 1 OR vicelider = 1) 
                AND status = 1";
        
        $stmt = DB::prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':idEquipe', $idEquipe, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check and update team level based on gold donations
     * Each level requires a FULL gold payment (resets on level up)
     */
    public function verificaLevel($idEquipeEspecifica = null) {
        $core = new Core();
        
        try {
            // If specific team ID provided, only check that team
            if($idEquipeEspecifica != null) {
                $sql = "SELECT * FROM equipes WHERE id = ?";
                $stmt = DB::prepare($sql);
                $stmt->execute([intval($idEquipeEspecifica)]);
                $equipes = $stmt->fetchAll(PDO::FETCH_OBJ);
            } else {
                // Get all teams
                $sql = "SELECT * FROM equipes";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $equipes = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
            
            foreach ($equipes as $value) {
                // Get total gold donated FOR CURRENT LEVEL ONLY
                // Only count donations made AT current level
                $sql = "SELECT SUM(valor) as total FROM equipes_doacoes 
                        WHERE idEquipe = ? AND level_quando_doado = ?";
                $stmt = DB::prepare($sql);
                $stmt->execute([intval($value->id), intval($value->level)]);
                $golds = $stmt->fetch(PDO::FETCH_OBJ);
                
                $totalGold = $golds->total ?? 0;
                
                if($totalGold <= 0) {
                    continue; // No donations yet
                }
                
                // Get the gold COST for next level
                $nextLevel = $value->level + 1;
                
                // Don't exceed max level
                if($nextLevel > 150) {
                    continue;
                }
                
                // Get cost for next level
                $sql = "SELECT goldminimo FROM equipes_levels WHERE level = ?";
                $stmt = DB::prepare($sql);
                $stmt->execute([intval($nextLevel)]);
                $levelData = $stmt->fetch(PDO::FETCH_OBJ);
                
                if(!$levelData) {
                    continue; // No level data found
                }
                
                $goldNecessario = intval($levelData->goldminimo);
                
                // Check if team has enough gold to level up
                if($totalGold >= $goldNecessario) {
                    // LEVEL UP!
                    $novoLevel = $nextLevel;
                    
                    // Update team level
                    $campos = array('level' => $novoLevel);
                    $where = "id = " . intval($value->id);
                    $core->update('equipes', $campos, $where);
                    
                    // ✅ KEEP donation history - donations stay in database
                    // Future donations will have level_quando_doado = new level
                    
                    error_log("Team {$value->id} leveled up from {$value->level} to {$novoLevel}");
                }
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Error in verificaLevel: " . $e->getMessage());
            return false;
        }
    }


    
    public function getStatusExtra($idPersonagem){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idPersonagem AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $membro = $stmt->fetch();
            
            $sql = "SELECT * FROM equipes WHERE id = $membro->idEquipe";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                $equipe = $stmt->fetch();
                
                // Check if level column exists, otherwise return 0
                if(isset($equipe->level)){
                    return $equipe->level * 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    
    public function printEquipe($idMembro){
        $sql = "SELECT * FROM equipes WHERE idCriador = $idMembro ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $equipe_dados = $stmt->fetch();
            $id_equipe = $equipe_dados->id;
        } else {
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                $membro_dados = $stmt->fetch();
                $id_equipe = $membro_dados->idEquipe;
            }
        }
        
        $sql = "SELECT * FROM equipes WHERE id = $id_equipe ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $minha_equipe = $stmt->fetch();
        
        return $minha_equipe;
    }
    
    /**
     * Calculate percentage progress towards next level
     * Progress is calculated WITHIN the current level bracket
     */
    public function getPorcentagemLevel($idPersonagem, $level, $gold) {
        $core = new Core();
        
        // Max level reached
        if($level >= 150) {
            return 100;
        }
        
        try {
            // Get CURRENT level minimum gold requirement
            $sql = "SELECT * FROM equipes_levels WHERE level = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($level)]);
            
            if($stmt->rowCount() == 0) {
                return 0;
            }
            
            $currentLevel = $stmt->fetch(PDO::FETCH_OBJ);
            
            // Get NEXT level minimum gold requirement
            $proximoLevel = $level + 1;
            $sql = "SELECT * FROM equipes_levels WHERE level = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($proximoLevel)]);
            $nextLevel = $stmt->fetch(PDO::FETCH_OBJ);
            
            if(!$nextLevel || !$currentLevel) {
                return 100;
            }
            
            // Calculate gold progress WITHIN current level
            // Example: Level 2 (1000) -> Level 3 (1020)
            // If total gold = 1001, progress = (1001 - 1000) / (1020 - 1000) = 1/20
            
            $goldCurrentLevel = intval($currentLevel->goldminimo); // Current level starts at this gold
            $goldNextLevel = intval($nextLevel->goldminimo);       // Next level starts at this gold
            $goldRange = $goldNextLevel - $goldCurrentLevel;        // Gold needed to level up
            
            // Prevent division by zero
            if($goldRange <= 0) {
                return 100;
            }
            
            // Gold earned in this level
            $goldProgress = intval($gold) - $goldCurrentLevel;
            
            // Calculate percentage (0-100)
            $resultado = intval(($goldProgress / $goldRange) * 100);
            
            // Clamp between 0-100
            return max(0, min(100, $resultado));
            
        } catch (PDOException $e) {
            error_log("Error in getPorcentagemLevel: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate remaining gold needed to reach next level
     * Returns gold needed from CURRENT total to reach NEXT level minimum
     */
    public function getGoldRestante($level, $gold) {
        $core = new Core();
        
        try {
            $proxLevel = $level + 1;
            
            // Get next level data
            $sql = "SELECT * FROM equipes_levels WHERE level = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($proxLevel)]);
            $goldNovo = $stmt->fetch(PDO::FETCH_OBJ);
            
            if(!$goldNovo) {
                return 0; // Max level reached
            }
            
            // Calculate remaining gold to reach next level
            // Example: Next level needs 1020, current total is 1001
            // Remaining = 1020 - 1001 = 19 gold
            
            $goldFaltante = intval($goldNovo->goldminimo) - intval($gold);
            
            return max(0, $goldFaltante);
            
        } catch (PDOException $e) {
            error_log("Error in getGoldRestante: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get the gold minimum required for the NEXT level
     * Used to display the denominator in progress (e.g., "1/20")
     */
    public function getProximoLevel($nivel) {
        try {
            if($nivel >= 150) {
                $level = 150;
            } else {
                $level = $nivel + 1;
            }
            
            $sql = "SELECT * FROM equipes_levels WHERE level = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([intval($level)]);
            $item = $stmt->fetch(PDO::FETCH_OBJ);
            
            if(!$item) {
                return 0;
            }
            
            return $item->goldminimo;
            
        } catch (PDOException $e) {
            error_log("Error in getProximoLevel: " . $e->getMessage());
            return 0;
        }
    }


    public function getCountConvites($idMembro){
        $sql = "SELECT count(*) as total FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        return $item->total;
    }
    
    public function getEquipesPendentes($idMembro, $pc, $qtd_resultados){
        $user = new Usuarios();
        $core = new Core();
        $personagem = new Personagens();
        
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membro_em_equipe = $stmt->rowCount();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes_membros", "WHERE idPersonagem = $idMembro AND status = 0");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT e.*, d.id as idAceite "
             . "FROM equipes_membros as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "INNER JOIN equipes as e ON e.id = d.idEquipe "
             . "WHERE d.idPersonagem = $idMembro "
             . "AND d.status = 0 "
             . "ORDER BY id DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<tr>
                            <td>
                                <a href="'.BASE.'equipes/'.$value->id.'">
                                    <img src="'.BASE.'assets/equipes/'.$value->foto.'" alt="'.$value->nome.'" />
                                </a>
                            </td>
                            <td width="250">
                                <strong>'.$value->nome.'</strong>
                            </td>
                            <td>'.$value->level.'</td>
                            <td>'.$value->sigla.'</td>
                            <td>'.$this->getTotalMembros($value->id).'</td>
                            <td>';
                                if($membro_em_equipe <= 0){
                                $row .= '<a href="'.BASE.'equipes/aceitar/'.$value->idAceite.'" class="bt-aceitar" title="Aceitar">
                                            <i class="fas fa-check"></i>
                                         </a>';
                                }
                                $row .= '<a href="'.BASE.'equipes/recusar/'.$value->idAceite.'" class="bt-recusar" title="Recusar">
                                    <i class="fas fa-minus-circle"></i>
                                </a>
                            </td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Nenhum Convite Pendente</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function verificaMembrosEquipe($idPersonagem, $idAtacado){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $minha_equipe = $stmt->fetch();
        
        if(!empty($minha_equipe)){
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idAtacado";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $equipe_adversario = $stmt->fetch();
            
            if(!empty($equipe_adversario)){
                if($minha_equipe->idEquipe == $equipe_adversario->idEquipe){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function getRankingEquipes($pc, $qtd_resultados){
        $core = new Core();
        
        $this->sincronizaEquipes();

        //Paginando os Resultados
        $counter = $core->counterRegisters("equipes");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM equipes "
             . "ORDER BY level DESC, pvp DESC, gold DESC "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
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
                    $top = 'top-rank';
                    $tdClass = 'top';
                    $star = '<i class="fas fa-star"></i>';
                } else {
                    $top = '';
                    $tdClass = '';
                    $star = '';
                }

                $row .= '<tr class="tr-equipes '.$top.'">
                            <td class="'.$tdClass.'"><strong>'.$star.$rank.'º</strong></td>
                            <td>
                                <a href="'.BASE.'equipes/'.$value->id.'">
                                    <img src="'.BASE.'assets/equipes/'.$value->foto.'" alt="'.$value->nome.'" />
                                </a>
                            </td>
                            <td width="250">
                                <a href="'.BASE.'equipes/'.$value->id.'">
                                    <strong>'.$value->nome.'</strong>
                                </a>
                            </td>
                            <td>'.$value->level.'</td>
                            <td>'.$value->pvp.'</td>
                            <td>'.$value->gold.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
           $row .= '<tr>'
                   . '<td colspan="6">Ranking não encontrado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function sincronizaEquipes(){
        $core = new Core();
        
        $sql = "SELECT * FROM equipes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $equipes = $stmt->fetchAll();
        
        foreach ($equipes as $key => $value) {
            $gold = 0;
            $pvp = 0;
        
            $sql = "SELECT sum(valor) as total FROM equipes_doacoes WHERE idEquipe = $value->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $golds = $stmt->fetch();
            
            $gold += $golds->total;
            
            $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $value->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $membros = $stmt->fetchAll();
            
            foreach ($membros as $key2 => $value2) {
                $sql = "SELECT sum(vitorias_pvp) as total FROM usuarios_personagens WHERE id = $value2->idPersonagem";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $pvps = $stmt->fetch();
                
                $pvp += $pvps->total;
            }
            
            $campos = array(
                'pvp' => $pvp,
                'gold' => $gold
            );

            $where = 'id="'.$value->id.'"';

            $core->update('equipes', $campos, $where);
        }
    }
    
    public function getInteracoesChat($idEquipe){
        $sql = "SELECT * FROM equipes_chat_interacoes WHERE idEquipe = $idEquipe AND status = 1 ORDER BY id DESC LIMIT 100";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $interacoes = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($interacoes as $key => $value) {
                $row .= '<div class="item">'.$value->mensagem.'</div>';
            }
        } else {
            $row .= '<span class="no-interacoes">Nenhuma mensagem encontrada.</span>';
                     
        }
        
        
        
        return $row;
    }
    
    public function getIndicadorDoacao($idEquipe){
        $core = new Core();
        
        $sql = "SELECT em.*, 
                (SELECT SUM(ed.valor) FROM equipes_doacoes AS ed WHERE ed.idPersonagem = em.idPersonagem) AS total 
                FROM equipes_membros as em 
                WHERE em.status = 1 
                AND em.idEquipe = $idEquipe 
                ORDER BY total DESC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();
        
        // ✅ FIX: Get total count of members with donations
        $total_doadores = count($itens);
        
        $row = '';
        
        // ✅ FIX: Add rank counter
        $posicao = 0;
        
        foreach ($itens as $key => $value) {
            // ✅ FIX: Increment position counter
            $posicao++;
            
            $sql = 'SELECT ed.*, sum(valor) as total, up.nome, up.foto 
                    FROM equipes_doacoes as ed 
                    INNER JOIN usuarios_personagens as up ON up.id = ed.idPersonagem 
                    WHERE ed.idEquipe = '.$idEquipe.'  
                    AND ed.idPersonagem = '.$value->idPersonagem;
            
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $membros = $stmt->fetch();
            
            if($membros->total != null){
                $total = $membros->total;
            } else {
                $total = 0;
            }
            
            // ✅ FIX: Display rank position instead of total gold
            $row .= '<li>
                        <img src="'.BASE.'assets/cards/'.$membros->foto.'" alt="'.$membros->nome.'" />
                        <h3>'.$membros->nome.'</h3>
                        <span class="total-golds">
                            <img src="'.BASE.'assets/icones/gold.png" alt="Golds" />
                            '.$posicao.' / '.$total_doadores.'
                        </span>
                        <span class="total-donated" style="display:block; font-size:12px; color:#999; margin-top:5px;">
                            Total Doado: '.$total.' gold
                        </span>
                    </li>';
        }
        
        echo $row;
    }

    
    public function getDoacoesSemanal($idEquipe){
        $core = new Core();
        
        $sql = "SELECT DISTINCT d.id, d.idPersonagem, d.idEquipe, d.data, up.nome, up.foto, "
             . "(SELECT SUM(ed.valor) FROM equipes_doacoes AS ed WHERE ed.idPersonagem = d.idPersonagem AND YEARWEEK(ed.data, 1) = YEARWEEK(CURDATE(), 1)) AS total "
             . "FROM equipes_doacoes as d "
             . "INNER JOIN usuarios_personagens as up ON up.id = d.idPersonagem "
             . "WHERE d.idEquipe = $idEquipe "
             . "AND YEARWEEK(d.data, 1) = YEARWEEK(CURDATE(), 1) "
             . "GROUP BY total "
             . "ORDER BY total DESC ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        $rank = 0;
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $rank ++;
                
                if($rank == 1){
                    $top = 'top-player';
                } else {
                    $top = '';
                }
                
                $row .= '<tr class="'.$top.'">
                            <td><strong>'.$rank.'º</strong></td>
                            <td>
                                <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                            </td>
                            <td>
                                <a href="'.BASE.'publico/'.$value->idPersonagem.'">
                                    '.$value->nome.'
                                </a>
                            </td>
                            <td width="150">
                                '.$core->dataBR($value->data).'
                            </td>
                            <td>'.$value->total.'</td>
                         </tr>';
            }
        } else {
           $row .= '<tr>'
                   . '<td colspan="5">Nenhuma doação feita.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
}
