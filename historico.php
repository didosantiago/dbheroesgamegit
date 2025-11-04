<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">Histórico PVP</h2>

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