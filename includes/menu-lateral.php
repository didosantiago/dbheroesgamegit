<?php
    // Load selected character data ONLY if character is selected
    if(isset($_SESSION["PERSONAGEMID"]) && $_SESSION["PERSONAGEMID"] > 0){
        $personagem->getGuerreiro($_SESSION["PERSONAGEMID"]);
        
        // Calculate HP Max based on level
        if($personagem->nivel > 1){
            $nivel_hp = 150 + ((intval($personagem->nivel) - 1) * 50);
        } else {
            $nivel_hp = 150;
        }
        
        // Calculate current values
        $ki_atual = intval($personagem->mana) - intval($personagem->ki_usado);
        $energia_atual = intval($personagem->energia) - intval($personagem->energia_usada);
        
        // Calculate percentages (avoid division by zero)
        $porcentagem_hp = ($nivel_hp > 0) ? ($personagem->hp / $nivel_hp) * 100 : 0;
        $porcentagem_ki = ($personagem->mana > 0) ? ($ki_atual / $personagem->mana) * 100 : 0;
        $porcentagem_energia = ($personagem->energia > 0) ? ($energia_atual / $personagem->energia) * 100 : 0;
        
        // Get next level experience
        $sql_next_level = "SELECT * FROM level WHERE level = ".($personagem->nivel + 1);
        $stmt_next = DB::prepare($sql_next_level);
        $stmt_next->execute();
        
        if($stmt_next->rowCount() > 0){
            $next_level_data = $stmt_next->fetch();
            $exp_faltante = $next_level_data->exp - $personagem->exp;
            $porcentagem_exp = ($next_level_data->exp > 0) ? ($personagem->exp / $next_level_data->exp) * 100 : 0;
        } else {
            // Max level reached
            $next_level_data = (object)array('exp' => $personagem->exp);
            $exp_faltante = 0;
            $porcentagem_exp = 100;
        }
        
        // Clean foto path - remove 'cards/' if present
        $foto = str_replace('cards/', '', $personagem->foto);
        
        $hasCharacter = true;
    } else {
        $hasCharacter = false;
    }
?>

<div class="menu-lateral desktop">
    <?php if($hasCharacter){ ?>
        <div class="personagem-logado">
            <div class="foto-personagem">
                <div class="alter-foto">
                    <a href="<?php echo BASE; ?>minhas-fotos">
                        <i class="fas fa-camera"></i>
                        <span>Alterar</span>
                    </a>
                </div>
                <a href="<?php echo BASE; ?>publico">
                    <img src="<?php echo BASE.'assets/cards/'.$foto; ?>" alt="<?php echo $personagem->nome; ?>" />
                </a>
            </div>
            
            <div class="exp">
                <div class="nivel-atual">
                    <h4>LEVEL</h4>
                    <span><?php echo $personagem->nivel; ?></span>
                </div>
                
                <div class="label-exp">
                    Próximo Level 
                    <span class="txt">
                        <?php echo ($personagem->nivel + 1); ?>
                    </span>
                </div>
                
                <div class="meter animate roxo">
                    <div class="exp-faltante">Falta <strong><?php echo number_format($exp_faltante, 0, ',', '.'); ?></strong> de exp para avançar</div>
                    <em><?php echo number_format($personagem->exp, 0, ',', '.'); ?> / <?php echo number_format($next_level_data->exp, 0, ',', '.'); ?></em>
                    <span style="width: <?php echo $porcentagem_exp; ?>%"><span></span></span>
                </div>
                
                <span class="desc-exp">Experiência</span>
            </div>
            
            <div class="graduacao-patente">
                <h4>Graduação</h4>
                <?php 
                    if(method_exists($personagem, 'getGraduacao')){
                        $personagem->getGraduacao($personagem->nivel);
                    }
                ?>
                <span><?php echo isset($personagem->graduacao) ? $personagem->graduacao : 'Graduação 1'; ?></span>
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
                <div class="atributos hp at-meter">
                    <strong>HP </strong>
                    <div class="meter animate red">
                        <em><?php echo $personagem->hp; ?> / <strong><?php echo $nivel_hp; ?></strong></em>
                        <span style="width: <?php echo $porcentagem_hp; ?>%"><span></span></span>
                    </div>
                </div>
                <div class="atributos mana at-meter">
                    <strong>KI </strong>
                    <div class="meter animate blue">
                        <em><?php echo $ki_atual; ?> / <strong><?php echo $personagem->mana; ?></strong></em>
                        <span style="width: <?php echo $porcentagem_ki; ?>%"><span></span></span>
                    </div>
                </div>
                <div class="atributos energia at-meter">
                    <strong>Energia </strong>
                    <div class="meter animate">
                        <em><?php echo $energia_atual; ?> / <strong><?php echo $personagem->energia; ?></strong></em>
                        <span style="width: <?php echo $porcentagem_energia; ?>%"><span></span></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- No character selected - show placeholder -->
        <div class="personagem-logado">
            <div class="foto-personagem">
                <a href="<?php echo BASE; ?>meus-personagens">
                    <img src="<?php echo BASE.'assets/guerreiro_blank.jpg'; ?>" alt="Selecione seu Guerreiro" />
                </a>
            </div>
            
            <div class="exp">
                <div class="nivel-atual">
                    <h4>LEVEL</h4>
                    <span>0</span>
                </div>
                
                <div class="label-exp">
                    Próximo Level 
                    <span class="txt">1</span>
                </div>
                
                <div class="meter animate roxo">
                    <div class="exp-faltante">Selecione um guerreiro</div>
                    <em>0 / 0</em>
                    <span style="width: 0%"><span></span></span>
                </div>
                
                <span class="desc-exp">Experiência</span>
            </div>
            
            <div class="info">
                <h3>Nenhum guerreiro selecionado</h3>
                <p><a href="<?php echo BASE; ?>meus-personagens">Clique aqui para selecionar</a></p>
            </div>
        </div>
    <?php } ?>

    <ul class="desktop">
        <h2>Utilitários</h2>
        <li>
            <a href="<?php echo BASE; ?>experiencia"><i class="fas fa-upload"></i> Tabela de Experiência</a>
        </li>
        <li>
            <a href="<?php echo BASE; ?>graduacoes"><i class="fas fa-level-up-alt"></i> Tabela de Graduações</a>
        </li>
        <li>
            <a href="<?php echo BASE; ?>loja/creditos"><i class="fas fa-coins"></i> Adquirir Coins</a>
        </li>
        
        <h2>Parceiros</h2>
        
        <li>
            <a href="https://www.youtube.com/user/didogameplay" target="_blank"><i class="fab fa-youtube"></i> Dido Gameplay</a>
        </li>
        <li>
            <a href="https://www.facebook.com/dbheroesgame" target="_blank"><i class="fab fa-facebook"></i> Facebook DBHeroes</a>
        </li>
        <li>
            <a href="https://www.instagram.com/dbheroesgame" target="_blank"><i class="fab fa-instagram"></i> Instagram DBHeroes</a>
        </li>
        <li>
            <a href="https://www.youtube.com/channel/UC88PqK6ByP47PrcyW5oCYrQ" target="_blank"><i class="fab fa-youtube"></i> Canal no Youtube</a>
        </li>
        <li>
            <a href="https://discord.gg/zbSWtcs" target="_blank"><i class="fab fa-discord"></i> Canal no Discord</a>
        </li>
    </ul>
</div>