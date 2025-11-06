<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuarios
 *
 * @author Felipe Faciroli
 */
class Usuarios {
    public $id;
    public $nome;
    public $coins;
    public $ranking;
    public $email;
    public $username;
    public $perfil;
    public $vip;
    public $data_cadastro;
    public $data_expiracao;
    public $reputacao;
    public $forum_reputacao;
    public $foto;
    
    public function login($usuario, $senha){
        
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE username = '$usuario' AND senha = '$senha' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        // GRAVA O IP CASO ESTEJA VAZIO
        if($row->ip == ''){
            $meuIP = $core->getIP();
            
            $campos = array(
                'ip' => $meuIP
            );

            $where = 'id = "'.$row->id.'"';

            $core->update('usuarios', $campos, $where);
        }
        
        if($stmt->rowCount() > 0){
            $campos = array(
                'online' => 1,
                'ultimo_logon' => date('Y-m-d H:i:s')
            );

            $where = 'id = "'.$row->id.'"';

            $core->update('usuarios', $campos, $where);

            if(!empty($row)){

                $this->id = $row->id;
                $this->nome = $row->nome;
                $this->coins = $row->coins;
                $this->ranking = $row->ranking;
                $this->email = $row->email;
                $this->username = $row->username;
                $this->perfil = $row->perfil;
                $this->vip = $row->vip;
                $this->data_cadastro = $row->data_cadastro;
                $this->data_expiracao = $row->data_expiracao;
                $this->reputacao = $row->idReputacao;
                $this->forum_reputacao = $row->forum_reputacao;
                $this->foto = $row->foto;

                $_SESSION['user_id'] = $row->id; 
                $_SESSION['username'] = $row->username; 
                $_SESSION['email'] = $this->email; 
                $_SESSION['user_logado'] = true; 

                date_default_timezone_set("Brazil/East");
                $tempolimite = 2100;
                $_SESSION['autenticado'] = time();
                $_SESSION['limite'] = $tempolimite;
                
                $_SESSION['donoDaSessao'] = md5('seg'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function expireSession($idPersonagem = ''){
        $core = new Core();
        
        if(isset($_SESSION['autenticado'])){
            $registro = $_SESSION['autenticado'];
            $limite = $_SESSION['limite'];

            if($registro){
             $segundos = time()- $registro;
            }

            if($segundos>$limite){
                if($idPersonagem != ''){
                    $campos_p = array(
                        'online' => 0
                    );

                    $where_p = 'id = "'.$idPersonagem.'"';

                    $core->update('usuarios_personagens', $campos_p, $where_p);
                }
                session_destroy();
                header('Location: '.BASE.'home');
            } else {
                $_SESSION['autenticado'] = time();
            }
        }
    }
    
    public function checkLogin(){
        if(isset($_SESSION["user_logado"])){
            if($_SESSION["user_logado"] == true){
                $this->getUserInfo($_SESSION["username"]);
            } else {
                session_destroy();
                header('Location: '.BASE.'home');
                exit;
            }
        } else {
            session_destroy();
            header('Location: '.BASE.'home');
            exit;
        }
    }
    
    public function getUserInfo($usuario){
        
        if($usuario != ''){
            $sql = "SELECT * FROM usuarios WHERE username = '$usuario'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            $this->id = $row->id;
            $this->nome = $row->nome;
            $this->coins = $row->coins;
            $this->ranking = $row->ranking;
            $this->email = $row->email;
            $this->username = $row->username;
            $this->perfil = $row->perfil;
            $this->vip = $row->vip;
            $this->data_cadastro = $row->data_cadastro;
            $this->data_expiracao = $row->data_expiracao;
            $this->reputacao = $row->idReputacao;
            $this->forum_reputacao = $row->forum_reputacao;
            $this->foto = $row->foto;
            
            $_SESSION['user_id'] = $row->id;
        }
    }
    
    public function getUserInfoByID($idUsuario){
        
        if($idUsuario != ''){
            $sql = "SELECT * FROM usuarios WHERE id = '$idUsuario'";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            
            $this->id = $row->id;
            $this->nome = $row->nome;
            $this->coins = $row->coins;
            $this->ranking = $row->ranking;
            $this->email = $row->email;
            $this->username = $row->username;
            $this->perfil = $row->perfil;
            $this->vip = $row->vip;
            $this->data_cadastro = $row->data_cadastro;
            $this->data_expiracao = $row->data_expiracao;
            $this->reputacao = $row->idReputacao;
            $this->forum_reputacao = $row->forum_reputacao;
            $this->foto = $row->foto;
        }
    }
    
    public function isOnline($usuario){
        $sql = "SELECT online FROM usuarios WHERE username = '$usuario' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        if($row->online == 1){
            $status = '<div class="user-online">
                            <div class="stat"></div>
                            <span>Online</span>
                       </div>';
        } else {
            $status = '<div class="user-offline">
                            <div class="stat"></div>
                            <span>Offline</span>
                       </div>';
        }
        
        return $status;
    }
    
    public function isGuerreiroOnline($idPersonagem){
        $sql = "SELECT * FROM usuarios_monitoramento WHERE idPersonagem = $idPersonagem ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $status = '<div class="user-online">
                            <div class="stat"></div>
                            <span>Online</span>
                       </div>';
        } else {
            $status = '<div class="user-offline">
                            <div class="stat"></div>
                            <span>Offline</span>
                       </div>';
        }
        
        return $status;
    }
    
    public function isGuerreiroOnlineInt($idPersonagem){
        $sql = "SELECT * FROM usuarios_monitoramento WHERE idPersonagem = $idPersonagem ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $status = 1;
        } else {
            $status = 0;
        }
        
        return $status;
    }
    
    public function verificaVIP($usuario, $personagem, $graduacao, $idPersonagem){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE username = '$usuario' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        $dt_atual = date('Y-m-d');
        $timestamp_dt_atual = strtotime($dt_atual);

        $dt_expira = $row->data_expiracao;
        $timestamp_dt_expira = strtotime($dt_expira);

        if ($timestamp_dt_atual > $timestamp_dt_expira){
            if($row->vip == 1){
                $campos = array(
                    'vip' => 0
                );

                $where = 'username = "'.$usuario.'"';

                $core->update('usuarios', $campos, $where);
            }
        }
    }
    
    public function diasRestantes($data_final){
        $data_inicial = date('Y-m-d'); 
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        return $dias;
    }
    
    public function recuperarSenha($email){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE email = '$email' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        if($stmt->rowCount() == 1){
            
            $chave = sha1(uniqid( mt_rand(), true));
            
            $sql = "INSERT INTO usuarios_recuperacao VALUES ('$usuario->id', '$chave') ";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                $link = BASE."login/confirmacao/".$usuario->id."/".$chave;
                
                $mail = new Mail();
                
                $body = '<div style="width: 600px; margin: 0 auto;">';
                    $body .= '<img src="'.BASE.'assets/mail-header.jpg" style="display: block; width: 100%;" />';
                    $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;">Olá <strong>'.$usuario->nome.'</strong>, este e-mail foi enviado devido sua solicitação de recuperação de senha.</p>';
                    $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;"><a style="display: inline-block; vertical-align: middle; margin-right: 10px;" href="'.$link.'"><img src="'.BASE.'assets/mail-clique.png" /></a> para recuperar sua senha.</p>';
                    $body .= '<img src="'.BASE.'assets/mail-footer.jpg" style="display: block; width: 100%;" />';
                $body .= '</div>';
                
                if($mail->sendMail($email, 'Recuperação de Senha - DB Heroes - RPG GAME', $body)){
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    
    public function novaSenha($user, $senha){
        $novaSenha = md5($senha);
        
        $sql = "UPDATE usuarios SET senha = '$novaSenha' WHERE id = $user ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getRecuperacao($user, $chave){
        $sql = "SELECT COUNT(*) FROM usuarios_recuperacao WHERE utilizador = '$user' AND confirmacao = '$chave' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $sql = "DELETE FROM usuarios_recuperacao WHERE utilizador = '$user' AND confirmacao = '$chave' ";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            return true;
        } else {
            return false;
        }
    }
    
    public function gravaTrafego($idPersonagem){
        $core = new Core();
        
        $timestamp = time(); 
        $timeout = time() - 120; // valor em segundos 
        
        $tipo_conexao = $_SERVER['HTTP_HOST'];
 
        if (($tipo_conexao != 'localhost') && ($tipo_conexao != '127.0.0.1')){
            if(!$core->isExists('usuarios_monitoramento', 'WHERE ip = "'.$_SERVER['REMOTE_ADDR'].'"')){
                if(!$core->isExists('usuarios_monitoramento', 'WHERE idPersonagem = "'.$idPersonagem.'"')){
                    $insert = array(
                        'timestamp' => $timestamp,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'arquivo' => $_SERVER['PHP_SELF'],
                        'idPersonagem' => $idPersonagem
                    );

                    $core->insert('usuarios_monitoramento', $insert);
                } else {
                    $campos = array(
                        'timestamp' => $timestamp,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'idPersonagem' => $idPersonagem
                    );

                    $where = 'ip = "'.$_SERVER['REMOTE_ADDR'].'"';

                    $core->update('usuarios_monitoramento', $campos, $where);
                }
            } else {
                $campos = array(
                    'timestamp' => $timestamp,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'idPersonagem' => $idPersonagem
                );

                $where = 'ip = "'.$_SERVER['REMOTE_ADDR'].'"';

                $core->update('usuarios_monitoramento', $campos, $where);
            }

            $core->delete('usuarios_monitoramento', "timestamp < ".$timeout);
        }
    }
    
    public function monitoramento(){        
        $sql = "SELECT DISTINCT count(*) as total FROM usuarios_monitoramento";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $online = $stmt->fetch();

        return $online->total;
    }
    
    public function deleteMonitoramento($ip){
        $core = new Core();
        $core->delete('usuarios_monitoramento', "ip = '".$ip."'");
    }
    
    public function verificaInvites(){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE user_vinculado != 0 AND vinculo_fidelizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $vinculados = $stmt->fetchAll();
            
            foreach ($vinculados as $key => $value) {
                $fidelizado = 0;
                $nivel_atingido = 0;
                
                //VERIFICA SE CRIOU ALGUM PERSONAGEM
                $sql_personagem = "SELECT * FROM usuarios_personagens WHERE idUsuario = $value->id";
                $stmt = DB::prepare($sql_personagem);
                $stmt->execute();
                
                if($stmt->rowCount() > 0){
                    $fidelizado = $fidelizado + 1;
                    $personagens_vinculados = $stmt->fetchAll();
                    
                    foreach ($personagens_vinculados as $key2 => $value2) {
                        //VERIFICA SE REALIZOU ALGUM PVP
                        $sql_pvp = "SELECT * FROM pvp WHERE idPersonagem = $value2->id AND concluido = 1";
                        $stmt = DB::prepare($sql_pvp);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $fidelizado = $fidelizado + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUM NPC
                        $sql_npc = "SELECT * FROM npc WHERE idPersonagem = $value2->id AND concluido = 1";
                        $stmt = DB::prepare($sql_npc);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $fidelizado = $fidelizado + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUMA MISSÃO
                        $sql_missoes = "SELECT * FROM personagens_missoes WHERE idPersonagem = $value2->id AND concluida = 1 AND cancelada = 0";
                        $stmt = DB::prepare($sql_missoes);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $fidelizado = $fidelizado + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUMA CAÇADA
                        $sql_cacadas = "SELECT * FROM cacadas WHERE idPersonagem = $value2->id AND concluida = 1 AND cancelada = 0";
                        $stmt = DB::prepare($sql_cacadas);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $fidelizado = $fidelizado + 1;
                        }
                        
                        if($value2->nivel >= 10){
                            $nivel_atingido = 1;
                        }
                    }
                }
                
                if($fidelizado >= 5){
                    if($nivel_atingido == 1){
                        $campos = array(
                            'vinculo_fidelizado' => 1
                        );

                        $where = 'id="'.$value->id.'"';

                        $core->update('usuarios', $campos, $where);

                        //PAGA PREMIAÇÃO DO INVITE
                        $sql_vinculado = "SELECT * FROM usuarios WHERE id = $value->user_vinculado";
                        $stmt = DB::prepare($sql_vinculado);
                        $stmt->execute();
                        $dados_invite = $stmt->fetch();

                        $campos_u = array(
                            'coins' => intval($dados_invite->coins) + 1
                        );

                        $where_u = 'id="'.$value->user_vinculado.'"';

                        $core->update('usuarios', $campos_u, $where_u);
                    }
                }
            }
        }
    }
    
    public function getListInvites($idUsuario, $pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("usuarios", "WHERE user_vinculado = $idUsuario");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT u.* "
             . "FROM usuarios as u "
             . "WHERE u.user_vinculado = $idUsuario "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $qtd_cacadas = 0;
                $qtd_missoes = 0;
                $qtd_pvp = 0;
                $qtd_npc = 0;
                
                $sql_personagens = "SELECT *, count(*) as total FROM usuarios_personagens WHERE idUsuario = $value->id";
                $stmt = DB::prepare($sql_personagens);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $person = $stmt->fetchAll();

                    foreach ($person as $key2 => $value2) {
                        //VERIFICA SE REALIZOU ALGUM PVP
                        $sql_pvp = "SELECT * FROM pvp WHERE idPersonagem = $value2->id AND concluido = 1";
                        $stmt = DB::prepare($sql_pvp);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $qtd_pvp = $qtd_pvp + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUM NPC
                        $sql_npc = "SELECT * FROM npc WHERE idPersonagem = $value2->id AND concluido = 1";
                        $stmt = DB::prepare($sql_npc);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $qtd_npc = $qtd_npc + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUMA MISSÃO
                        $sql_missoes = "SELECT * FROM personagens_missoes WHERE idPersonagem = $value2->id AND concluida = 1 AND cancelada = 0";
                        $stmt = DB::prepare($sql_missoes);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $qtd_missoes = $qtd_missoes + 1;
                        }
                        
                        //VERIFICA SE REALIZOU ALGUMA CAÇADA
                        $sql_cacadas = "SELECT * FROM cacadas WHERE idPersonagem = $value2->id AND concluida = 1 AND cancelada = 0";
                        $stmt = DB::prepare($sql_cacadas);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0){
                            $qtd_cacadas = $qtd_cacadas + 1;
                        }
                    }
                }
                
                $row .= '<tr>
                            <td>'.$value->username.'</td>
                            <td>'.$value->email.'</td>
                            <td>'.$value2->total.'/1</td>
                            <td>'.$qtd_cacadas.'/1</td>
                            <td>'.$qtd_missoes.'/1</td>
                            <td>'.$qtd_pvp.'/1</td>
                            <td>'.$qtd_npc.'/1</td>';
                
                            if($value2->nivel >= 10){
                                $row .= '<td class="aprovado" title="Level Necessário Atingido">
                                            <i class="fas fa-check-circle"></i>
                                         </td>';
                            } else {
                                $row .= '<td class="pendente" title="Pendente">
                                            <i class="fas fa-exclamation-triangle"></i>
                                         </td>';
                            }
                
                            if($value->vinculo_fidelizado == 1){
                                $row .= '<td class="aprovado" title="Você recebeu o Coin">
                                            <i class="fas fa-check-circle"></i>
                                         </td>';
                            } else {
                                $row .= '<td class="pendente" title="Pendente">
                                            <i class="fas fa-exclamation-triangle"></i>
                                         </td>';
                            }
                $row .= '</tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="9" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="9" class="not">Nenhum usuário convidado.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function getTotalCoinsInvites($idUsuario){
        $core = new Core();

        $counter = $core->counterRegisters("usuarios", "WHERE user_vinculado = $idUsuario AND vinculo_fidelizado = 1");
        
        return $counter;
    }
    
    public function getEmailVerificado($idUsuario){
        $sql = "SELECT * FROM usuarios WHERE id = $idUsuario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->email_validado == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function enviaConfirmacao($idUsuario){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE id = $idUsuario ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        $data = date('y-m-d H:i:s');
        $chave = md5($usuario->email.$data);
        
        $campos = array(
            'chave_verificacao' => $chave
        );

        $where = 'id="'.$idUsuario.'"';
        
        if($core->update('usuarios', $campos, $where)){
            $link = BASE."autenticar/".$chave;
                
            $mail = new Mail();

            $body = '<div style="width: 600px; margin: 0 auto;">';
                $body .= '<img src="'.BASE.'assets/mail-header.jpg" style="display: block; width: 100%;" />';
                $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;">Olá <strong>'.$usuario->nome.'</strong>, este e-mail foi enviado para a confirmação de cadastro.</p>';
                $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;"><a style="display: inline-block; vertical-align: middle; margin-right: 10px;" href="'.$link.'"><img src="'.BASE.'assets/mail-clique.png" /></a> para confirmar seu cadastro.</p>';
                $body .= '<img src="'.BASE.'assets/mail-footer.jpg" style="display: block; width: 100%;" />';
            $body .= '</div>';

            if($mail->sendMail($usuario->email, 'Coinfirmação de cadastro - DB Heroes - RPG GAME', $body)){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function enviaConfirmacaoCadastro($username){
        $core = new Core();
        
        $sql = "SELECT * FROM usuarios WHERE username = '$username' ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        $data = date('y-m-d H:i:s');
        $chave = md5($usuario->email.$data);
        
        $campos = array(
            'chave_verificacao' => $chave
        );

        $where = 'id="'.$usuario->id.'"';
        
        if($core->update('usuarios', $campos, $where)){
            $link = BASE."autenticar/".$chave;
                
            $mail = new Mail();

            $body = '<div style="width: 600px; margin: 0 auto;">';
                $body .= '<img src="'.BASE.'assets/mail-header.jpg" style="display: block; width: 100%;" />';
                $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;">Olá <strong>'.$usuario->nome.'</strong>, este e-mail foi enviado para a confirmação de cadastro.</p>';
                $body .= '<p style="display: block; width: 90%; margin: 0 auto; padding: 20px 0; text-align: center;"><a style="display: inline-block; vertical-align: middle; margin-right: 10px;" href="'.$link.'"><img src="'.BASE.'assets/mail-clique.png" /></a> para confirmar seu cadastro.</p>';
                $body .= '<img src="'.BASE.'assets/mail-footer.jpg" style="display: block; width: 100%;" />';
            $body .= '</div>';

            if($mail->sendMail($usuario->email, 'Coinfirmação de cadastro - DB Heroes - RPG GAME', $body)){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
        
    // Add this method to Usuarios.php class
    public function validaIP($ip) {
        $core = new Core();
        
        // Check how many accounts exist with this IP
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE ip = '$ip'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        // Allow maximum 3 accounts per IP (you can change this limit)
        if($row->total >= 3) {
            return false; // IP limit reached
        }
        
        return true; // IP is valid
    }

    
    public function validaCamposCadastro($id){
        $sql = "SELECT * FROM usuarios WHERE id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usr = $stmt->fetch();
        
        $validado = 0;
        
        if(!empty($usr->cep) && !empty($usr->endereco) && !empty($usr->cidade) && !empty($usr->bairro) && !empty($usr->estado)){
            $validado = 1;
        }
        
        if($validado == 1){
            return true;
        } else {
            return false;
        }
    }
}
