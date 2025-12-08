<?php
$core = new Core();

if(isset($_POST['depositar'])){
    $gold_to_deposit = intval($_POST['gold']);
    
    if($gold_to_deposit > 0 && $gold_to_deposit <= $personagem->gold){
        $campos = array(
            'gold' => $personagem->gold - $gold_to_deposit,
            'gold_guardados' => $personagem->gold_guardados + $gold_to_deposit
        );
        
        $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';
        
        if($core->update('usuarios_personagens', $campos, $where)){
            $core->msg('sucesso', 'Depósito Realizado.');
            header('Location: '.BASE.'banco/');
            exit();
        } else {
            $core->msg('error', 'Erro ao efetuar Depósito.');
        }
    } else {
        $core->msg('error', 'Valor não Permitido');
    }
}

if(isset($_POST['sacar'])){
    $gold_to_withdraw = intval($_POST['gold']);
    
    if($gold_to_withdraw > 0 && $gold_to_withdraw <= $personagem->gold_guardados){
        $campos = array(
            'gold' => $personagem->gold + $gold_to_withdraw,
            'gold_guardados' => $personagem->gold_guardados - $gold_to_withdraw
        );
        
        $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';
        
        if($core->update('usuarios_personagens', $campos, $where)){
            $core->msg('sucesso', 'Saque Realizado.');
            header('Location: '.BASE.'banco/');
            exit();
        } else {
            $core->msg('error', 'Erro ao efetuar Saque.');
        }
    } else {
        $core->msg('error', 'Valor não Permitido');
    }
}

// ✅ SECURITY FIX: Players cannot change item price
if(isset($_POST['vender'])){
    $idVenda = intval($_POST['idVenda']);
    $idItem = intval($_POST['id']);
    
    // ⚠️ IGNORE the valor from POST - it can be tampered!
    // Instead, get the REAL price from database
    
    // Validate that the item exists and belongs to this character
    if($core->isExists('personagens_inventario_itens', "WHERE id = ".$idVenda." AND idPersonagem = ".$_SESSION['PERSONAGEMID'])){
        
        // Get item data from database (trusted source)
        $dadosItem = $core->getDados('itens', 'WHERE id = '.$idItem);
        
        if(!$dadosItem){
            $core->msg('error', 'Item não encontrado.');
            header('Location: '.BASE.'banco/');
            exit();
        }
        
        // 🔒 USE ONLY THE DATABASE VALUE - never trust user input for prices!
        $valorReal = intval($dadosItem->preco_venda_min);
        
        // Validate price is positive
        if($valorReal <= 0){
            $valorReal = 1; // Minimum 1 gold
        }
        
        // ✅ Delete item FIRST, then give gold only if successful
        if($core->delete('personagens_inventario_itens', "id = ".$idVenda)){
            
            // Now give the REAL gold value from database
            $campos = array(
                'gold' => $personagem->gold + $valorReal,
            );
            $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';
            
            if($core->update('usuarios_personagens', $campos, $where)){
                $core->msg('sucesso', 'Item "'.$dadosItem->nome.'" vendido por '.$valorReal.' gold(s)!');
            } else {
                $core->msg('error', 'Item removido mas erro ao adicionar gold. Contate o administrador.');
            }
            
            header('Location: '.BASE.'banco/');
            exit();
        } else {
            $core->msg('error', 'Erro ao vender item.');
            header('Location: '.BASE.'banco/');
            exit();
        }
    } else {
        $core->msg('error', 'Item não encontrado no seu inventário.');
        header('Location: '.BASE.'banco/');
        exit();
    }
}
?>

<div class="banco-shenlong-banner"></div>
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

<!-- ✅ FIX: Add JavaScript confirmation popup -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all sell forms
    const sellForms = document.querySelectorAll('form input[name="vender"]');
    
    sellForms.forEach(function(button) {
        button.closest('form').addEventListener('submit', function(e) {
            const valorInput = this.querySelector('input[name="valor"]');
            const valor = valorInput ? valorInput.value : '0';
            const itemName = this.querySelector('.market_listing_item_name')?.textContent || 'este item';
            
            const confirmed = confirm('Tem certeza que deseja vender "' + itemName + '" por ' + valor + ' gold(s)?\n\nVocê não poderá recuperar este item!');
            
            if(!confirmed){
                e.preventDefault();
                return false;
            }
        });
    });
});
</script>
