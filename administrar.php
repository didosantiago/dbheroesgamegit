<?php switch($acao) {
    default: ?>
    
    
    <?php break; ?>
    
    <?php case 'online': ?>
        <h2 class="title">Visitantes Online</h2>
    
        <table class="lista-geral">
            <thead>
                <tr>
                    <th style="border-right: 1px solid #031116;">Nome</th>
                    <th style="border-right: 1px solid #031116;">Usuário</th>
                    <th style="border-right: 1px solid #031116;">E-mail</th>
                    <th style="border-right: 1px solid #031116;">Data Cadastro</th>
                    <th style="border-right: 1px solid #031116;">Gold</th>
                    <th>Nível</th>
                </tr>
            </thead>
            <tbody>
                <?php $administrar->getListVisitantesOnline($pc, 30); ?>
            </tbody>
        </table>
    <?php break; ?>
        
    <?php case 'faturamento': ?>
        <h2 class="title">Transações no Mês Atual</h2>
    
        <table class="lista-geral">
            <thead>
                <tr>
                    <th style="border-right: 1px solid #031116;">Nome</th>
                    <th style="border-right: 1px solid #031116;">E-mail</th>
                    <th style="border-right: 1px solid #031116;">Data</th>
                    <th style="border-right: 1px solid #031116;">Valor</th>
                    <th>Coins</th>
                </tr>
            </thead>
            <tbody>
                <?php $administrar->getListTransacoes($pc, 30); ?>
            </tbody>
        </table>
    <?php break; ?>
<?php } ?>