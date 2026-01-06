<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'core/config.php';
require_once 'core/auth.php';
require_once 'core/CharacterRequired.php';

if(!isset($_SESSION['PERSONAGEMID'])){
    header('Location: '.BASE.'portal');
    exit;
}

$idPersonagem = $_SESSION['PERSONAGEMID'];
$personagem_check = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);

if($personagem_check->hp <= 0){
    $core->msg('error', 'Você está derrotado! Vá ao hospital para se curar.');
    header('Location: '.BASE.'hospital');
    exit;
}

if($core->proccessInExecution()){
    header('Location: '.BASE.'profile');
}

$stats = $veraoexplorando->getStats($idPersonagem);
$energia_restante = intval($personagem_check->energia) - intval($personagem_check->energia_usada);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verão Litorando - DBHeroes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            image-rendering: -moz-crisp-edges;
            image-rendering: crisp-edges;
        }

        body {
            background: linear-gradient(180deg, #4fc3f7 0%, #29b6f6 40%, #fdd835 60%, #f9a825 100%), 
                        repeating-conic-gradient(from 45deg at 10% 50%, #ffffff 0deg 90deg, transparent 90deg 180deg);
            background-size: 100% 100%, 20px 20px;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Courier New', monospace;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '☁️';
            position: fixed;
            top: 10%;
            left: -10%;
            width: 100px;
            height: 40px;
            background: linear-gradient(#fff 20%, transparent 20%), 
                        linear-gradient(#fff 40%, transparent 40%), 
                        linear-gradient(#fff 60%, transparent 60%);
            background-size: 20px 20px;
            box-shadow: 0 0 0 4px #fff,
                        400px 50px 0 0px #fff,
                        400px 50px 0 4px #fff,
                        800px 100px 0 0px #fff,
                        800px 100px 0 4px #fff;
            animation: cloudMove 30s linear infinite;
            opacity: 0.7;
            z-index: 0;
        }

        @keyframes cloudMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(100vw); }
        }

        body::after {
            content: '☀️';
            position: fixed;
            top: 5%;
            right: 10%;
            width: 80px;
            height: 80px;
            background: #ffd54f;
            border-radius: 0%;
            box-shadow: 0 0 0 4px #ffeb3b,
                        0 0 0 8px #ffd54f,
                        20px 20px 0 0 rgba(255, 235, 59, 0.3),
                        -20px -20px 0 0 rgba(255, 235, 59, 0.3),
                        20px -20px 0 0 rgba(255, 235, 59, 0.3),
                        -20px 20px 0 0 rgba(255, 235, 59, 0.3);
            animation: pixelSunRotate 8s steps(8) infinite;
            z-index: 0;
        }

        @keyframes pixelSunRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .event-header {
            background: #2196f3;
            border: 8px solid #0d47a1;
            padding: 50px 40px;
            margin: 0 0 40px 0;
            max-width: 1000px;
            box-shadow: 0 8px 0 #1565c0,
                        0 16px 0 #0d47a1,
                        0 24px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .event-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(0deg, transparent 0px, transparent 3px, rgba(255, 255, 255, 0.1) 3px, rgba(255, 255, 255, 0.1) 4px),
                repeating-linear-gradient(90deg, transparent 0px, transparent 3px, rgba(255, 255, 255, 0.1) 3px, rgba(255, 255, 255, 0.1) 4px);
            pointer-events: none;
        }

        .event-header::after {
            content: '🏖️';
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 40px;
            animation: pixelBlink 2s steps(2) infinite;
            filter: drop-shadow(0 0 10px #ffeb3b);
        }

        @keyframes pixelBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .event-header h1 {
            position: relative;
            color: #fff;
            font-size: 48px;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 20px;
            z-index: 1;
            font-weight: 900;
            text-shadow: 4px 0 0 #0d47a1,
                        -4px 0 0 #0d47a1,
                        0 4px 0 #0d47a1,
                        0 -4px 0 #0d47a1,
                        4px 4px 0 #0d47a1,
                        -4px -4px 0 #0d47a1,
                        4px -4px 0 #0d47a1,
                        -4px 4px 0 #0d47a1,
                        0 8px 20px rgba(0, 0, 0, 0.5);
            animation: pixelShake 0.5s steps(4) infinite;
        }

        @keyframes pixelShake {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(-2px, 2px); }
            50% { transform: translate(2px, -2px); }
            75% { transform: translate(-2px, -2px); }
        }

        .event-subtitle {
            position: relative;
            color: #e3f2fd;
            font-size: 18px;
            margin-bottom: 30px;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 #0d47a1,
                        4px 4px 10px rgba(0, 0, 0, 0.5);
            font-weight: 700;
        }

        .progress-section {
            position: relative;
            background: #1565c0;
            border: 6px solid #0d47a1;
            padding: 30px;
            margin-top: 25px;
            z-index: 1;
            box-shadow: inset 0 0 0 2px #1976d2,
                        inset 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #42a5f5;
            padding: 25px;
            border: 6px solid #1976d2;
            text-align: center;
            transition: all 0.2s steps(2);
            box-shadow: 0 4px 0 #1565c0,
                        0 8px 0 #0d47a1,
                        0 12px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            pointer-events: none;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 0 #1565c0,
                        0 16px 0 #0d47a1,
                        0 20px 30px rgba(0, 0, 0, 0.4);
        }

        .stat-card h3 {
            color: #0d47a1;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 rgba(255, 255, 255, 0.5);
            font-weight: 900;
        }

        .stat-value {
            color: #ffeb3b;
            font-size: 44px;
            font-weight: 900;
            text-shadow: 3px 0 0 #0d47a1,
                        -3px 0 0 #0d47a1,
                        0 3px 0 #0d47a1,
                        0 -3px 0 #0d47a1,
                        3px 3px 0 #0d47a1,
                        0 6px 15px rgba(0, 0, 0, 0.5);
            animation: numberPulse 1s steps(2) infinite;
        }

        @keyframes numberPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .progress-bar-container {
            background: #0d47a1;
            height: 40px;
            border: 6px solid #1976d2;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.5),
                        0 4px 0 #1565c0;
            margin-bottom: 0;
        }

        .progress-bar {
            height: 100%;
            background: repeating-linear-gradient(90deg, #4caf50 0px, #4caf50 10px, #66bb6a 10px, #66bb6a 20px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 900;
            font-size: 18px;
            min-width: 100%;
            transition: width 0.3s steps(10);
            text-shadow: 2px 2px 0 #1b5e20,
                        4px 4px 10px rgba(0, 0, 0, 0.6);
            animation: progressAnimate 0.5s steps(2) infinite;
            box-shadow: inset 0 -4px 0 #2e7d32;
            white-space: nowrap;
            padding: 0 10px;
        }

        @keyframes progressAnimate {
            0% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
        }

        .page-title {
            text-align: center;
            margin: 0px 0px 40px 0px;
            max-width: 1000px;
            background: #ff9800;
            padding: 40px;
            border: 8px solid #e65100;
            box-shadow: 0 8px 0 #f57c00,
                        0 16px 0 #e65100,
                        0 24px 40px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .page-title::before {
            content: '🏝️';
            position: absolute;
            top: -25px;
            left: 30px;
            font-size: 50px;
            animation: islandBob 2s ease-in-out infinite;
        }

        .page-title::after {
            content: '🌴';
            position: absolute;
            top: -25px;
            right: 30px;
            font-size: 50px;
            animation: islandBob 2s ease-in-out infinite 0.5s;
        }

        @keyframes islandBob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .page-title h2 {
            position: relative;
            color: #fff;
            font-size: 38px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 4px;
            text-shadow: 4px 0 0 #e65100,
                        -4px 0 0 #e65100,
                        0 4px 0 #e65100,
                        0 -4px 0 #e65100,
                        4px 4px 0 #e65100,
                        0 8px 20px rgba(0, 0, 0, 0.5);
            z-index: 1;
            font-weight: 900;
        }

        .page-title p {
            position: relative;
            color: #fff3e0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 #e65100;
            z-index: 1;
            font-weight: 700;
        }

        .warriors-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            list-style: none;
            padding: 0;
            margin: 0px 0px;
            max-width: 1000px;
        }

        /* SAIYAN POWER AURA CARDS */
        .warrior-card {
            background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
            transition: all 0.4s ease;
            position: relative;
            border: 3px solid #ff6b00; /* Saiyan Aura Effect */
        }

        /* Aura Effect */
        .warrior-card::before {
            content: '';
            position: absolute;
            inset: -20px;
            background: radial-gradient(circle at center, rgba(255, 107, 0, 0.3) 0%, rgba(255, 165, 0, 0.2) 30%, transparent 70%);
            animation: saiyanAura 2s ease-in-out infinite;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 0;
        }

        @keyframes saiyanAura {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .warrior-card:hover::before {
            opacity: 1;
        }

        /* Energy Sparks */
        .warrior-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 0, 0.4) 0%, transparent 3%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 0, 0.4) 0%, transparent 2%),
                radial-gradient(circle at 60% 80%, rgba(255, 255, 0, 0.4) 0%, transparent 2.5%),
                radial-gradient(circle at 30% 70%, rgba(255, 255, 0, 0.4) 0%, transparent 3%);
            animation: energySparks 1.5s infinite;
            opacity: 0;
            pointer-events: none;
        }

        @keyframes energySparks {
            0%, 100% { opacity: 0; }
            10%, 30%, 50%, 70% { opacity: 1; }
            20%, 40%, 60%, 80% { opacity: 0; }
        }

        .warrior-card:hover::after {
            animation: energySparks 0.8s infinite;
        }

        .warrior-card:hover {
            transform: translateY(-15px);
            border-color: #ffa500;
            box-shadow: 
                0 0 30px rgba(255, 107, 0, 0.6),
                0 0 60px rgba(255, 107, 0, 0.3),
                0 25px 70px rgba(0, 0, 0, 0.6);
        }

        /* Power Level Badge */
        .order-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 65px;
            height: 65px;
            background: linear-gradient(145deg, #ff6b00 0%, #ff8c00 100%);
            border-radius: 50%;
            border: 4px solid #ffd700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 900;
            color: #fff;
            z-index: 10;
            box-shadow: 
                0 0 20px rgba(255, 107, 0, 0.8),
                0 0 40px rgba(255, 107, 0, 0.4),
                inset 0 0 15px rgba(255, 215, 0, 0.3);
            text-shadow: 
                0 0 10px rgba(255, 255, 0, 0.8),
                2px 2px 4px rgba(0, 0, 0, 0.8);
            animation: powerPulse 1.5s ease-in-out infinite;
        }

        @keyframes powerPulse {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 20px rgba(255, 107, 0, 0.8), 0 0 40px rgba(255, 107, 0, 0.4), inset 0 0 15px rgba(255, 215, 0, 0.3);
            }
            50% { 
                transform: scale(1.1); 
                box-shadow: 0 0 30px rgba(255, 107, 0, 1), 0 0 60px rgba(255, 107, 0, 0.6), inset 0 0 20px rgba(255, 215, 0, 0.5);
            }
        }

        .warrior-card:hover .order-badge {
            animation: powerExplosion 0.6s ease-in-out infinite;
        }

        @keyframes powerExplosion {
            0%, 100% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.2); filter: brightness(1.5); }
        }

        /* Super Saiyan State - Green Aura */
        .warrior-current {
            border-color: #00ff00;
            animation: superSaiyanGlow 1.5s ease-in-out infinite;
        }

        @keyframes superSaiyanGlow {
            0%, 100% { 
                box-shadow: 0 0 30px rgba(0, 255, 0, 0.6), 0 0 60px rgba(0, 255, 0, 0.3), 0 15px 50px rgba(0, 0, 0, 0.5);
            }
            50% { 
                box-shadow: 0 0 50px rgba(0, 255, 0, 0.9), 0 0 100px rgba(0, 255, 0, 0.5), 0 15px 50px rgba(0, 0, 0, 0.5);
            }
        }

        .warrior-current::before {
            background: radial-gradient(circle at center, rgba(0, 255, 0, 0.4) 0%, rgba(50, 255, 50, 0.3) 30%, transparent 70%);
            opacity: 1;
        }

        .warrior-current .order-badge {
            background: linear-gradient(145deg, #00ff00 0%, #00ff7f 100%);
            border-color: #ffff00;
            box-shadow: 0 0 30px rgba(0, 255, 0, 1), 0 0 60px rgba(0, 255, 0, 0.6);
            animation: superSaiyanBadge 0.8s ease-in-out infinite;
        }

        @keyframes superSaiyanBadge {
            0%, 100% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.15); filter: brightness(1.5); }
        }

        /* Powered Down - Blue Aura */
        .warrior-completed {
            border-color: #0080ff;
            opacity: 0.85;
        }

        .warrior-completed::before {
            background: radial-gradient(circle at center, rgba(0, 128, 255, 0.3) 0%, rgba(64, 164, 255, 0.2) 30%, transparent 70%);
        }

        .warrior-completed .order-badge {
            background: linear-gradient(145deg, #0080ff 0%, #00bfff 100%);
            border-color: #87ceeb;
            box-shadow: 0 0 20px rgba(0, 128, 255, 0.6), 0 0 40px rgba(0, 128, 255, 0.3);
        }

        /* Locked/Sealed */
        .warrior-locked {
            border-color: #666;
            opacity: 0.6;
        }

        .warrior-locked::before {
            background: radial-gradient(circle at center, rgba(128, 128, 128, 0.2) 0%, transparent 50%);
        }

        .warrior-locked .order-badge {
            background: linear-gradient(145deg, #666 0%, #888 100%);
            border-color: #999;
            box-shadow: 0 0 15px rgba(128, 128, 128, 0.4);
            animation: none;
        }

        /* Battle Arena Image */
        .warrior-image {
            position: relative;
            height: 280px;
            background: radial-gradient(ellipse at center, #1a1a1a 0%, #0a0a0a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Energy Lines Background */
        .warrior-image::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: 
                repeating-linear-gradient(90deg, transparent 0px, transparent 40px, rgba(255, 107, 0, 0.1) 40px, rgba(255, 107, 0, 0.1) 42px),
                repeating-linear-gradient(0deg, transparent 0px, transparent 40px, rgba(255, 107, 0, 0.1) 40px, rgba(255, 107, 0, 0.1) 42px);
            animation: energyGrid 3s linear infinite;
        }

        @keyframes energyGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(40px, 40px); }
        }

        .warrior-image img {
            max-width: 564px;
            max-height: 200px;
            object-fit: contain;
            transition: all 0.4s ease;
            filter: drop-shadow(0 0 20px rgba(255, 107, 0, 0.6)) drop-shadow(0 10px 25px rgba(0, 0, 0, 0.8));
            position: relative;
            z-index: 1;
        }

        .warrior-current .warrior-image {
            background: radial-gradient(ellipse at center, #1a2e1a 0%, #0a1a0a 100%);
        }

        .warrior-current .warrior-image::before {
            background: 
                repeating-linear-gradient(90deg, transparent 0px, transparent 40px, rgba(0, 255, 0, 0.15) 40px, rgba(0, 255, 0, 0.15) 42px),
                repeating-linear-gradient(0deg, transparent 0px, transparent 40px, rgba(0, 255, 0, 0.15) 40px, rgba(0, 255, 0, 0.15) 42px);
        }

        .warrior-current .warrior-image img {
            animation: powerUpFloat 2s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(0, 255, 0, 0.8)) drop-shadow(0 10px 25px rgba(0, 0, 0, 0.8));
        }

        @keyframes powerUpFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .warrior-card:hover .warrior-image img {
            transform: scale(1.1);
            filter: drop-shadow(0 0 40px rgba(255, 165, 0, 1)) drop-shadow(0 15px 35px rgba(0, 0, 0, 0.9));
        }

        .warrior-locked .warrior-image img {
            filter: grayscale(100%) brightness(0.4);
        }

        .warrior-completed .warrior-image img {
            filter: saturate(0.7) brightness(0.8);
        }

        /* Power Stats Panel */
        .warrior-content {
            padding: 25px;
            background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
            position: relative;
        }

        .warrior-name {
            color: #ffa500;
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(255, 165, 0, 0.8), 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .warrior-description {
            color: #ccc;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 18px;
            min-height: 55px;
            font-weight: 600;
        }

        /* Scouter Stats Display */
        .warrior-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 18px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 12px;
            padding: 18px;
            border: 2px solid #ff6b00;
            box-shadow: 0 0 20px rgba(255, 107, 0, 0.3), inset 0 0 20px rgba(255, 107, 0, 0.1);
        }

        .stat-item {
            color: #ffa500;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(255, 165, 0, 0.6);
        }

        .stat-item strong {
            color: #ffff00;
            font-size: 20px;
            font-weight: 900;
            display: block;
            margin-top: 5px;
            text-shadow: 0 0 10px rgba(255, 255, 0, 0.8), 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* Ki Status Tag */
        .status-tag {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 18px;
            border: 2px solid;
        }

        .tag-available {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
            border-color: #00ff00;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.4), inset 0 0 10px rgba(0, 255, 0, 0.2);
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
        }

        .tag-completed {
            background: rgba(0, 128, 255, 0.2);
            color: #0080ff;
            border-color: #0080ff;
            box-shadow: 0 0 20px rgba(0, 128, 255, 0.4), inset 0 0 10px rgba(0, 128, 255, 0.2);
            text-shadow: 0 0 10px rgba(0, 128, 255, 0.8);
        }

        .tag-locked {
            background: rgba(128, 128, 128, 0.2);
            color: #888;
            border-color: #666;
            box-shadow: 0 0 10px rgba(128, 128, 128, 0.3);
            text-shadow: 0 0 5px rgba(128, 128, 128, 0.6);
        }

        /* Senzu Bean Rewards */
        .rewards {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 107, 0, 0.1);
            border-radius: 12px;
            border: 2px solid rgba(255, 107, 0, 0.4);
            box-shadow: 0 0 20px rgba(255, 107, 0, 0.2), inset 0 0 10px rgba(255, 107, 0, 0.1);
        }

        .reward-item {
            color: #ffa500;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            text-shadow: 0 0 10px rgba(255, 165, 0, 0.6);
        }

        .reward-item i {
            margin-right: 6px;
        }

        /* Kamehameha Button */
        .action-btn {
            width: 100%;
            padding: 16px;
            border: 2px solid;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .btn-attack {
            background: linear-gradient(135deg, #ff0000 0%, #ff4500 100%);
            border-color: #ffd700;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.6), 0 8px 25px rgba(0, 0, 0, 0.5);
            text-shadow: 0 0 10px rgba(255, 255, 0, 0.8), 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .btn-attack:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 50px rgba(255, 0, 0, 0.9), 0 12px 35px rgba(0, 0, 0, 0.6);
            animation: attackPulse 0.5s infinite;
        }

        @keyframes attackPulse {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.5); }
        }

        .btn-no-energy {
            background: linear-gradient(135deg, #ff8c00 0%, #ffa500 100%);
            border-color: #ffd700;
            box-shadow: 0 0 20px rgba(255, 140, 0, 0.5), 0 8px 25px rgba(0, 0, 0, 0.5);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.6), 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .btn-no-energy:hover {
            transform: translateY(-3px);
        }

        .btn-completed {
            background: linear-gradient(135deg, #0080ff 0%, #00bfff 100%);
            border-color: #87ceeb;
            cursor: default;
            opacity: 0.8;
            box-shadow: 0 0 20px rgba(0, 128, 255, 0.4), 0 8px 25px rgba(0, 0, 0, 0.5);
            text-shadow: 0 0 10px rgba(135, 206, 235, 0.6), 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .btn-locked {
            background: linear-gradient(135deg, #666 0%, #888 100%);
            border-color: #999;
            cursor: not-allowed;
            opacity: 0.6;
            box-shadow: 0 0 10px rgba(128, 128, 128, 0.3), 0 8px 25px rgba(0, 0, 0, 0.5);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.92);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: #fff;
            border: 10px solid #212121;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 0 #424242, 0 20px 0 #212121, 0 30px 70px rgba(0, 0, 0, 0.9);
        }

        .modal-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .modal-header i {
            font-size: 64px;
            color: #f44336;
            filter: drop-shadow(6px 6px 0 #b71c1c);
        }

        .modal-header h2 {
            color: #212121;
            margin-top: 20px;
            font-size: 32px;
            text-transform: uppercase;
            text-shadow: 4px 4px 0 #f44336, 8px 8px 0 #b71c1c;
        }

        .modal-body {
            color: #424242;
            text-align: center;
            margin-bottom: 35px;
            font-size: 16px;
            line-height: 1.8;
            font-weight: 600;
        }

        .modal-body p {
            margin: 18px 0;
        }

        .close-modal-btn {
            width: 100%;
            padding: 18px;
            background: repeating-linear-gradient(0deg, #2196f3 0px, #2196f3 4px, #1e88e5 4px, #1e88e5 8px);
            color: #fff;
            border: 8px solid #fff;
            font-size: 20px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.1s steps(2);
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: inset 0 -8px 0 #0d47a1, 0 0 0 4px #1565c0, 0 10px 0 #1565c0, 0 20px 0 #0d47a1;
            text-shadow: 3px 3px 0 #0d47a1;
        }

        .close-modal-btn:hover {
            transform: translateY(-6px);
            box-shadow: inset 0 -8px 0 #0d47a1, 0 0 0 4px #1565c0, 0 16px 0 #1565c0, 0 26px 0 #0d47a1;
        }

        .close-modal-btn:active {
            transform: translateY(6px);
            box-shadow: inset 0 -8px 0 #0d47a1, 0 0 0 4px #1565c0, 0 4px 0 #1565c0, 0 14px 0 #0d47a1;
        }

        @media (max-width: 900px) {
            .warriors-grid {
                grid-template-columns: 1fr;
                max-width: 550px;
            }
        }

        @media (max-width: 768px) {
            .event-header h1 {
                font-size: 36px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="verao-explorando-banner"></div>
        
        <div class="event-header">
            <h1>🌊 VERÃO LITORANDO 🌊</h1>
            <p class="event-subtitle">CRIATURAS HOSTIS HABITAM A ILHA!</p>
            
            <div class="progress-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>DESAFIOS</h3>
                        <div class="stat-value"><?php echo $stats['desbloqueados']; ?>/8</div>
                    </div>
                    <div class="stat-card">
                        <h3>VITÓRIAS</h3>
                        <div class="stat-value"><?php echo $stats['vitorias']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>DERROTAS</h3>
                        <div class="stat-value"><?php echo $stats['derrotas']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>PONTOS</h3>
                        <div class="stat-value"><?php echo $energia_restante; ?></div>
                    </div>
                </div>
                
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo max($stats['progresso'], 100); ?>%">
                        <?php echo round($stats['progresso']); ?>% COMPLETO
                    </div>
                </div>
            </div>
        </div>

        <div class="page-title">
            <h2>🔥 EXPLORE 🔥</h2>
            <p>DESBLOQUEIE AS CRIATURAS EM SEQUÊNCIA!</p>
        </div>

        <ul class="warriors-grid">
            <?php $veraoexplorando->getListGrid(); ?>
        </ul>
    </div>

    <div id="energyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-battery-empty"></i>
                <h2>ENERGIA INSUFICIENTE!</h2>
            </div>
            <div class="modal-body">
                <p>Você precisa de <strong>10 pontos</strong> de energia para batalhar.</p>
                <p>Sua energia atual: <strong><?php echo $energia_restante; ?> pontos</strong></p>
                <p>⏰ Descanse e aguarde a energia recuperar!</p>
            </div>
            <button class="close-modal-btn" onclick="closeModal()">ENTENDI</button>
        </div>
    </div>

    <script>
        function showEnergyModal() {
            document.getElementById('energyModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('energyModal').style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('energyModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
