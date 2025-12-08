<?php 
    $dadosAmigo = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
?>

<?php if($idPersonagem != $_SESSION['PERSONAGEMID']){ ?>
    <div class="chat-messenger">
        <div class="chat-header">
            <?php
                $totalPendente = $chat->getMensagensCount($_SESSION['PERSONAGEMID'], $idPersonagem);
                
                if($totalPendente > 0){
                    echo '<div class="mensagens-pendentes">'.$totalPendente.'</div>';
                }
            ?>
            
            <img src="<?php echo BASE.'assets/cards/'.$dadosAmigo->foto; ?>" alt="<?php echo $dadosAmigo->nome; ?>" />

            <?php if($user->isGuerreiroOnlineInt($idPersonagem) == 1){ ?>
                <div class="status online">
                    <i class="fas fa-circle"></i>
                    <span>Online</span>
                </div>
            <?php } else { ?>
                <div class="status">
                    <i class="fas fa-circle"></i>
                    <span>Offline</span>
                </div>
            <?php } ?>

            <h3>Chat <?php echo $dadosAmigo->nome; ?></h3>
        </div>
        <div class="chat-body">
            <p class="frase">Você está em uma conversa privada com <strong><?php echo $dadosAmigo->nome ?></strong></p>

            <div class="chat-conversation" id="chatConversation">
                <?php echo $chat->getChat($_SESSION['PERSONAGEMID'], $idPersonagem); ?>
            </div>
        </div>
        <div class="chat-footer">
            <form id="formConversation" action="" method="post">
                <input type="hidden" id="idPersonagem" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID']; ?>" />
                <input type="hidden" id="idAmigo" name="idAmigo" value="<?php echo $idPersonagem; ?>" />
                <input type="hidden" id="tipo" name="tipo" value="conversar" />
                <input type="hidden" id="baseSite" value="<?php echo BASE; ?>" />

                <textarea name="mensagem" id="mensagemChat" placeholder="Escrever mensagem..."></textarea>
                <button type="button" id="btnEnviarMensagem">
                    <i class="far fa-envelope"></i>
                    <span>Enviar</span>
                </button>
            </form>
        </div>
    </div>
    
    <script>
    // Function to load/refresh chat messages
    function loadChatMessages(){
        var idPersonagem = $('#idPersonagem').val();
        var idAmigo = $('#idAmigo').val();
        var baseSite = $('#baseSite').val();
        
        console.log('Loading messages for:', idPersonagem, idAmigo);
        
        $.ajax({
            type: 'POST',
            url: baseSite + "ajax/ajaxChat.php",
            data: {
                idPersonagem: idPersonagem,
                idAmigo: idAmigo,
                tipo: 'monitora'
            },
            success: function (res) {
                console.log('AJAX Response:', res);  // <-- ADD THIS LINE
                $('#chatConversation').html(res);
                
                var chatBox = $('#chatConversation')[0];
                if(chatBox){
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error:', error);  // <-- ADD THIS LINE
            }
        });
    }


    
    // Send message button
    $('#btnEnviarMensagem').on('click', function(){
        var idPersonagem = $('#idPersonagem').val();
        var idAmigo = $('#idAmigo').val();
        var baseSite = $('#baseSite').val();
        var tipo = $('#tipo').val();
        var mensagem = $('#mensagemChat').val();
        
        if(!mensagem.trim()){
            alert('Digite uma mensagem');
            return false;
        }
        
        console.log('Sending message:', mensagem);
        
        $.ajax({
            type: 'POST',
            url: baseSite + "ajax/ajaxChat.php",
            data: {
                idPersonagem: idPersonagem,
                idAmigo: idAmigo,
                mensagem: mensagem,
                tipo: tipo
            },
            success: function (res) {
                $('#mensagemChat').val('');
                console.log('Message sent! Response:', res);
                
                // RELOAD CHAT MESSAGES IMMEDIATELY
                loadChatMessages();
            },
            error: function(xhr, status, error){
                console.error('Error:', error);
                alert('Erro ao enviar mensagem');
            }
        });
    });
    
    // Auto-refresh every 3 seconds
    setInterval(loadChatMessages, 3000);
    </script>
<?php } ?>
