<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Invasao.php";

header('Content-Type: application/json');

try {
    if(!isset($_POST['idPersonagem']) || !isset($_POST['idInvasor']) || !isset($_POST['idBatalha']) || !isset($_POST['idGolpe'])){
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }

    $idPersonagem = intval($_POST['idPersonagem']);
    $idInvasor = intval($_POST['idInvasor']);
    $idBatalha = intval($_POST['idBatalha']);
    $idGolpe = intval($_POST['idGolpe']);

    $invasao = new Invasao();
    $core = new Core();

    $personagem = $core->getDados('usuarios_personagens', "WHERE id = $idPersonagem");
    if(!$personagem){
        echo json_encode(['success' => false, 'error' => 'Character not found']);
        exit;
    }

    $dadosInvasor = $core->getDados('adm_invasao_boss', "WHERE id = $idInvasor");
    if(!$dadosInvasor){
        echo json_encode(['success' => false, 'error' => 'Boss not found']);
        exit;
    }

    $ataque = $core->getDados('ataques', "WHERE id = $idGolpe");
    if(!$ataque){
        echo json_encode(['success' => false, 'error' => 'Attack not found']);
        exit;
    }

    if($personagem->hp < 10){
        echo json_encode(['success' => false, 'error' => 'Not enough HP']);
        exit;
    }

    if(($personagem->mana - $personagem->ki_usado) < $ataque->ki){
        echo json_encode(['success' => false, 'error' => 'Not enough KI']);
        exit;
    }

    $dano_adversario = intval($ataque->dano) + intval($personagem->nivel * 2);
    
    $ataque_boss = $core->getDados('ataques', "WHERE graduacao <= " . intval($dadosInvasor->graduacao) . " ORDER BY RAND() LIMIT 1");
    if(!$ataque_boss){
        $ataque_boss = $core->getDados('ataques', "WHERE id = 1");
    }
    $dano_personagem = intval($ataque_boss->dano) + 50;

    $novo_hp = intval($personagem->hp) - $dano_personagem;
    if($novo_hp < 0) $novo_hp = 0;
    
    $novo_ki_usado = intval($personagem->ki_usado) + intval($ataque->ki);

    $campos_personagem = array(
        'hp' => $novo_hp,
        'ki_usado' => $novo_ki_usado
    );
    $core->update('usuarios_personagens', $campos_personagem, "id = $idPersonagem");

    $novo_hp_boss_usado = intval($dadosInvasor->hp_usado) + $dano_adversario;

    $campos_boss = array(
        'hp_usado' => $novo_hp_boss_usado
    );
    $core->update('adm_invasao_boss', $campos_boss, "id = $idInvasor");

    $round = 1;
    if($core->isExists('adm_invasao_ataques', "WHERE idBatalha = $idBatalha")){
        $last_attack = $core->getDados('adm_invasao_ataques', "WHERE idBatalha = $idBatalha ORDER BY round DESC LIMIT 1");
        if($last_attack){
            $round = intval($last_attack->round) + 1;
        }
    }

    $campos_ataque = array(
        'idBatalha' => $idBatalha,
        'idGolpe' => $idGolpe,
        'idGolpeBoss' => $ataque_boss->id,
        'dano_adversario' => $dano_adversario,
        'dano_personagem' => $dano_personagem,
        'round' => $round,
        'time_ataque' => time() + 10
    );
    
    $insert_result = $core->insert('adm_invasao_ataques', $campos_ataque);

    $campos_batalha = array(
        'dano_total' => $novo_hp_boss_usado
    );
    $core->update('adm_invasao_batalhas', $campos_batalha, "id = $idBatalha");

    $hp_boss_restante = intval($dadosInvasor->hp_total) - $novo_hp_boss_usado;
    if($hp_boss_restante < 0) $hp_boss_restante = 0;

    if($hp_boss_restante <= 0){
        $invasao->premiaVencedor($_SESSION['USERID'], $idPersonagem, $idInvasor);
        
        $campos_fim = array(
            'concluida' => 1,
            'finalizado' => 1
        );
        $core->update('adm_invasao_batalhas', $campos_fim, "id = $idBatalha");
    }

    if($novo_hp < 10){
        $campos_fim = array(
            'finalizado' => 1
        );
        $core->update('adm_invasao_batalhas', $campos_fim, "id = $idBatalha");
        
        $campos_timer = array(
            'time_invasao' => time() + 600
        );
        $core->update('usuarios_personagens', $campos_timer, "id = $idPersonagem");
    }

    echo json_encode([
        'success' => true,
        'dano_causado' => $dano_adversario,
        'dano_recebido' => $dano_personagem,
        'hp_restante' => $novo_hp,
        'hp_boss_restante' => $hp_boss_restante,
        'round' => $round
    ]);

} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
}
?>
