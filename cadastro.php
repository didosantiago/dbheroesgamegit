<?php 
    if(!$_POST){
        $_SESSION['token_form_cadastro'] = md5(time());
    }
    
    if(isset($_POST['cadastrar'])){
        if($_SESSION['token_form_cadastro'] == addslashes($_POST['token_form_cadastro'])){
            if(Url::getURL(2) != null){
                if(Url::getURL(2) == 'invite'){
                    $invite = Url::getURL(3);
                }
            }

            $campos = array(
                'nome' => addslashes($_POST['nome']),
                'email' => addslashes($_POST['email']),
                'username' => addslashes($_POST['username']),
                'senha' => md5(addslashes($_POST['senha'])),
                'aceite' => addslashes($_POST['aceite']),
                'vip' => 0,
                'data_cadastro' => date('Y-m-d'),
                'data_expiracao' => date('Y-m-d', strtotime('+3 days')),
                'user_vinculado' => $invite,
                'ip' => $core->getIP()
            );

            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                if($core->filtrarPalavrasOfensivas(addslashes($_POST['nome']))){
                    if($core->filtrarPalavrasOfensivas(addslashes($_POST['username']))){
                        if(!$core->isExists('usuarios', 'WHERE email = "'.addslashes($_POST['email']).'"')){
                            if(!$core->isExists('usuarios', 'WHERE username = "'.addslashes($_POST['username']).'"')){
                                if($user->validaIP($core->getIP())){
                                    if(addslashes($_POST['senha']) == addslashes($_POST['confirmar_senha'])){
                                        if($core->insert('usuarios', $campos)){

                                            $user->enviaConfirmacaoCadastro(addslashes($_POST['username']));

                                            if($user->login(addslashes($_POST['username']), md5(addslashes($_POST['senha'])))){
                                                $core->msg('sucesso', 'Cadastro Realizado.');
                                                header('Location: '.BASE.'portal/');
                                            }
                                        } else {
                                            $core->msg('error', 'Ocorreu um Erro ao Registrar.');
                                        }
                                    } else {
                                        $core->msg('error', 'Confirmação de senha diferente.');
                                    }
                                } else {
                                    $core->msg('error', 'Número de Contas por Máquina esgotado.');
                                }
                            } else {
                                $core->msg('error', 'Este Username já foi utilizado por outro Usuário.');
                            }
                        } else {
                            $core->msg('error', 'Este E-mail já foi utilizado por outro Usuário.');
                        }
                    } else {
                        $core->msg('error', 'Não é permitido palavras ofensivas ou bloqueadas.');
                    }
                } else {
                    $core->msg('error', 'Não é permitido palavras ofensivas ou bloqueadas.');
                }
            } else {
                $core->msg('error', 'E-mail inválido.');
            }
        }
    }
?>

<?php require_once 'front/header-front.php'; ?>

<div class="secao-cadastro">
    <div class="stm-container">
        <h2>Ainda não tem Cadastro? Cadastre-se!</h2>
        
        <form id="formCadastro" action="" method="post" autocomplete="off">
            <input type="hidden" name="token_form_cadastro" value="<?php echo md5(time()); ?>" />
            
            <div class="img-form desktop">
                <img src="<?php echo BASE; ?>assets/goku-form.png" />
            </div>
            <div class="box-form">
                <div class="campos">
                    <input type="text" name="nome" value="" placeholder="Nome" required />
                </div>
                <div class="campos">
                    <input type="text" name="username" style="text-transform: lowercase;" value="" placeholder="Username" required />
                </div>
                <div class="separador"></div>
                <div class="campos">
                    <input type="password" name="senha" value="" placeholder="Senha" required />
                </div>
                <div class="campos">
                    <input type="password" name="confirmar_senha" value="" placeholder="Confirme a senha" required />
                </div>
                <div class="separador"></div>
                <div class="campos">
                    <input type="email" name="email" value="" placeholder="E-mail" required />
                </div>
                <div class="separador"></div>
                <div class="campos">
                    <label for="aceite">
                        <input type="checkbox" id="aceite" name="aceite" value="1" required />
                        Declaro que li e aceito as regras do jogo
                    </label>
                </div>
                <div class="botoes-form">
                    <a href="<?php echo BASE; ?>login" class="bt-login">Login</a>
                    <input type="submit" id="cadastrar" name="cadastrar" class="bt-cadastrar" value="Registrar" />
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once 'front/footer-front.php'; ?>
