<?php    
    // DO NOT redirect logged-in users here - let them see the login form
    // This allows the page to be accessed after logout
    
    if(isset($_POST['username'])){
        if($_SESSION['token_form_login'] == addslashes($_POST['token_form_login'])){
            $core->controleLogin($core->getIP(), addslashes($_POST['username']), addslashes($_POST['senha']));
            
            if($user->login(addslashes(htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8')), md5(addslashes($_POST['senha'])))){
                header('Location: '.BASE.'portal');
                exit;
            } else {
                $core->msg('error', 'Usuário ou Senha invalidos!');
            }
        }
    }
    
    if(!$_POST){
        $_SESSION['token_form_login'] = md5(time());
    }
        
    if(isset($_REQUEST['recuperar'])){
        if($_SESSION['token_form_login'] == addslashes($_POST['token_form_login'])){
            if($user->recuperarSenha(addslashes($_POST['email']))){
                $core->msg('sucesso', 'Recuperação enviada com sucesso.');
                header('Location: '.BASE.'login');
            } else {
                $core->msg('error', 'Ocorreu um erro ao enviar a recuperação de senha.');
            }
        }
    }
    
    if(isset($_REQUEST['resetar'])){
        if($_SESSION['token_form_login'] == addslashes($_POST['token_form_login'])){
            if($user->novaSenha(Url::getURL(2), addslashes($_POST['senha']))){
                $core->msg('sucesso', 'Senha alterada com sucesso.');
                header('Location: '.BASE.'login');
            } else {
                $core->msg('error', 'Erro ao alterar senha.');
            }
        }
    }
?>

<?php require_once 'front/header-front.php'; ?>

<div class="secao-login">
    <div class="stm-container">
        <h2>Efetue Login!</h2>
        
        <form id="formLogin" action="" method="post" autocomplete="off">
            <input type="hidden" name="token_form_login" value="<?php echo md5(time()); ?>" />
            
            <div class="img-form desktop">
                <img src="<?php echo BASE; ?>assets/goku-form-login.png" />
            </div>
            <div class="box-form">
                <?php if(Url::getURL(1) == 'recuperacao'){ ?>
                    <h2>Recuperação de Senha</h2>
                    <div class="campos">
                        <input type="text" name="email" value="" style="text-transform: lowercase; font-size: 14px;" placeholder="Digite seu E-mail para recuperação" required />
                    </div>
                    <div class="botoes-form">
                        <a href="<?php echo BASE; ?>login" class="bt-login">Login</a>
                        <input type="submit" id="recuperar" name="recuperar" class="bt-cadastrar" value="Enviar" />
                    </div>
                <?php } else if(Url::getURL(1) == 'confirmacao'){ ?>
                    <?php 
                        if(empty(Url::getURL(2)) || empty(Url::getURL(3))){
                            $core->msg('error', 'Não é possível alterar a senha: dados em falta.');
                            header('Location: '.BASE.'login');
                        }
                    ?>
                    <?php if($user->getRecuperacao(Url::getURL(2), Url::getURL(3))){ ?>
                        <h2>Nova Senha</h2>
                        <div class="campos">
                            <input type="password" name="senha" value="" placeholder="Nova senha" required />
                        </div>
                        <div class="botoes-form">
                            <a href="<?php echo BASE; ?>login" class="bt-login">Login</a>
                            <input type="submit" id="resetar" name="resetar" class="bt-cadastrar" value="Confirmar" />
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <h2>Login de Usuários</h2>
                    <div class="campos username">
                        <input type="text" name="username" style="text-transform: lowercase;" value="" placeholder="Nome de Usuário" required />
                    </div>
                    <div class="campos">
                        <input type="password" name="senha" value="" placeholder="Senha" required />
                    </div>
                    <div class="botoes-form">
                        <a href="<?php echo BASE; ?>login/recuperacao" class="recuperacao">Esqueci minha senha</a>
                        <a href="<?php echo BASE; ?>cadastro" class="bt-login">Cadastrar</a>
                        <input type="submit" id="logar" name="logar" class="bt-cadastrar" value="Entrar" />
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<?php require_once 'front/footer-front.php'; ?>