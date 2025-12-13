<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>
 <div class="historico-pvp-banner"></div>
<h2 class="title">Veja abaixo o seu histórico PvP</h2>

<table class="lista-batalhas">
    <thead>
        <tr>
            <th>Guerreiro que Atacou</th>
            <th>Guerreiro Atacado</th>
            <th>Data</th>
            <th>Vitória</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $batalha->getHistorico($_SESSION['PERSONAGEMID'], $pc, 10); 
        ?>
    </tbody>
</table>