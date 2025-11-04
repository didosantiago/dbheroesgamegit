<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">Ranking de Guerreiros</h2>

<form id="formRanking" method="post">
    <div class="campos-form" style="width: 300px;">
        <label>Filtrar Por:</label>
        <select name="tipo" id="tipo">
            <option value="1">Ranking Geral</option>
            <option value="2">Vitórias PVP</option>
            <option value="4">Torneio de Artes Marciais(TAM)</option>
        </select>
    </div>
    
    <div class="campos-form" style="width: 200px;">
        <label>Planeta:</label>
        <select name="planeta" id="planeta">
            <option disabled selected>Selecione</option>
            <option value="1">Terra</option>
            <option value="2">Vegeta</option>
            <option value="3">Namekusei</option>
        </select>
    </div>
    
    <input type="submit" class="bts-form" id="filtrar" name="filtrar" value="Filtrar" />
</form>

<table class="lista-ranking">
    <thead>
        <tr>
            <th>Rank</th>
            <th>Foto</th>
            <th>Guerreiro</th>
            <th width="400">Graduação</th>
            <th>Level</th>
            <th>Vitórias PVP</th>
            <th>Derrotas PVP</th>
            <th>Vitórias (TAM)</th>
            <th>Gold Faturado</th>
            <th>Status</th>
            <th>Planeta</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if(isset($_POST['filtrar'])){
                $personagem->getRanking($_POST['tipo'], addslashes($_POST['planeta']), $pc, 50);
            } else {
                $personagem->getRanking('', '', $pc, 50); 
            }
        ?>
    </tbody>
</table>