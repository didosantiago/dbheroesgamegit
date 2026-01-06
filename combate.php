<?php
// ===== PVP COMBAT PAGE - FIXED LAYOUT =====
// Based on working npc.php structure

// Check if user is logged in
if(!isset($_SESSION['PERSONAGEMID'])){
    header('Location: '.BASE.'portal');
    exit;
}

// Initialize variables
$habilitado = 1;
$idPersonagem = $_SESSION['PERSONAGEMID'];
$parametro_1 = Url::getURL(1);

// Load classes
$core = new Core();
$user = new Usuarios(); // Don't call getUsuario() - just instantiate
$personagem = new Personagens();
$batalha = new Batalha();
$equipes = new Equipes();

// Load character data
$personagem->getGuerreiro($idPersonagem);

// Initialize opponent as null
$oponente = null;

// ===== LOAD OPPONENT DATA =====
if(($parametro_1 != null) && ($parametro_1 != 'ajax') && is_numeric($parametro_1)){
    $oponente = new Personagens();
    $oponente->getGuerreiro($parametro_1);

    if(!$oponente->id || !$oponente->idUsuario){
        $core->msg('error', 'Oponente não encontrado.');
        header('Location: '.BASE.'ranking');
        exit;
    }
} else {
    $core->msg('error', 'ID do oponente inválido.');
    header('Location: '.BASE.'ranking');
    exit;
}

// ===== SESSION INITIALIZATION =====
if(isset($_SESSION['pvp_finalizado'])){
    $pvp_finalizado = $_SESSION['pvp_finalizado'];
} else {
    $pvp_finalizado = 0;
}

if(isset($_SESSION['pvp_desafiador'])){
    $pvp_desafiador = $_SESSION['pvp_desafiador'];
} else {
    $pvp_desafiador = 0;
}

if(isset($_SESSION['pvp_life'])){
    $pvp_life = $_SESSION['pvp_life'];
} else {
    $pvp_life = 0;
}

if(isset($_SESSION['pvp_life_oponente'])){
    $pvp_life_oponente = $_SESSION['pvp_life_oponente'];
} else {
    $pvp_life_oponente = 0;
}

if(isset($_SESSION['pvp_ki_oponente'])){
    $pvp_ki_oponente = $_SESSION['pvp_ki_oponente'];
} else {
    $pvp_ki_oponente = 0;
}

// ===== VALIDATION CHECKS =====
if(!isset($_SESSION['verao_npc'])){

    // Level check
    if($personagem->nivel < 10){
        $habilitado = 0;
        $core->msg('error', 'Você não está habilitado para o PVP, é necessário ter level 10 no mínimo.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    if($oponente->nivel < 10){
        $habilitado = 0;
        $core->msg('error', 'Adversário não habilitado para o PVP, é necessário ter level 10 no mínimo.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    // HP check
    if(!isset($_SESSION['pvp']) && $personagem->hp <= 0){
        $habilitado = 0;
        $core->msg('error', 'Seu HP é insuficiente para a luta.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    if(!isset($_SESSION['pvp']) && $oponente->hp <= 0){
        $habilitado = 0;
        $core->msg('error', 'O HP de seu adversário é insuficiente para a luta.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    // Gold check
    if($personagem->gold < 20){
        $habilitado = 0;
        $core->msg('error', 'Gold insuficiente para a Batalha, realize caçadas ou missões para conseguir o gold necessário!');
        header('Location: '.BASE.'ranking');
        exit;
    }

    if($oponente->gold < 20){
        $habilitado = 0;
        $core->msg('error', 'Seu adversário não tem Gold suficiente para a Batalha.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    // Team check
    if($equipes->verificaMembrosEquipe($idPersonagem, $oponente->id)){
        $habilitado = 0;
        $core->msg('error', 'Você não pode atacar membros da sua Equipe.');
        header('Location: '.BASE.'ranking');
        exit;
    }

    // Same account check
    if($oponente->idUsuario == $user->id){
        $core->msg('error', 'ATENÇÃO! Você não pode atacar personagens da mesma conta.');
        header('Location: '.BASE.'pvp');
        exit;
    }

    // Self-attack check
    if($parametro_1 == $idPersonagem){
        $habilitado = 0;
        $core->msg('error', 'Você não pode se atacar.');
        header('Location: '.BASE.'pvp');
        exit;
    }

    // Daily attack limit check
    if($batalha->playerAtacadoDAY($idPersonagem, $parametro_1)){
        $habilitado = 0;
        $core->msg('error', 'Você já atacou este guerreiro hoje, aguarde até amanhã para um novo ataque.');
        header('Location: '.BASE.'pvp');
        exit;
    }

    // Recent attack check
    if($batalha->getAtacouRecente($idPersonagem, $parametro_1)){
        if(!isset($_SESSION['pvp'])){
            $habilitado = 0;
            $core->msg('error', 'Você atacou um adversário recentemente e não poderá atacar durante 10 minutos.');
            header('Location: '.BASE.'pvp');
            exit;
        }
    }
}

// ===== BATTLE MANAGEMENT =====
if($batalha->pvpRun($idPersonagem, $parametro_1)){
    $sql = "SELECT * FROM pvp WHERE id = ".$_SESSION['pvp_id'];
    $stmt = DB::prepare($sql);
    $stmt->execute();
    $pvp_info = $stmt->fetch();

    // Resume paused battle
    if($pvp_info->pausado == 1){
        if($pvp_info->atacado == 1){
            $campos = array(
                'pausado' => 0,
                'time_inicial' => time(),
                'time_final' => time() + 30
            );
            $where = 'id="'.$_SESSION['pvp_id'].'"';
            $core->update('pvp', $campos, $where);
        }

        if($pvp_info->atacou == 1){
            $campos = array('pausado' => 0);
            $where = 'id="'.$_SESSION['pvp_id'].'"';
            $core->update('pvp', $campos, $where);
            $batalha->atack(4, $parametro_1, $idPersonagem, 0);
        }
    }

    $habilitado = 0;
} else {
    // Clear battle sessions if not active
    unset($_SESSION['pvp'], $_SESSION['pvp_id'], $_SESSION['pvp_vitoria'], 
          $_SESSION['pvp_derrota'], $_SESSION['atacado'], $_SESSION['pvp_desafiador'],
          $_SESSION['pvp_finalizado'], $_SESSION['pvp_life'], $_SESSION['pvp_life_oponente'],
          $_SESSION['pvp_ki_oponente'], $_SESSION['pvp_final']);
}

// ===== CREATE NEW BATTLE =====
if(!isset($_SESSION['pvp']) && $habilitado == 1){
    $batalha->saveBatalha($idPersonagem, $parametro_1);

    $comeca = rand(1, 2);
    if($comeca == 1){
        $_SESSION['pvp_desafiador'] = 1;
    } else {
        $_SESSION['pvp_desafiador'] = 0;
    }

    if($batalha->getGuerreiroAtacado($_SESSION['pvp_id'])){
        $_SESSION['pvp_desafiador'] = 1;
    }

    if($_SESSION['pvp_desafiador'] == 0){
        $batalha->atack(4, $parametro_1, $idPersonagem, 0);
        $_SESSION['pvp_desafiador'] = 1;
    }
}

// ===== HANDLE ATTACK POST =====
if(isset($_POST['atacar'])){
    if(addslashes($_POST['estado']) == 1){
        if(!isset($_SESSION['pvp_vitoria']) && !isset($_SESSION['pvp_derrota'])){
            if(isset($_SESSION['pvp_final'])){
                $pvp_final = $_SESSION['pvp_final'];
            } else {
                $pvp_final = 0;
            }

            $batalha->atack(addslashes($_POST['idAtack']), $idPersonagem, $parametro_1, 1, $pvp_final);
            $_SESSION['pvp_desafiador'] = 0;

            header('Location: '.BASE.'combate/'.$parametro_1);
            exit;
        }
    }
}

// ===== CALCULATE HP AND KI =====
if($batalha->pvpRun($idPersonagem, $parametro_1) || isset($_SESSION['pvp_vitoria']) || isset($_SESSION['pvp_derrota'])){
    $pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$idPersonagem.' AND idDesafiado = '.$parametro_1.' ORDER BY id DESC LIMIT 1');

    if($pvp){
        $lifes = $batalha->getLifeRestante($pvp->id);
        $kis = $batalha->getKiRestante($pvp->id);

        $_SESSION['pvp_life'] = $personagem->hp - $lifes->dano_atacante;

        // VIP HP reduction for opponent
        if($user->vip == 1){
            $porcentagemVip = (40 / 100) * intval($oponente->hp);
            $_SESSION['pvp_life_oponente'] = $oponente->hp - $porcentagemVip - $lifes->dano_atacado;
        } else {
            $porcentagemFree = (20 / 100) * intval($oponente->hp);
            $_SESSION['pvp_life_oponente'] = $oponente->hp - $porcentagemFree - $lifes->dano_atacado;
        }

        $_SESSION['pvp_ki_oponente'] = $kis->ki_pvp;

        // Ensure HP never goes below 0
        if($_SESSION['pvp_life'] < 0) $_SESSION['pvp_life'] = 0;
        if($_SESSION['pvp_life_oponente'] < 0) $_SESSION['pvp_life_oponente'] = 0;
        if($_SESSION['pvp_ki_oponente'] < 0) $_SESSION['pvp_ki_oponente'] = 0;

        // Determine winner
        if($_SESSION['pvp_life'] > 0 && $_SESSION['pvp_life_oponente'] <= 0){
            $_SESSION['pvp_vitoria'] = true;
            $_SESSION['pvp_finalizado'] = 1;
        } else if($_SESSION['pvp_life'] <= 0 && $_SESSION['pvp_life_oponente'] > 0){
            $_SESSION['pvp_derrota'] = true;
            $_SESSION['pvp_finalizado'] = 1;
        } else if($_SESSION['pvp_life'] <= 0 && $_SESSION['pvp_life_oponente'] <= 0){
            if($_SESSION['pvp_life'] > $_SESSION['pvp_life_oponente']){
                $_SESSION['pvp_vitoria'] = true;
            } else {
                $_SESSION['pvp_derrota'] = true;
            }
            $_SESSION['pvp_finalizado'] = 1;
        }
    }
}

// ===== HANDLE BATTLE CONCLUSION =====
if(isset($_POST['concluir'])){
    $vitoria = isset($_SESSION['pvp_vitoria']) ? 1 : 0;

    if($vitoria == 1){
        // Player won
        $gold_recebido = intval((intval($oponente->gold) * 10) / 100);

        if($user->vip == 1){
            $extra_vip_gold = intval($gold_recebido) * (20 / 100);
        } else {
            $extra_vip_gold = 0;
        }

        $campos_usuario = array(
            'vitorias_pvp' => intval($personagem->vitorias_pvp) + 1,
            'gold' => intval($personagem->gold) + $gold_recebido + $extra_vip_gold,
            'gold_total' => intval($personagem->gold_total) + $gold_recebido + $extra_vip_gold
        );
        $core->update('usuarios_personagens', $campos_usuario, 'id = "'.$idPersonagem.'"');

        $campos_adversario = array(
            'derrotas_pvp' => intval($oponente->derrotas_pvp) + 1,
            'gold' => intval($oponente->gold) - $gold_recebido
        );
        $core->update('usuarios_personagens', $campos_adversario, 'id = "'.$oponente->id.'"');
    } else {
        // Player lost
        $gold_recebido = intval((intval($personagem->gold) * 10) / 100);

        $campos_adv = array(
            'vitorias_pvp' => intval($oponente->vitorias_pvp) + 1,
            'gold' => intval($oponente->gold) + $gold_recebido,
            'gold_total' => intval($oponente->gold_total) + $gold_recebido
        );
        $core->update('usuarios_personagens', $campos_adv, 'id = "'.$oponente->id.'"');

        $campos_usuario = array(
            'derrotas_pvp' => intval($personagem->derrotas_pvp) + 1,
            'gold' => intval($personagem->gold) - $gold_recebido
        );
        $core->update('usuarios_personagens', $campos_usuario, 'id = "'.$idPersonagem.'"');
    }

    // Update battle record
    $pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$idPersonagem.' AND idDesafiado = '.$parametro_1.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
    if($pvp){
        $campos_pvp = array(
            'vencedor' => $vitoria,
            'concluido' => 1
        );
        $core->update('pvp', $campos_pvp, 'id = "'.$pvp->id.'"');
    }

    // Update player HP
    if($_SESSION['pvp_finalizado'] == 1){
        $campos = array(
            'hp' => $_SESSION['pvp_life'],
            'time_hp' => time()
        );
        $core->update('usuarios_personagens', $campos, 'id = "'.$idPersonagem.'"');
    }

    // Clear sessions
    unset($_SESSION['atacado'], $_SESSION['pvp'], $_SESSION['pvp_id'], 
          $_SESSION['pvp_vitoria'], $_SESSION['pvp_derrota'], $_SESSION['pvp_desafiador'],
          $_SESSION['pvp_finalizado'], $_SESSION['pvp_life'], $_SESSION['pvp_life_oponente'],
          $_SESSION['pvp_final']);

    header('Location: '.BASE.'historico');
    exit;
}

?>

<!-- ===== VICTORY/DEFEAT POPUP ===== -->
<?php if(isset($_SESSION['pvp_vitoria'])): ?>
<div class="vitoria-modal">
    <div class="modal-content">
        <h2>Você venceu!</h2>
        <p><strong><?php echo $oponente->nome; ?></strong> desmaiou após o seu último ataque.</p>
        <p><strong>Gold:</strong> Você recebeu <?php echo intval((intval($oponente->gold) * 10) / 100); ?> golds de seu rival.</p>
        <form method="POST">
            <button type="submit" name="concluir" class="btn-confirmar">Continuar</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['pvp_derrota'])): ?>
<div class="derrota-modal">
    <div class="modal-content">
        <h2>Você Perdeu!</h2>
        <p><strong><?php echo $personagem->nome; ?></strong> desmaiou após o último ataque de <strong><?php echo $oponente->nome; ?></strong>.</p>
        <p><strong>Gold:</strong> Você perdeu <?php echo intval((intval($personagem->gold) * 10) / 100); ?> golds para seu rival.</p>
        <form method="POST">
            <button type="submit" name="concluir" class="btn-confirmar">Continuar</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ===== BATTLE ARENA (3-COLUMN LAYOUT) ===== -->
<div class="batalha">
    <?php 
    if($batalha->pvpRun($idPersonagem, $parametro_1) || isset($_SESSION['pvp_vitoria']) || isset($_SESSION['pvp_derrota'])){
        // Use printConfronto from Batalha class (needs to match NPC structure)
        $batalha->printConfronto($parametro_1, $idPersonagem, $_SESSION['pvp_life'], 
                                 $_SESSION['pvp_life_oponente'], $personagem->mana, 
                                 $user->vip, $_SESSION['pvp_ki_oponente']);
    }
    ?>
</div>

<script>
// Timer countdown
function startTimer(duration) {
    let timeLeft = duration;
    const timerElement = document.querySelector('.cronometro');

    if(timerElement){
        const countdown = setInterval(function(){
            timeLeft--;
            timerElement.textContent = timeLeft + 's';

            if(timeLeft <= 0){
                clearInterval(countdown);
                location.reload();
            }
        }, 1000);
    }
}

// Auto-start timer if exists
window.addEventListener('DOMContentLoaded', function(){
    const timer = document.querySelector('.cronometro');
    if(timer){
        startTimer(30);
    }
});
</script>
