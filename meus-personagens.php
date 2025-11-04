<div class="personagem-atual">
    <div class="foto-personagem">
        <img src="<?php echo BASE; ?>assets/guerreiro_blank.jpg" alt="Selecione seu Guerreiro" />
    </div>
    <div class="info">
        <h3>Não Selecionado</h3>
        <div class="atributos raca">
            <strong>Raça: </strong>
            Não Selecionado
        </div>
        <div class="atributos planeta">
            <strong>Planeta: </strong>
            Não Selecionado
        </div>
        <div class="atributos graduacao">
            <strong>Graduação: </strong>
            Não Selecionado
        </div>
        <div class="atributos nivel">
            <strong>Nível: </strong>
            Não Selecionado
        </div>
        <div class="atributos hp at-meter">
            <strong>HP </strong>
            <div class="meter animate red">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos mana at-meter">
            <strong>KI </strong>
            <div class="meter animate blue">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos energia at-meter">
            <strong>Energia </strong>
            <div class="meter animate">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
    </div>
</div>

<h2 class="title">Escolha um Guerreiro</h2>

<div class="lista-meus-personagens">
    <?php
    if (!is_object($personagem) || !method_exists($personagem, 'getMeusPersonagens')) {
    $personagem = new Personagens();
}
    $personagem->getMeusPersonagens($user->id); ?>
</div>