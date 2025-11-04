<?php switch($acao) {
    default: ?>
    <h2 class="title">Tabela de Graduações</h2>
    
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