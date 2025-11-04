<?php
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($personagem->existsGuerreiro($user->id)){
        if(!isset($_SESSION['PERSONAGEMID'])){
            header('Location: '.BASE.'meus-personagens');
        }
    } else {
        header('Location: '.BASE.'criar-personagem');
    }
    
    $idBau = Url::getURL(1);
    $idBauItem = Url::getURL(2);
    $dados = $inventario->getDadosBau($idBau);
    $idPersonagem = $_SESSION['PERSONAGEMID'];
    
    if($dados->idPersonagem != $idPersonagem){
        header('Location: '.BASE.'inventario');
    }
    
    if($dados->bau == 0){
        header('Location: '.BASE.'inventario');
    }
    
    if($idBauItem != 'ajax'){
        $id = $inventario->getIdItem($idBau, $idBauItem);
    }
    
    if(!$inventario->existsItem($id)){
        header('Location: '.BASE.'inventario');
    }
    
    if(isset($_POST['destrancar'])){
        if($_SESSION['DESTRANCAR'] == 0){
            if($inventario->existsBau($id)){
                $_SESSION['DESTRANCAR'] += 1;
                $total = $inventario->getCountItensBau($dados->id);
                $sorteio = $inventario->getSorteio();

                $item_recebido = $inventario->getItemSorteado($dados->id, $sorteio);

                if($inventario->verificaItemIgual($item_recebido->nome, $idPersonagem)){
                    $slot_recebido = $inventario->verificaItemIgual($item_recebido->nome, $idPersonagem);
                }

                $campos_inventario = array(
                    'idItem' => $item_recebido->idItem,
                    'idSlot' => $slot_recebido,
                    'idPersonagem' => $idPersonagem
                );

                $core->insert('personagens_inventario_itens', $campos_inventario);

                $campos = array(
                    'novo' => 1
                );

                $where = 'id = "'.$slot_recebido.'"';

                $core->update('personagens_inventario', $campos, $where);

                $core->delete('personagens_inventario_itens', "id = $id");

                header('Location: '.BASE.'inventario');
            }
        } else {
            header('Location: '.BASE.'inventario/open');
        }
    }
?>

<div class="box-inventario">
    <div class="border-horizontal-top"></div>
    <div class="border-vertical-left"></div>
    <div class="border-top-left"></div>
    <div class="border-top-right"></div>
    <div class="border-bottom-left"></div>
    <div class="border-bottom-right"></div>
    <div class="border-vertical-right"></div>
    <div class="border-horizontal-bottom"></div>
    
    <div class="content-inventory">
        <div class="itens">
            <div class="imagem-bau">
                <img src="<?php echo BASE.'assets/'.$dados->foto; ?>" alt="<?php echo $dados->nome; ?>" />
                <h2>Itens do <?php echo $dados->nome; ?></h2>
            </div>
            <ul>
                <?php $inventario->getItensBau($dados->id); ?>
            </ul>
        </div>
        <div class="separador"></div>

        <form id="abrirBau" method="post">
            <input type="hidden" name="destrancar" value="1" />
            <button type="submit" id="destrancarBau" class="bts-form"><i class="fas fa-key"></i> Destrancar Baú</button>
        </form>
    </div>
</div>