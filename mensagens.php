<?php switch($acao) {
    default: ?>
        <h2 class="title">Conversas Pendentes</h2>

        <table class="lista-mensagens">
            <thead>
                <tr>
                    <th style="width: 100px; border-right: 1px solid #031116;">Jogador</th>
                    <th style="width: 500px; border-right: 1px solid #031116;">Última Mensagem</th>
                    <th style="width: 150px; border-right: 1px solid #031116;">Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $chat->getListConversas($_SESSION["PERSONAGEMID"]); ?>
            </tbody>
        </table>
    
    <?php break; ?>
<?php } ?>