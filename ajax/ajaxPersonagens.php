<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";   
    require_once "../core/Core.php";
    require_once "../core/Treino.php";
    require_once "../core/Personagens.php";
    require_once "../core/Usuarios.php";
    
    $core = new Core();
    $personagem = new Personagens();
    $treino = new Treino();
    $user = new Usuarios();
    
    $id = addslashes($_POST['id']);
    
    $treino->recoveryEnergia($id, $user->vip);
    $treino->recoveryKI($id, $user->vip);
    $treino->recoveryHP($id, $user->vip);
    
    $personagem->getGuerreiro($id);
    
    // FIX: Remove 'cards/' from foto if it exists
    $foto = str_replace('cards/', '', $personagem->foto);
    
    // Calculate max HP based on level
    if($personagem->nivel > 1){
        $nivel_hp = 150 + ((intval($personagem->nivel) - 1) * 50);
    } else {
        $nivel_hp = 150;
    }
    
    // Set max values
    $personagem->hpMax = $nivel_hp;
    $personagem->kiMax = $personagem->mana;
    $personagem->energiaMax = $personagem->energia;
    
    // Calculate current values
    $ki_atual = intval($personagem->mana) - intval($personagem->ki_usado);
    $energia_atual = intval($personagem->energia) - intval($personagem->energia_usada);
?>

<div class="foto-personagem">
    <img src="<?php echo BASE; ?>assets/cards/<?php echo $foto; ?>" alt="<?php echo $personagem->nome; ?>" />
</div>
<div class="info">
    <h3><?php echo $personagem->nome; ?></h3>
    <div class="atributos raca">
        <strong>Raça: </strong>
        <?php echo $personagem->raca; ?>
    </div>
    <div class="atributos planeta">
        <strong>Planeta: </strong>
        <?php echo $personagem->planeta; ?>
    </div>
    <div class="atributos graduacao">
        <strong>Graduação: </strong>
        <?php echo $personagem->graduacao; ?>
    </div>
    <div class="atributos nivel">
        <strong>Nível: </strong>
        <?php echo $personagem->nivel; ?>
    </div>
    <div class="atributos hp at-meter">
        <strong>HP </strong>
        <div class="meter animate red">
            <em><?php echo $personagem->hp; ?> / <strong><?php echo $personagem->hpMax; ?></strong></em>
            <span style="width: <?php echo ($personagem->hp / $personagem->hpMax) * 100; ?>%"><span></span></span>
        </div>
    </div>
    <div class="atributos mana at-meter">
        <strong>KI </strong>
        <div class="meter animate blue">
            <em><?php echo $ki_atual; ?> / <strong><?php echo $personagem->kiMax; ?></strong></em>
            <span style="width: <?php echo ($ki_atual / $personagem->kiMax) * 100; ?>%"><span></span></span>
        </div>
    </div>
    <div class="atributos energia at-meter">
        <strong>Energia </strong>
        <div class="meter animate">
            <em><?php echo $energia_atual; ?> / <strong><?php echo $personagem->energiaMax; ?></strong></em>
            <span style="width: <?php echo ($energia_atual / $personagem->energiaMax) * 100; ?>%"><span></span></span>
        </div>
    </div>
    
    <button class="bts-form bt-jogar fas fa-play" dataid="<?php echo $personagem->id; ?>"> Jogar</button>
</div>