<?php
session_start();

/*
 * ESTE ARQUIVO NÃO DEVE MAIS SER USADO COMO TELA.
 * Sempre que alguém acessar /login, redireciona para o login da home.
 */

// se já estiver logado, manda direto para a área logada
if (!empty($_SESSION['id_usuario'])) {
    header('Location: ' . BASE . 'meus-personagens');
    exit;
}

// se não estiver logado, manda para o login novo na home
header('Location: ' . BASE . 'home#login');   // ajuste o #login se não usar âncora
exit;

// DO NOT redirect logged-in users here - let them see the login form
// This allows the page to be accessed after logout

if(isset($_POST['username'])){
    if($_SESSION['tokenformlogin'] == addslashes($_POST['tokenformlogin'])){
        $core->controleLogin($core->getIP(), addslashes($_POST['username']), addslashes($_POST['senha']));
        
        if($user->login(addslashes(htmlspecialchars($_POST['username'], ENT_QUOTES, "UTF-8")), md5(addslashes($_POST['senha'])))){
            // ✅ IMPROVED: Set success message
            $_SESSION['login_success'] = true;
            $_SESSION['username_logged'] = htmlspecialchars($_POST['username'], ENT_QUOTES, "UTF-8");
            header('Location: '.BASE.'meus-personagens');
            exit;
        } else {
            $core->msg('error', 'Usuário ou Senha inválidos!');
        }
    }
}

if(!$_POST){
    $_SESSION['tokenformlogin'] = md5(time());
}

// Password recovery
if(isset($_REQUEST['recuperar'])){
    if($_SESSION['tokenformlogin'] == addslashes($_POST['tokenformlogin'])){
        if($user->recuperarSenha(addslashes($_POST['email']))){
            $core->msg('sucesso', 'E-mail de recuperação enviado com sucesso! Verifique sua caixa de entrada.');
            // ✅ IMPROVED: Redirect after 2 seconds
            echo '<script>setTimeout(function(){ window.location.href = "'.BASE.'login"; }, 2000);</script>';
        } else {
            $core->msg('error', 'Erro ao enviar e-mail de recuperação. Verifique se o e-mail está correto.');
        }
    }
}

// Password reset
if(isset($_REQUEST['resetar'])){
    if($_SESSION['tokenformlogin'] == addslashes($_POST['tokenformlogin'])){
        if($user->novaSenha(Url::getURL(2), addslashes($_POST['senha']))){
            $core->msg('sucesso', 'Senha alterada com sucesso!');
            // ✅ FIXED: Redirect to home page after password reset
            echo '<script>setTimeout(function(){ window.location.href = "'.BASE.'home"; }, 2000);</script>';
        } else {
            $core->msg('error', 'Erro ao alterar senha. Link pode estar expirado.');
        }
    }
}

// ✅ FIXED: Redirect logout success to home page
if(isset($_GET['logout']) && $_GET['logout'] == 'success'){
    header('Location: '.BASE.'home?logout=success');
    exit;
}
?>

<?php require_once 'front/header-front.php'; ?>

<div class="secao-login">
    <div class="stm-container">
        <!-- ✅ IMPROVED: Add logo/title -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #fff; font-size: 48px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); margin-bottom: 10px;">
                DB HEROES
            </h1>
            <p style="color: #41BCD1; font-size: 18px; font-weight: bold;">
                Bem-vindo de volta, Guerreiro!
            </p>
        </div>
        
        <h2>Efetue Login!</h2>
        
        <form id="formLogin" action="" method="post" autocomplete="off">
            <input type="hidden" name="tokenformlogin" value="<?php echo $_SESSION['tokenformlogin']; ?>">
            
            <div class="img-form desktop">
                <img src="<?php echo BASE; ?>assets/goku-form-login.png">
            </div>
            
            <div class="box-form">
                <?php if(Url::getURL(1) == 'recuperacao'): ?>
                    <!-- PASSWORD RECOVERY FORM -->
                    <h2>Recuperação de Senha</h2>
                    <p style="color: #fff; margin-bottom: 20px; text-align: center;">
                        Digite seu e-mail cadastrado para receber as instruções de recuperação.
                    </p>
                    
                    <div class="campos">
                        <input type="email" 
                               name="email" 
                               value="" 
                               style="text-transform: lowercase; font-size: 14px;" 
                               placeholder="Digite seu E-mail para recuperação" 
                               required>
                    </div>
                    
                    <div class="botoes-form">
                        <a href="<?php echo BASE; ?>login" class="bt-login">← Voltar</a>
                        <input type="submit" 
                               id="recuperar" 
                               name="recuperar" 
                               class="bt-cadastrar" 
                               value="Enviar">
                    </div>
                    
                <?php elseif(Url::getURL(1) == 'confirmacao'): ?>
                    <!-- PASSWORD RESET FORM -->
                    <?php if(empty(Url::getURL(2)) || empty(Url::getURL(3))): ?>
                        <?php 
                            $core->msg('error', 'Não é possível alterar a senha: dados em falta.');
                            header('Location: '.BASE.'login');
                            exit;
                        ?>
                    <?php endif; ?>
                    
                    <?php if($user->getRecuperacao(Url::getURL(2), Url::getURL(3))): ?>
                        <h2>Nova Senha</h2>
                        <p style="color: #fff; margin-bottom: 20px; text-align: center;">
                            Digite sua nova senha abaixo.
                        </p>
                        
                        <div class="campos">
                            <input type="password" 
                                   name="senha" 
                                   value="" 
                                   placeholder="Nova senha (mínimo 6 caracteres)" 
                                   minlength="6"
                                   required>
                        </div>
                        
                        <div class="botoes-form">
                            <a href="<?php echo BASE; ?>login" class="bt-login">← Voltar</a>
                            <input type="submit" 
                                   id="resetar" 
                                   name="resetar" 
                                   class="bt-cadastrar" 
                                   value="Confirmar">
                        </div>
                    <?php else: ?>
                        <div style="color: #f44336; text-align: center; padding: 20px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>Link de recuperação inválido ou expirado!</p>
                            <a href="<?php echo BASE; ?>login" class="bt-cadastrar" style="margin-top: 15px; display: inline-block;">
                                Voltar ao Login
                            </a>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- STANDARD LOGIN FORM -->
                    <h2>Login de Usuários</h2>
                    
                    <div class="campos username">
                        <input type="text" 
                               name="username" 
                               style="text-transform: lowercase;" 
                               value="" 
                               placeholder="Nome de Usuário" 
                               required 
                               autofocus>
                    </div>
                    
                    <div class="campos">
                        <input type="password" 
                               name="senha" 
                               value="" 
                               placeholder="Senha" 
                               required>
                    </div>
                    
                    <div class="botoes-form">
                        <a href="<?php echo BASE; ?>login/recuperacao" class="recuperacao">
                            🔑 Esqueci minha senha
                        </a>
                        <a href="<?php echo BASE; ?>cadastro" class="bt-login">
                            📝 Cadastrar
                        </a>
                        <input type="submit" 
                               id="logar" 
                               name="logar" 
                               class="bt-cadastrar" 
                               value="Entrar">
                    </div>
                    
                    <!-- ✅ NEW: Additional info -->
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <p style="color: #999; font-size: 14px; margin-bottom: 10px;">
                            Novo no DB Heroes?
                        </p>
                        <a href="<?php echo BASE; ?>cadastro" 
                           class="bt-cadastrar" 
                           style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); display: inline-block; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;">
                            🌟 Criar Conta Grátis
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </form>
        
        <!-- ✅ NEW: Footer info -->
        <div style="text-align: center; margin-top: 40px; color: #666; font-size: 13px;">
            <p>
                <i class="fas fa-shield-alt"></i> Seus dados estão protegidos e criptografados
            </p>
            <p style="margin-top: 5px;">
                © 2024 DB Heroes - Todos os direitos reservados
            </p>
        </div>
    </div>
</div>

<style>
/* ✅ IMPROVED: Better form styling */
.secao-login {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.box-form {
    background: rgba(0, 0, 0, 0.7);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.campos input {
    width: 100%;
    padding: 15px;
    border: 2px solid #41BCD1;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    font-size: 16px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.campos input:focus {
    outline: none;
    border-color: #4caf50;
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.3);
}

.bt-cadastrar {
    background: linear-gradient(135deg, #41BCD1 0%, #2196f3 100%);
    padding: 15px 40px;
    border: none;
    border-radius: 25px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
}

.bt-cadastrar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(65, 188, 209, 0.4);
}

.recuperacao {
    color: #41BCD1;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.recuperacao:hover {
    color: #4caf50;
    text-decoration: underline;
}
</style>

<?php require_once 'front/footer-front.php'; ?>
