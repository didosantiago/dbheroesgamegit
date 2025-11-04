<?php switch($acao) {
    default: ?>
    <h2 class="title">Usuários Convidados <span class="total-coins">Total de Coin Adquiridos: <?php echo $user->getTotalCoinsInvites($user->id); ?></span></h2>
    
    <table class="lista-geral">
        <thead>
            <tr>
                <th style="border-left: 1px solid #031116; border-right: 1px solid #031116;">Username</th>
                <th style="border-right: 1px solid #031116;" width="230">E-mail</th>
                <th style="border-right: 1px solid #031116;">Guerreiros</th>
                <th style="border-right: 1px solid #031116;">Caçadas</th>
                <th style="border-right: 1px solid #031116;">Missões</th>
                <th style="border-right: 1px solid #031116;">PVP</th>
                <th style="border-right: 1px solid #031116;">TAM</th>
                <th style="border-right: 1px solid #031116;">Level(10)</th>
                <th style="border-right: 1px solid #031116;">Coin Recebido</th>
            </tr>
        </thead>
        <tbody>
            <?php $user->getListInvites($user->id, $pc, 30); ?>
        </tbody>
    </table>
    
    <?php break; ?>
<?php } ?>