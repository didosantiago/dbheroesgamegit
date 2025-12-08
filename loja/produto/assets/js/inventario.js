// =========================================
// LOJA PURCHASE HANDLER - Modal Version
// =========================================
$(document).ready(function() {
    
    // Store the product ID when confirmation modal opens
    var currentProductId = null;
    
    // When "ADQUIRIR ITEM" button is clicked, store the product ID
    $(document).on('click', '.confirma-compra', function(e) {
        e.preventDefault();
        currentProductId = $(this).data('idproduto');
        console.log("Produto selecionado para confirmação:", currentProductId);
    });
    
    // When modal "Sim" button is clicked, process the purchase
    $(document).on('click', '#confirmaProdutoSim', function(e) {
        e.preventDefault();
        
        var guerreiro = $('#personagemLogged').val();
        
        if (!currentProductId) {
            console.error("ID do produto não encontrado!");
            alert('Erro: ID do produto não encontrado!');
            return;
        }
        
        console.log("Comprando produto:", currentProductId, "Guerreiro:", guerreiro);
        
        // Close modal
        $('#confirmaProduto').modal('hide');
        
        $.ajax({
            type: "POST",
            url: "ajax/ajaxAdquirirItem.php",
            data: { 
                id: currentProductId,
                idPersonagem: guerreiro
            },
            success: function(response) {
                console.log("Compra bem-sucedida!");
                console.log("Response:", response);
                // Reload page to update coins/inventory
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Erro na compra:", error);
                console.error("Response:", xhr.responseText);
                alert('Erro ao processar compra. Tente novamente.');
            }
        });
    });
    
    // Reset product ID when modal is closed
    $(document).on('hidden.bs.modal', '#confirmaProduto', function () {
        currentProductId = null;
    });
});
