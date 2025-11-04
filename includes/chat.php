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

                <textarea name="mensagem" id="mensagemChat" placeholder="Escrever mensagem..."></textarea>
                <button type="button" id="btnEnviarMensagem">
                    <i class="far fa-envelope"></i>
                    <span>Enviar</span>
                </button>
            </form>
        </div>
    </div>
<?php } ?>