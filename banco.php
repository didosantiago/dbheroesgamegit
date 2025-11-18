<?php
$core = new Core();

if(isset($_POST['depositar'])){
    $gold_to_deposit = intval($_POST['gold']); // Convert to integer FIRST
    
    if($gold_to_deposit > 0 && $gold_to_deposit <= $personagem->gold){
        $campos = array(
            'gold' => $personagem->gold - $gold_to_deposit,
            'gold_guardados' => $personagem->gold_guardados + $gold_to_deposit
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
    $gold_to_withdraw = intval($_POST['gold']); // Convert to integer FIRST
    
    if($gold_to_withdraw > 0 && $gold_to_withdraw <= $personagem->gold_guardados){
        $campos = array(
            'gold' => $personagem->gold + $gold_to_withdraw,
            'gold_guardados' => $personagem->gold_guardados - $gold_to_withdraw
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
    $idVenda = intval($_POST['idVenda']);
    $idItem = intval($_POST['id']);
    $valor = intval($_POST['valor']);
    
    if($core->isExists('personagens_inventario_itens', "WHERE id = ".$idVenda)){
        $dadosItem = $core->getDados('itens', 'WHERE id = '.$idItem);
        
        $campos = array(
            'gold' => $personagem->gold + $valor,
        );
        
        $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';
        $core->update('usuarios_personagens', $campos, $where);
        
        if($core->delete('personagens_inventario_itens', "id = ".$idVenda)){
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

<div class="kame-bank-banner"></div>
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