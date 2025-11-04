<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Chat
 *
 * @author Felipe Faciroli
 */
class Chat {
    public function getChat($idPersonagem, $idAmigo){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_chat WHERE (idPersonagem = $idPersonagem AND idAmigo = $idAmigo) OR (idPersonagem = $idAmigo AND idAmigo = $idPersonagem)";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '<ul>';
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
            
            foreach ($itens as $key => $value) {
                
                if($idPersonagem == $value->idAmigo){
                    $class = 'amigo';
                } else {
                    $class = '';
                }
                
                $row .= '<li class="'.$class.'">
                            <div class="line">
                                <p>'.$value->mensagem.'</p>
                            </div>
                            <em>'.$core->dataTimeBR($value->data).'</em>
                         </li>';
            }
        } else {
            $row .= '<li>
                        <p class="mensagemNotFound">Iniciar Conversa</p>
                     </li>';
        }
        
        $row .= '</ul>';
        
        echo $row;
    }
    
    public function getMensagensCount($idPersonagem, $idAmigo){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_chat WHERE (idPersonagem = $idPersonagem AND idAmigo = $idAmigo) OR (idPersonagem = $idAmigo AND idAmigo = $idPersonagem)";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $count = 0;
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
            
            foreach ($itens as $key => $value) {
                
                if($value->idPersonagem == $idAmigo){
                    $count ++;
                    
                    $sql = "SELECT * FROM adm_chat_lidas WHERE idMensagem = $value->id AND idPersonagem = $idPersonagem";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();

                    if($stmt->rowCount() > 0){
                        $count --;
                    }
                }
            }
        }
        
        return $count;
    }
    
    public function getMensagensCountAll($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_chat WHERE idAmigo = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $count = 0;
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
            
            foreach ($itens as $key => $value) {
                $count ++;

                $sql = "SELECT * FROM adm_chat_lidas WHERE idMensagem = $value->id AND idPersonagem = $idPersonagem";
                $stmt = DB::prepare($sql);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $count --;
                }
            }
        }
        
        return $count;
    }
    
    public function getMensagensPending($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_chat WHERE idAmigo = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $count = 0;
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
            
            foreach ($itens as $key => $value) {
                $count ++;

                $sql = "SELECT * FROM adm_chat_lidas WHERE idMensagem = $value->id AND idPersonagem = $idPersonagem";
                $stmt = DB::prepare($sql);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $count --;
                }
            }
        }
        
        if($count > 0){
            return 'pulse';
        } else {
            return '';
        }
    }
    
    public function getLerMensagens($idPersonagem, $idAmigo){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_chat WHERE (idPersonagem = $idPersonagem AND idAmigo = $idAmigo) OR (idPersonagem = $idAmigo AND idAmigo = $idPersonagem)";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '<ul>';
        
        if($stmt->rowCount() > 0){
            $itens = $stmt->fetchAll();
        
            foreach ($itens as $key => $value) {
                
                if($value->idAmigo == $idPersonagem){
                    $sql = "SELECT * FROM adm_chat_lidas WHERE id = $value->id AND idPersonagem = $idPersonagem";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();

                    if($stmt->rowCount() <= 0){
                        $campos = array(
                            'idMensagem' => $value->id,
                            'idPersonagem' => $idPersonagem
                        );

                        $core->insert('adm_chat_lidas', $campos);
                    }
                }
            }
        }
    }
    
    public function getListConversas($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT DISTINCT * FROM adm_chat WHERE idAmigo = $idPersonagem GROUP BY idPersonagem ORDER BY id DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                
                if($value->idAmigo == $idPersonagem){
                    $sql = "SELECT * FROM adm_chat_lidas WHERE id = $value->id AND idPersonagem = $idPersonagem";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();

                    if($stmt->rowCount() <= 0){
                        $dadosAmigo = $core->getDados('usuarios_personagens', "WHERE id = ".$value->idPersonagem);
                        
                        $sql = "SELECT * FROM adm_chat WHERE idAmigo = $idPersonagem AND idPersonagem = $value->idPersonagem ORDER BY id DESC LIMIT 1";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $dadosMsg = $stmt->fetch();
                        
                        $row .= '<tr>
                                    <td class="enviado-por"><a href="'.BASE.'publico/'.$value->idPersonagem.'" style="color: #069683;">'.$dadosAmigo->nome.'</a></td>
                                    <td class="mensagem">'.$dadosMsg->mensagem.'</td>
                                    <td class="data">'.$core->dataTimeBR($dadosMsg->data).'</td>
                                    <td>
                                        <a style="color: #069683;" href="'.BASE.'publico/'.$value->idPersonagem.'" title="Conversar">
                                            <i class="fas fa-comment-dots"></i>
                                        </a>
                                    </td>
                                 </tr>';
                    }
                }
            }
        }
        
        echo $row;
    }
}
