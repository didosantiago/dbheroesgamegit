<?php 
    if(isset($_POST['depositar'])){ 
        if(intval(addslashes($_POST['gold'])) <= $personagem->gold){
            $campos = array(
                'gold' => $personagem->gold - addslashes($_POST['gold']),
                'gold_guardados' => $personagem->gold_guardados + addslashes($_POST['gold'])
            );

            $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';

            if($core->update('usuarios_personagens', $campos, $where)){
                $core->msg('sucesso', 'Depósito Realizado.');
                header('Location: '.BASE.'banco/');
            } else {
                $core->msg('error', 'Erro ao efetuar Depósito.');
            }
        } else {
            $core->msg('error', 'Valor não Permitido');
        }
    }
    
    if(isset($_POST['sacar'])){ 
        if(intval(addslashes($_POST['gold'])) <= $personagem->gold_guardados){
            $campos = array(
                'gold' => $personagem->gold + addslashes($_POST['gold']),
                'gold_guardados' => $personagem->gold_guardados - addslashes($_POST['gold'])
            );

            $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';

            if($core->update('usuarios_personagens', $campos, $where)){
                $core->msg('sucesso', 'Saque Realizado.');
                header('Location: '.BASE.'banco/');
            } else {
                $core->msg('error', 'Erro ao efetuar Saque.');
            }
        } else {
            $core->msg('error', 'Valor não Permitido');
        }
    }
    
    if(isset($_POST['vender'])){ 
        if($core->isExists('personagens_inventario_itens', "WHERE id = ".addslashes($_POST['idVenda']))){
            $dadosItem = $core->getDados('itens', 'WHERE id = '.addslashes($_POST['id']));

            $campos = array(
                'gold' => $personagem->gold + addslashes($_POST['valor']),
            );

            $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';

            $core->update('usuarios_personagens', $campos, $where);

            if($core->delete('personagens_inventario_itens', "id = ".addslashes($_POST['idVenda']))){
                $core->msg('sucesso', 'Item Vendido.');
                header('Location: '.BASE.'banco/');
            } else {
                $core->msg('error', 'Erro ao vender item.');
            }
        } else {
            $core->msg('error', 'Erro ao buscar item.');
        }
    }
?>

<h2 class="title">Bem vindo ao Banco Central</h2>

<div class="depositos">
    <h3>Depositar Gold</h3>
    <span class="possui">Você possui <?php echo $personagem->gold ?> gold(s)</span>
    <form id="formDeposito" action="" method="post">
        <input type="hidden" name="depositar" />
        <input type="number" name="gold" value="" placeholder="0" />
        <button type="submit">
            <i class="fas fa-piggy-bank"></i> Depositar
        </button>
    </form>
</div>

<div class="saques">
    <h3>Sacar Gold</h3>
    <span class="possui">Você possui <?php echo $personagem->gold_guardados ?> gold(s) no Banco</span>
    <form id="formDeposito" action="" method="post">
        <input type="hidden" name="sacar" />
        <input type="number" name="gold" value="" placeholder="0" />
        <button type="submit">
            <i class="fas fa-piggy-bank"></i> Sacar
        </button>
    </form>
</div>

<div class="market-itens-inventario">
    <h3 class="subtitle">Itens do Meu Inventário</h3>
    
    <div class="market-itens-header">
        <div class="tag-market nome">
            <span>Nome</span>
        </div>
        <div class="tag-market preco">
            <span>Preço de Mercado</span>
        </div>
        <div class="tag-market acoes">

        </div>
    </div>

    <?php $mercado->getListInventarioBanco($_SESSION['PERSONAGEMID'], $pc, 5); ?>              
</div>