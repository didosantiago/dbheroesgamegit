// =========================================
// LOJA PURCHASE HANDLER - Standalone Version
// =========================================
$(document).ready(function() {
    $(document).on('click', '.btn-comprar-loja', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var idProduto = $button.data('idproduto');
        var guerreiro = $('#personagemLogged').val();
        
        if (!idProduto) {
            console.error("ID do produto não encontrado!");
            return;
        }
        
        console.log("Comprando produto:", idProduto, "Guerreiro:", guerreiro);
        
        // Disable button during purchase
        $button.prop('disabled', true).text('Processando...');
        
        $.ajax({
            type: "POST",
            url: "ajax/ajaxAdquirirItem.php",
            data: { 
                id: idProduto,
                idPersonagem: guerreiro
            },
            success: function(response) {
                console.log("Compra bem-sucedida!");
                // Reload page to update coins/inventory
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Erro na compra:", error);
                console.error("Response:", xhr.responseText);
                alert('Erro ao processar compra. Tente novamente.');
                $button.prop('disabled', false).text('COMPRAR');
            }
        });
    });
});
