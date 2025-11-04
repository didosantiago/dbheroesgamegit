<?php require_once 'front/header-front.php'; ?>

<div class="secao-ranking">
    <div class="stm-container">
        <h2>Ranking TOP 10 Jogadores</h2>
        
        <table class="lista-ranking">
            <thead>
                <tr>
                    <th></th>
                    <th width="100">Foto</th>
                    <th width="400">Guerreiro</th>
                    <th width="200">Graduação</th>
                    <th width="200">Level</th>
                    <th width="80">Planeta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $personagem->getRankingFront(); 
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'front/footer-front.php'; ?>