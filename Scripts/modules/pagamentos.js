DBH.pagamentos = (function() {
    var init = function() {
        
    },
    pagamentoDoacao = function(i) {
        $.ajax({
            type: "POST",
            url: "ajax/ajaxDoacao.php",
            data: new FormData(i),
            processData: false,
            cache: false,
            contentType: false,
            success: function (res) {
                $(i).find('#code').val(res);
                
                PagSeguroLightbox({
                    code: res
                }, {
                    success : function(transactionCode) {
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxTransacoes.php",
                            data: {
                                transaction_id: res,
                                retorno: 'success'
                            },
                            success: function (res) {
                                var url_atual = window.location.href;
                                window.location.href = url_atual.replace('doacao', 'transacoes');
                            }
                        });
                    },
                    abort : function(transactionCode) {            
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxTransacoes.php",
                            data: {
                                transaction_id: res,
                                retorno: 'abort'
                            },
                            success: function (res) {
                                
                            }
                        });
                    }
                });
            }
        });
    }
    
    return {
        init: init,
        pagamentoDoacao: pagamentoDoacao
    }
}());