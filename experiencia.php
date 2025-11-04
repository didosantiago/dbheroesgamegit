<?php switch($acao) {
    default: ?>
    <h2 class="title">Tabela de Experiência</h2>
    
    <table class="tableList">
        <thead>
            <tr>
                <th style="border-right: 1px solid #031116;">Level</th>
                <th style="border-right: 1px solid #031116;">Experiência</th>
            </tr>
        </thead>
        <tbody>
            <?php $personagem->getListExperiencia($pc, 100); ?>
        </tbody>
    </table>
    
    <?php break; ?>
<?php } ?>