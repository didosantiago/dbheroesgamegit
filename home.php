<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginError = false; // controla o popup

// ===== PROCESSAR LOGIN =====
if (isset($_POST['username']) && isset($_POST['senha'])) {

    $core->controleLogin(
        $core->getIP(),
        addslashes($_POST['username']),
        addslashes($_POST['senha'])
    );

    if ($user->login(
        addslashes(htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8')),
        md5(addslashes($_POST['senha']))
    )) {

        $_SESSION['login_success']  = true;
        $_SESSION['username_logged'] = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');

        header('Location: ' . BASE . 'meus-personagens');
        exit;

    } else {
        // NÃO usa alert() aqui
        $core->msg('error', 'Nome de usuário ou senha inválida');
        $loginError = true; // ativa popup
    }
}

// ===== TOKEN DO FORMULÁRIO =====
if (!isset($_SESSION['tokenformlogin'])) {
    $_SESSION['tokenformlogin'] = md5(time());
}

// ===== MENSAGEM DE LOGOUT =====
$show_welcome_back = false;
$logout_username   = '';

if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $show_welcome_back = true;

    if (isset($_SESSION['logout_username'])) {
        $logout_username = $_SESSION['logout_username'];
        unset($_SESSION['logout_username']);
    }

    unset($_SESSION['logout_success']);
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Heroes - O Melhor MMORPG de Dragon Ball</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
            color: #fff;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        /* ===== HEADER ===== */
        .home-header {
            background: rgba(0, 0, 0, 0.95);
            padding: 15px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 2px solid rgba(255, 193, 7, 0.3);
        }
        
        .home-header .container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        /* ===== BIGGER LOGO ===== */
        .home-logo {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        
        .home-logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        
        .home-logo a:hover {
            transform: scale(1.05);
        }
        
        .home-logo-svg {
            width: 180px;  /* ✅ BIGGER - was 60px */
            height: 180px;
            margin: -70px 0px -50px 0px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .home-logo-svg svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.6));
        }
        
        .home-logo img {
            height: 80px;  /* ✅ BIGGER - was 60px */
            width: auto;
            display: block;
            filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.6));
        }
        
        /* ===== NAV ===== */
        .home-nav {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
            justify-content: flex-end;
            margin: -10px 0px 0px 0px;
            padding-right: 50px;
        }
        
        .home-nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            padding: 8px 14px;
            border-radius: 6px;
            position: relative;
            white-space: nowrap;
        }
        
        .home-nav a:hover {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
            transform: translateY(-2px);
        }
        
        .home-nav a::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #ffc107;
            transition: width 0.3s ease;
        }
        
        .home-nav a:hover::after {
            width: 70%;
        }
        
        /* ===== HERO SECTION ===== */
        .home-hero {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -110px 0px -170px 0px;
            padding: 60px 20px;
            position: relative;
        }
        
        .home-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 193, 7, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        
        /* ===== LOGIN CONTAINER ===== */
        .login-container {
            background: rgba(0, 0, 0, 0.9);
            border-radius: 25px;
            padding: 50px 60px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 193, 7, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header img {
            width: 340px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-header h2 {
            color: #fff;
            font-size: 26px;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        
        .login-header p {
            color: #ffc107;
            font-size: 15px;
            margin: 0;
            font-weight: 500;
        }
        
        /* ===== LOGOUT SUCCESS ===== */
        .logout-success {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: white;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.5);
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .logout-success i {
            font-size: 28px;
            margin-bottom: 8px;
            display: block;
        }
        
        .logout-success strong {
            font-size: 17px;
            display: block;
            margin-bottom: 5px;
        }
        
        .logout-success p {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
        }
        
        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid rgba(255, 193, 7, 0.4);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ffc107;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.4);
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }
        
        .forgot-password a {
            color: #ffc107;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .forgot-password a:hover {
            color: #ffeb3b;
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            border-radius: 12px;
            color: #ffffffff;      
            text-shadow: 2px 0 #000000, -2px 0 #000000, 0 2px #000000, 0 -2px #000000,
               1px 1px #000000, -1px -1px #000000, 1px -1px #000000, -1px 1px #000000;      
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 193, 7, 0.6);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-footer p {
            color: #999;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .btn-register {
            display: inline-block;
            padding: 13px 35px;
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 15px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.5);
        }
        
        /* ===== PAGE FOOTER ===== */
        .page-footer {
            background: rgba(0, 0, 0, 0.95);
            padding: 50px 20px 30px;
            margin-top: 80px;
            border-top: 2px solid rgba(255, 193, 7, 0.2);
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-social {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .social-section {
            text-align: center;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
        }
        
        .social-section h3 {
            color: #ffc107;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .social-section p {
            color: #999;
            font-size: 14px;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .social-section a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .instagram-btn {
            background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 50%, #fcb045 100%);
            color: white;
        }
        
        .instagram-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(131, 58, 180, 0.5);
        }
        
        .facebook-btn {
            background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
            color: white;
        }
        
        .facebook-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(59, 89, 152, 0.5);
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #ffc107;
        }
        
        .footer-bottom {
            text-align: center;
            color: #666;
            font-size: 13px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .footer-bottom p {
            margin: 8px 0;
        }
        
        .footer-bottom .shield-icon {
            color: #4caf50;
            margin-right: 5px;
        }
        
        .footer-social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .footer-social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .footer-social-icons a:hover {
            background: #ffc107;
            color: #000;
            transform: translateY(-5px);
        }

        /* ===== POPUP GLOBAL (mesmo estilo de golpes) ===== */
        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(3px);
            z-index: 9998;
        }

        .popup-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #121531;
            border-radius: 18px;
            padding: 30px 40px;
            max-width: 480px;
            width: 90%;
            color: #fff;
            text-align: center;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.8);
            z-index: 9999;
        }

        .popup-message.popup-error {
            border: 2px solid #ff4b4b;
            box-shadow: 0 0 25px rgba(255, 75, 75, 0.7);
        }

        .popup-icon {
            font-size: 32px;
            margin-bottom: 10px;
            color: #ff4b4b;
        }

        .popup-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .popup-text {
            font-size: 15px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .popup-close {
            background: transparent;
            border: 1px solid #ff4b4b;
            color: #ff4b4b;
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .popup-close:hover {
            background: #ff4b4b;
            color: #fff;
        }

        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1100px) {
            .home-nav {
                gap: 15px;
            }
            
            .home-nav a {
                font-size: 13px;
                padding: 6px 10px;
            }
        }
        
        @media (max-width: 968px) {
            .home-logo-svg,
            .home-logo img {
                height: 65px;
                width: 65px;
            }
            
            .home-nav {
                gap: 12px;
            }
            
            .home-nav a {
                font-size: 12px;
                padding: 6px 8px;
            }
        }
        
        @media (max-width: 768px) {
            .home-logo-svg,
            .home-logo img {
                height: 55px;
                width: 55px;
            }
            
            .home-nav {
                gap: 8px;
            }
            
            .home-nav a {
                font-size: 11px;
                padding: 5px 6px;
            }
            
            .login-container {
                padding: 35px 30px;
            }
            
            .login-header h2 {
                font-size: 22px;
            }
            
            .footer-social {
                gap: 30px;
            }
            
            .social-section {
                min-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .home-logo-svg,
            .home-logo img {
                height: 50px;
                width: 50px;
            }
            
            .home-nav a {
                font-size: 10px;
                padding: 4px 5px;
            }
        }

        .home-hero {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            position: relative;
        }

        .home-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 193, 7, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ADICIONE ESSA PARTE NOVA */
        .home-hero-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(76, 175, 80, 0.5);
        }   

    </style>
</head>
<body>
    <div class="bg-overlay"></div>   <!-- overlay em todas as páginas -->
    <!-- ===== HEADER ===== -->
    <div class="home-header">
        <div class="container">
            <!-- ===== BIGGER LOGO ===== -->
            <div class="home-logo">
                <a href="<?php echo BASE; ?>home">
                    <?php if(file_exists('front/svg.php')): ?>
                        <div class="home-logo-svg">
                            <?php require_once 'front/svg.php'; ?>
                        </div>
                    <?php elseif(file_exists('assets/logo.png')): ?>
                        <img src="<?php echo BASE; ?>assets/logo.png" alt="DB Heroes Logo">
                    <?php else: ?>
                        <img src="<?php echo BASE; ?>assets/header.jpg" alt="DB Heroes" style="height: 80px; width: auto;">
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- ===== UPDATED NAV MENU ===== -->
            <div class="home-nav">
                <a href="<?php echo BASE; ?>home">INÍCIO</a>
                <a href="<?php echo BASE; ?>cadastro">CRIE SUA CONTA</a>
                <a href="<?php echo BASE; ?>rank">RANKING</a>
                <a href="<?php echo BASE; ?>sobre">SOBRE</a>
            </div>
        </div>
    </div>
    
    <!-- ===== HERO SECTION ===== -->
    <div class="home-hero">
        <div class="login-container">
            <!-- Header -->
            <div class="login-header">
                <img src="<?php echo BASE; ?>assets/test-1.gif" alt="Goku">
                <h2>Login de Usuários</h2>
                <p>Bem-vindo de volta, Guerreiro!</p>
            </div>
            
            <!-- Logout Success Message -->
            <?php if($show_welcome_back): ?>
            <div class="logout-success">
                <i class="fas fa-check-circle"></i>
                <strong>Você saiu com sucesso!</strong>
                <?php if($logout_username): ?>
                <p>Até logo, <?php echo htmlspecialchars($logout_username); ?>! 👋</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="post" action="">
                <input type="hidden" name="tokenformlogin" value="<?php echo $_SESSION['tokenformlogin']; ?>">
                
                <div class="form-group">
                    <input type="text" 
                           name="username" 
                           placeholder="Nome de usuário" 
                           style="text-transform: lowercase;"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <input type="password" 
                           name="senha" 
                           placeholder="Senha" 
                           required>
                </div>
                
                <div class="forgot-password">
                    <a href="<?php echo BASE; ?>login/recuperacao">🔑 Esqueci minha senha</a>
                </div>
                
                <button type="submit" class="btn-login">
                    JOGAR AGORA!
                </button>
            </form>
            
            <!-- Register Link -->
            <div class="login-footer">
                <p>Novo no DB Heroes?</p>
                <a href="<?php echo BASE; ?>cadastro" class="btn-register">
                    🌟 CRIAR CONTA GRÁTIS
                </a>
            </div>
        </div>
    </div>
    
<?php require_once 'front/footer-front.php'; ?>

<?php if ($loginError): ?>
    <div class="popup-overlay"></div>
    <div class="popup-message popup-error">
        <div class="popup-icon">!</div>
        <div class="popup-title">ERRO!</div>
        <div class="popup-text">Nome de usuário ou senha inválida.</div>
        <button class="popup-close" onclick="fecharPopupLogin()">Fechar</button>
    </div>
    <script>
    function fecharPopupLogin() {
        const overlay = document.querySelector('.popup-overlay');
        const popup   = document.querySelector('.popup-message.popup-error');
        if (overlay) overlay.remove();
        if (popup)   popup.remove();
    }
    </script>
<?php endif; ?>
</body>
</html>


