<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if(isset(_POST['aceitar'])){
        $campos = array(
            'aceitou' => 1
        );

        $where = 'id="'.addslashes($_POST['aceitar']).'"';

        if($core->update('personagens_amigos', $campos, $where)){
            $core->msg('sucesso', 'Amizade Confirmada.');
            header('Location: '.BASE.'amigos/');
        } else {
            $core->msg('error', 'Ocorreu um Erro ao efetuar Aceitar Amizade.');
        }
    }
    
    if(isset($_POST['deletar'])){
        if($core->delete('personagens_amigos', "id = ".addslashes($_POST['deletar']))){
            $core->msg('sucesso', 'Amizade Desfeita.');
            header('Location: '.BASE.'amigos/');
        } else {
            $core->msg('error', 'Erro ao desfazer Amizade.');
            header('Location: '.BASE.'amigos/');
        }
    }
?>

<h2 class="title">Lista de Amigos</h2>

<table class="lista-geral">
    <thead>
        <tr>
            <th>Foto</th>
            <th>Guerreiro</th>
            <th width="400">Graduação</th>
            <th>Level</th>
            <th>Vitórias PVP</th>
            <th>Derrotas PVP</th>
            <th>Vitórias (TAM)</th>
            <th>Gold Faturado</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $personagem->getAmigos($_SESSION['PERSONAGEMID']); 
        ?>
    </tbody>
</table>