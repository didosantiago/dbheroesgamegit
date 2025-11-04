<?php
    $pay->setViewTransaction($user->id); 
?>

<h2 class="title">Minhas Transações</h2>

<table class="lista-transacoes">
    <thead>
        <tr>
            <th>Data</th>
            <th>Valor</th>
            <th>Coins</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $pay->getMyTransactions($user->id); 
        ?>
    </tbody>
</table>