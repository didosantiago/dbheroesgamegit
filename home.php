<?php 
    // Redirect logged-in users to portal
    if(isset($_SESSION['user_logado']) && $_SESSION['user_logado'] === true){
        header('Location: '.BASE.'meus-personagens');
        exit;
    }
    session_start();
    if(!$_POST){
        $_SESSION['token_form_login_home'] = md5(time());
    }
    
    if(isset($_POST['username'])){
        if($_SESSION['token_form_login_home'] == addslashes($_POST['token_form_login_home'])){
            $core->controleLogin($core->getIP(), addslashes($_POST['username']), addslashes($_POST['senha']));
            
            if($user->login(addslashes(htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8')), md5(addslashes($_POST['senha'])))){
                $core->msg('sucesso', 'Bem vindo!');
                header('Location: '.BASE.'portal');
            } else {
                $core->msg('error', 'Usuário ou Senha invalidos!');
            }
        }
    }
?>

<?php require_once 'front/header-front.php'; ?>

<div class="secao-login">
    <div class="stm-container">
        <form id="formLogin" action="<?php echo BASE; ?>login" method="post" autocomplete="off">
            <input type="hidden" name="token_form_login_home" value="<?php echo md5(time()); ?>" />
            
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

<div class="secao-destaques desktop">
    <div class="stm-container">
        <h2>DESTAQUES E NOVIDADES</h2>
        <ul class="slider-destaques">
            <li>
                <div class="img-slider">
                    <a href="<?php echo BASE; ?>">
                        <img src="<?php echo BASE; ?>assets/front/slider-2.jpg" alt="" />
                    </a>
                </div>
                <div class="infos-slider">
                    <h3>Novidades no Mercado da Comunidade</h3>
                    <div class="miniaturas">
                        
                    </div>
                    <h4>Troca de Coins e Golds</h4>
                    <h5>Solicite itens que deseja!</h5>
                </div>
            </li>
            
            <li>
                <div class="img-slider">
                    <a href="<?php echo BASE; ?>">
                        <img src="<?php echo BASE; ?>assets/front/slider-1.jpg" alt="" />
                    </a>
                </div>
                <div class="infos-slider">
                    <h3>Adesivos de Perfil</h3>
                    <div class="miniaturas">
                        <img src="<?php echo BASE; ?>assets/front/slider-mini-1.jpg" alt="" />
                        <img src="<?php echo BASE; ?>assets/front/slider-mini-2.jpg" alt="" />
                        <img src="<?php echo BASE; ?>assets/front/slider-mini-4.jpg" alt="" />
                        <img src="<?php echo BASE; ?>assets/front/slider-mini-3.jpg" alt="" />
                    </div>
                    <h4>Já Disponível</h4>
                    <h5>Confira!</h5>
                </div>
            </li>
        </ul>
    </div>
</div>

<div class="publicidade-home" style="padding: 10px 0; text-align: center;">
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- Home -->
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-7787997452337920"
         data-ad-slot="3111461223"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>

<div class="secao-jogadores desktop">
    <div class="stm-container">
        <h2>QUANTIDADE DE PERSONAGENS POR GUERREIRO</h2>
        <ul class="lista-personagens">
            <?php $administrar->getJogadoresPorPersonagens(); ?>
        </ul>
    </div>
</div>
            

<?php require_once 'front/footer-front.php'; ?>
