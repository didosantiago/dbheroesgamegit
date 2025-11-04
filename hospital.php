<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
    
    $idPersonagem = $_SESSION['PERSONAGEMID'];
    
    $personagem_dbz = $core->getDados('personagens', 'WHERE id = '.$personagem->persona);
    
    $hp_level = 50;
    $preco_hp = 1;
    $preco_ki = 1;
    
    $hp = $personagem->hp;
    $ki = $personagem->mana;
    $ki_usado = $personagem->ki_usado;
    $level = $personagem->nivel;
    
    $valor_hp = ($level * $hp_level) + 100;
    
    $diferenca_hp = $valor_hp - $hp;
    $diferenca_ki = $ki_usado;
    
    $gold_hp = $diferenca_hp * $preco_hp;
    $gold_ki = $diferenca_ki * $preco_ki;
    $gold_ki_hp = $gold_hp + $gold_ki;
    
    if($user->vip == 1){
        $desconto_hp_vip = intval($gold_hp) * (50 / 100);
        $desconto_ki_vip = intval($gold_ki) * (50 / 100);
        $desconto_hp_ki_vip = intval($gold_ki_hp) * (50 / 100);
    } else {
        $desconto_hp_vip = 0;
        $desconto_ki_vip = 0;
        $desconto_hp_ki_vip = 0;
    }
    
    if(isset($_POST['recupera_vida'])){
        if($personagem->getSaldo(intval($gold_hp - $desconto_hp_vip), $idPersonagem)){
            if($hp >= $valor_hp){
                echo "<script type='text/javascript'>
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Sua vida já esta completa!'
                        })
                      </script>";
            } else {
                $rec_hp_gold = intval($gold_hp - $desconto_hp_vip);
                $campos = array(
                    'hp' => $valor_hp,
                    'gold' => $personagem->gold - $rec_hp_gold
                );

                $where = 'id = "'.$idPersonagem.'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    $core->msg('sucesso', 'Parabéns, sua vida foi restaurada.');
                    header('Location: '.BASE.'hospital/');
                } else {
                    $core->msg('error', 'Erro na Recuperação.');
                } 
            }
        } else {
            $core->msg('error', 'Golds Insuficientes.');
        } 
    }
    
    if(isset($_POST['recupera_ki'])){
        if($personagem->getSaldo(intval($gold_ki - $desconto_ki_vip), $idPersonagem)){
            if($ki_usado == 0){
                echo "<script type='text/javascript'>
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Seu KI já esta completo!'
                        })
                      </script>";
            } else {
                $rec_ki_gold = intval($gold_ki - $desconto_ki_vip);
                $campos = array(
                    'mana' => $ki,
                    'ki_usado' => 0,
                    'gold' => $personagem->gold - $rec_ki_gold
                );

                $where = 'id = "'.$idPersonagem.'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    $core->msg('sucesso', 'Parabéns, seu KI foi restaurado.');
                    header('Location: '.BASE.'hospital/');
                } else {
                    $core->msg('error', 'Erro na Recuperação.');
                } 
            }
        } else {
            $core->msg('error', 'Golds Insuficientes.');
        }
    }
    
    if(isset($_POST['recupera_vida_ki'])){
        if($personagem->getSaldo(intval($gold_ki_hp - $desconto_hp_ki_vip), $idPersonagem)){
            if($ki_usado == 0 && $hp >= $valor_hp){
                echo "<script type='text/javascript'>
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Sua Vida ou KI já estão completos!'
                        })
                      </script>";
            } else {
                $rec_hp_ki_gold = intval($gold_ki_hp - $desconto_hp_ki_vip);
                $campos = array(
                    'hp' => $valor_hp,
                    'mana' => $ki,
                    'ki_usado' => 0,
                    'gold' => $personagem->gold - $rec_hp_ki_gold
                );

                $where = 'id = "'.$idPersonagem.'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    $core->msg('sucesso', 'Parabéns, sua Vida e KI foram restaurados.');
                    header('Location: '.BASE.'hospital/');
                } else {
                    $core->msg('error', 'Erro na Recuperação.');
                } 
            }
        } else {
            $core->msg('error', 'Golds Insuficientes.');
        }
    }
?>

<h2 class="title"><i class="fas fa-plus-square"></i> Hospital</h2>

<ul class="ficha">
    <h3>Ficha do Paciente</h3>
    <div class="foto-principal">
        <?php $ft = str_replace('cards/', '', $personagem->foto); ?>
        <img src="<?php echo BASE.'assets/cards/'.$ft; ?>" class="ft-guerreiro" alt="<?php echo $personagem->nome; ?>" />
    </div>
    <li>
        <span>Nome:</span>
        <strong><?php echo $personagem->nome; ?></strong>
    </li>
    <li>
        <span>Personagem:</span>
        <strong><?php echo $personagem_dbz->nome; ?></strong>
    </li>
    <li class="alter">
        <span>Nível:</span>
        <strong><?php echo $personagem->nivel; ?></strong>
    </li>
    <li>
        <span>Planeta:</span>
        <strong><?php echo $personagem->planeta; ?></strong>
    </li>
    <li class="atributos hp at-meter">
        <strong>HP </strong>
        <div class="meter animate red">
            <em><?php echo $personagem->hp; ?></em>
            <span style="width: 99%"><span></span></span>
        </div>
    </li>
    <li class="atributos mana at-meter">
        <strong>KI </strong>
        <div class="meter animate blue">
            <em><?php echo $personagem->mana - $personagem->ki_usado; ?></em>
            <?php 
                $porcentagem_ki = $treino->getPorcentagemKI($personagem->mana, $personagem->ki_usado);
            ?>
            <span style="width: <?php echo $porcentagem_ki; ?>%"><span></span></span>
        </div>
    </li>
    <li class="atributos energia at-meter">
        <strong>Energia </strong>
        <div class="meter animate">
            <em><?php echo $personagem->energia; ?></em>
            <span style="width: 99%"><span></span></span>
        </div>
    </li>
</ul>

<ul class="tratamentos">
    <h3>Tratamentos Disponíveis</h3>
    <li class="vida">
        <i class="fas fa-procedures"></i>
        <div class="info">
            <p>Recuperar todos seus pontos de vida.</p>
            <span><?php echo intval($gold_hp - $desconto_hp_vip); ?> Golds</span>
        </div>
        <form id="recuperaVida" method="post">
            <input type="submit" class="bts-form" name="recupera_vida" value="Iniciar" />
        </form>
    </li>
    <li class="ki">
        <i class="fas fa-battery-full"></i>
        <div class="info">
            <p>Recuperar todo seu KI.</p>
            <span><?php echo intval($gold_ki - $desconto_ki_vip); ?> Golds</span>
        </div>
        <form id="recuperaKI" method="post">
            <input type="submit" class="bts-form" name="recupera_ki" value="Iniciar" />
        </form>
    </li>
    <li class="vida_energia">
        <i class="fas fa-charging-station"></i>
        <div class="info">
            <p>Recuperar todos seus pontos de vida e ki.</p>
            <span><?php echo intval($gold_ki_hp - $desconto_hp_ki_vip); ?> Golds</span>
        </div>
        <form id="recuperaVidaKi" method="post">
            <input type="submit" class="bts-form" name="recupera_vida_ki" value="Iniciar" />
        </form>
    </li>
    
</ul>