<?php switch($acao) {
    default: ?>
    <div class="tabela-de-graduacao-banner"></div>
    <h2 class="title">Olhe abaixo as graduações!</h2>
    
    <table class="tableList">
        <thead>
            <tr>
                <th style="width: 25%; border-right: 1px solid #031116;">Escudo</th>
                <th style="border-right: 1px solid #031116;">Graduação</th>
                <th style="border-right: 1px solid #031116;">Level Inicial</th>
                <th style="border-right: 1px solid #031116;">Status Extra</th>
            </tr>
        </thead>
        <tbody>
            <?php $personagem->getListGraduacoes($pc, 50); ?>
        </tbody>
    </table>
    
    <?php break; ?>
<?php } ?>