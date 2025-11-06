<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Equipes
 *
 * @author Felipe Faciroli
 */
class Equipes {
    public function getBandeiras(){
        $sql = "SELECT * FROM equipes_bandeiras";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            if($value->id == 1){
                $checked = 'checked';
            } else {
                $checked = '';
            }
            
            $row .= '<label for="bandeira-'.$value->id.'">
                        <i class="fas fa-check-circle"></i>
                        <input type="radio" id="bandeira-'.$value->id.'" '.$checked.' name="idBandeira" value="'.$value->bandeira.'" />
                        <img src="'.BASE.'assets/equipes/'.$value->bandeira.'" alt="" />
                     </label>';
            
        }
        
        echo $row;
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
    
    public function dadosEquipeAtual($idMembro, $idEquipe = ''){
        
        if($idEquipe == ''){
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $equipeMembros = $stmt->fetch();
            
            $idE = '';

            if($stmt->rowCount() > 0){
                $idE = $equipeMembros->idEquipe;
            }

            $sql = "SELECT * FROM equipes WHERE idCriador = $idMembro";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $equipeDados = $stmt->fetch();

            if($stmt->rowCount() > 0){
                $idE = $equipeDados->id;
            }
        } else {
            $idE = $idEquipe;
        }
        
        if($idE != ''){
            $sql = "SELECT * FROM equipes WHERE id = $idE";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $dados = $stmt->fetch();

            return $dados;
        } else {
            return 0;
        }
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
        
        $lista_membros = array();
        
        foreach ($membros as $chave => $d_membro) {
            array_push($lista_membros, $d_membro->idPersonagem);
        }
        
        $sql = "SELECT sum(vitorias_pvp) as total FROM usuarios_personagens WHERE id in (".implode(",", array_map('intval', $lista_membros)).") ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $vitorias = $stmt->fetch();
        
        return $vitorias->total;
    }
    
    public function getTotalVitoriasTAM($idEquipe){
        $sql = "SELECT * FROM equipes_membros WHERE idEquipe = $idEquipe AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membros = $stmt->fetchAll();
        
        $lista_membros = array();
        
        foreach ($membros as $chave => $d_membro) {
            array_push($lista_membros, $d_membro->idPersonagem);
        }
        
        $sql = "SELECT sum(tam) as total FROM usuarios_personagens WHERE id in (".implode(",", array_map('intval', $lista_membros)).") ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $vitorias = $stmt->fetch();
        
        return $vitorias->total;
    }
    
    public function getTotalGold($idEquipe){
        $sql = "SELECT sum(valor) as total FROM equipes_doacoes WHERE idEquipe = $idEquipe";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $gold = $stmt->fetch();
        
        if($gold->total != null){
            return $gold->total;
        } else {
            return 0;
        }
    }
    
    public function getRanking($idEquipe){
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
        
        $orderBY = "ORDER BY up.nivel DESC, up.vitorias_pvp DESC, up.tam DESC, up.gold_total DESC ";
        
        $sql = "SELECT "
            . "up.*, up.id as idP, up.foto as foto_personagem, "
            . "u.*, "
            . "up.nome as nome_guerreiro, "
            . "p.nome as planeta, p.imagem as img_planeta "
            . "FROM usuarios_personagens as up "
            . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
            . "INNER JOIN planetas as p ON up.idPlaneta = p.id "
            . "WHERE up.id in(".implode(",", array_map('intval', $lista_membros)).") "
            . $orderBY;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            $rank = 0;

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
                            <td width="250">'.$personagem->verificaGraduacao($value->nivel).'</td>
                            <td>'.$value->nivel.'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$user->isGuerreiroOnline($value->idP).'</td>
                         </tr>';
            }
        } else {
           $row .= '<tr>'
                   . '<td colspan="7">Nenhum membro encontrado.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
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
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idMembro AND status = 1 AND vice_lider = 1";
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
    
    public function isMembro($id, $idEquipe = ''){
        if($idEquipe == ''){
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $id AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $id AND idEquipe = $idEquipe AND status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
        }
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function existsMembro($id, $idEquipe){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $id AND idEquipe = $idEquipe";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function isLider($id, $idEquipe){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $id AND idEquipe = $idEquipe AND (lider = 1 OR vice_lider = 1) AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $membro = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaLevel(){
        $core = new Core();
        
        $sql = "SELECT * FROM equipes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $equipes = $stmt->fetchAll();
        
        foreach ($equipes as $key => $value) {
            $sql = "SELECT sum(valor) as total FROM equipes_doacoes WHERE idEquipe = $value->id ";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $golds = $stmt->fetch();
            
            if($golds->total == null){
                $level_atualiza = 1;
            } else {
                $sql = "SELECT * FROM equipes_levels WHERE gold_minimo <= $golds->total AND gold >= $golds->total";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $levels = $stmt->fetch();
                
                if(!$levels){
                    $sql = "SELECT * FROM equipes_levels ORDER BY id DESC LIMIT 1";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();
                    $ultimo_level = $stmt->fetch();
                
                    $level_atualiza = $ultimo_level->level;
                } else {
                    $level_atualiza = $levels->level;
                }
            }
            
            if($value->level != $level_atualiza){
                $campos = array(
                    'level' => $level_atualiza
                );

                $where = 'id = "'.$value->id.'"';

                $core->update('equipes', $campos, $where);
            }
        }
    }
    
    public function getStatusExtra($idPersonagem){
        $sql = "SELECT * FROM equipes_membros WHERE idPersonagem = $idPersonagem AND aceitou = 1";
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
                    return $equipe->level * 3;
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
    
    public function getPorcentagemLevel($idPersonagem, $level, $gold){
        $core = new Core();
        
        $sql = "SELECT * FROM equipes_levels WHERE level = $level";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        $proximo_level = $level + 1;
        
        $gold_anterior = $core->getDados('equipes_levels', 'WHERE level ='.$level);
        $dadosProximoLevel = $core->getDados('equipes_levels', 'WHERE level ='.$proximo_level);
        
        $gold_faltante = intval($dadosProximoLevel->gold_minimo) - intval($gold_anterior->gold_minimo);
        $gold_adquirido = intval($gold_anterior->gold) - intval($gold);
        $gold_alcancado = intval($gold_faltante) - intval($gold_adquirido);
        
        $total = intval($gold_alcancado) /  intval($gold_faltante);
        
        $resultado = intval($total * 100);

        return $resultado;
    }
    
    public function getGoldRestante($level, $gold){
        $core = new Core();
        
        $prox_level = $level+ 1;
        
        $gold_anterior = $core->getDados('equipes_levels', 'WHERE level ='.$level);
        $gold_novo = $core->getDados('equipes_levels', 'WHERE level ='.$prox_level);
        
        $gold_faltante = intval($gold_novo->gold_minimo) - intval($gold);
        
        return $gold_faltante;
    }
    
    public function getProximoLevel($nivel){
        if($nivel == 150){
            $level = 150;
        } else {
            $level = $nivel + 1;
        }
        
        $sql = "SELECT * FROM equipes_levels WHERE level = $level";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        return $item->gold_minimo;
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
        
        $row = '';
        
        foreach ($itens as $key => $value) {
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
            
            $row .= '<li>
                        <img src="'.BASE.'assets/cards/'.$membros->foto.'" alt="'.$membros->nome.'" />
                        <h3>'.$membros->nome.'</h3>
                        <span class="total-golds">
                            <img src="'.BASE.'assets/icones/gold.png" alt="Golds" />
                            '.$total.'
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
