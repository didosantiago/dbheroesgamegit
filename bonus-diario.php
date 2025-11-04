<?php 
    $dia_da_semana = $core->getDiaSemana();
    
    if(isset($_POST['coletar'])){
        $dados = $core->getDados('adm_recompensas', 'WHERE id = '.addslashes($_POST['id']));
        
        if($dados->dia_semana == 'domingo'){
            $dia = 1;
        } else if($dados->dia_semana == 'segunda'){
            $dia = 2;
        } else if($dados->dia_semana == 'terca'){
            $dia = 3;
        } else if($dados->dia_semana == 'quarta'){
            $dia = 4;
        } else if($dados->dia_semana == 'quinta'){
            $dia = 5;
        } else if($dados->dia_semana == 'sexta'){
            $dia = 6;
        } else if($dados->dia_semana == 'sabado'){
            $dia = 7;
        }
        
        $data_item_coleta = $core->getSemanaAtual($dia);
        
        if(date('Y-m-d') == $data_item_coleta){
            $campos = array(
                'idPersonagem' => $_SESSION['PERSONAGEMID'],
                'idRecompensa' => addslashes($_POST['id']),
                'data' => date('Y-m-d')
            );

            $core->insert('personagens_recompensas', $campos);

            if($dados->premio == 'gold'){
                $campos = array(
                    'gold' => intval($personagem->gold) + intval($dados->valor),
                    'gold_total' => intval($personagem->gold_total) + intval($dados->valor)
                );

                $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    $conteudo = '<p>Parabéns, você coletou '.$dados->valor.' golds diário.</p>';
                    $core->setNotification($conteudo, 'sucesso', $_SESSION['PERSONAGEMID']);

                    header('Location: '.BASE.'bonus-diario');
                }
            } else if($dados->premio == 'item'){
                $dadosItem = $core->getDados('itens', 'WHERE id = '.$dados->valor);

                if($inventario->verificaItemIgual($dadosItem->nome, $_SESSION['PERSONAGEMID'])){
                    $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $_SESSION['PERSONAGEMID']);

                    $campos = array(
                        'novo' => 1
                    );

                    $where = 'id = "'.$slot_recebido.'"';

                    $core->update('personagens_inventario', $campos, $where);

                    $campos_add = array(
                        'idItem' => $dadosItem->id,
                        'idSlot' => $slot_recebido,
                        'idPersonagem' => $_SESSION['PERSONAGEMID']
                    );

                    $core->insert('personagens_inventario_itens', $campos_add);

                    $conteudo = '<p>Parabéns, você coletou o item '.$dadosItem->nome.', adicionado ao seu inventário.</p>';
                    $core->setNotification($conteudo, 'sucesso', $_SESSION['PERSONAGEMID']);

                    header('Location: '.BASE.'inventario');
                }
            }
        } else {
            header('Location: '.BASE.'bonus-diario');
            $core->msg('error', 'Item não permitido para coleta.');
        }
    }
?>

<h2 class="title">Adquira seu Bônus do Dia</h2>

<ul class="dias-lista">
    <?php
        $treino->getListBonus($dia_da_semana, $_SESSION['PERSONAGEMID']); 
    ?> 
</ul>