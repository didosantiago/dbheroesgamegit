<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DB Heroes - O melhor MMORPG de Dragon Ball online">
    <link rel="stylesheet" href="<?php echo BASE; ?>assets/dbheroes-vendor.css">
    <link rel="stylesheet" href="<?php echo BASE; ?>assets/dbheroes.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>

        
        /* ===== FIX .STM-CONTAINER FOR PUBLIC PAGES ===== */
        .stm-container {
            width: 100% !important;
            max-width: 1400px !important;
            margin: 0 auto !important;
            padding: 20px !important;
            box-sizing: border-box;
        }
        
        /* ===== FIX .STM-MAIN FOR PUBLIC PAGES ===== */
        .stm-main {
            width: 100% !important;
            max-width: 1400px !important;
            margin: 0 auto !important;
        }
        
        /* ===== PUBLIC HEADER ===== */
        .header-front {
            background: rgba(0, 0, 0, 0.95);
            padding: 15px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 2px solid rgba(255, 193, 7, 0.3);
        }
        
        .header-front .container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        /* ===== LOGO ===== */
        .header-logo {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        
        .header-logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        
        .header-logo a:hover {
            transform: scale(1.05);
        }
        
        .header-logo svg {
            width: 180px;  /* ✅ BIGGER - was 60px */
            height: 180px;
            margin: -70px 0px -50px 0px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-logo-svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.6));
        }
        
        
        .header-logo img {
            height: 80px;  /* ✅ BIGGER - was 60px */
            width: auto;
            display: block;
            filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.6));
        }
        
        /* ===== NAVIGATION ===== */
        .header-nav {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
            justify-content: flex-end;
            margin: -10px 0px 0px 0px;
            padding-right: 90px;
        }
        
        .header-nav a {
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
        
        .header-nav a:hover {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
            transform: translateY(-2px);
        }
        
        .header-nav a::after {
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
        
        .header-nav a:hover::after {
            width: 70%;
        }
        
        /* ===== CONTENT SECTIONS ===== */
        .secao-sobre,
        .secao-dbz,
        .secao-cadastro,
        .secao-ranking,
        .secao-tutorial,
        .secao-forum {
            width: 100%;
            padding: 40px 20px;
        }
        
        .secao-sobre h2,
        .secao-dbz h2,
        .secao-sobre h3,
        .secao-dbz h3 {
            color: #ffc107;
            margin-bottom: 20px;
        }
        
        .secao-sobre img,
        .secao-dbz img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border-radius: 10px;
        }
        
        .secao-sobre p,
        .secao-dbz p {
            line-height: 1.8;
            color: #ddd;
            text-align: justify;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1100px) {
            .stm-container {
                max-width: 95% !important;
                padding: 15px !important;
            }
            
            .header-nav {
                gap: 15px;
            }
            
            .header-nav a {
                font-size: 13px;
                padding: 6px 10px;
            }
        }
        
        @media (max-width: 968px) {
            .header-front {
                padding: 8px 0;
            }
            
            .header-logo svg,
            .header-logo img {
                width: 60px;
                height: 60px;
            }
            
            .header-nav {
                gap: 12px;
            }
            
            .header-nav a {
                font-size: 12px;
                padding: 6px 8px;
            }
            
            .stm-container {
                padding: 10px !important;
            }
        }
        
        @media (max-width: 768px) {
            .header-front {
                padding: 6px 0;
            }
            
            .header-logo svg,
            .header-logo img {
                width: 50px;
                height: 50px;
            }
            
            .header-nav {
                gap: 8px;
            }
            
            .header-nav a {
                font-size: 11px;
                padding: 5px 6px;
            }
        }
        
        @media (max-width: 480px) {
            .header-logo svg,
            .header-logo img {
                width: 45px;
                height: 45px;
            }
            
            .header-nav a {
                font-size: 10px;
                padding: 4px 5px;
            }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <div class="header-front">
        <div class="container">
            <!-- ===== BIGGER LOGO ===== -->
            <div class="header-logo">
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
            <div class="header-nav">
                <a href="<?php echo BASE; ?>home">INÍCIO</a>
                <a href="<?php echo BASE; ?>cadastro">CRIE SUA CONTA</a>
                <a href="<?php echo BASE; ?>rank">RANKING</a>
                <a href="<?php echo BASE; ?>sobre">SOBRE</a>
            </div>
        </div>
    </div>
    <!-- Content starts here -->
