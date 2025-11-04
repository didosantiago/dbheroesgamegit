<?php 
    $dadosChat = $core->getDados('equipes_chat', "WHERE idEquipe = ".$idGet);
    $dados_equipe = $core->getDados('equipes', "WHERE id = ".$idGet);
?>

<?php if($equipes->isMembro($_SESSION['PERSONAGEMID'], $idGet)){ ?>
    <?php if(!empty($dadosChat)){ ?>
        <div class="chat-equipe">
            <div class="chat-header">
                <img src="<?php echo BASE.'assets/equipes/'.$dados_equipe->foto; ?>" alt="<?php echo $dados_equipe->nome; ?>" />

                <?php if($dadosChat->status == 1){ ?>
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

                <h3>Chat <?php echo $dados_equipe->nome; ?></h3>
            </div>
            <div class="chat-body">
                <p class="frase"><?php echo $dadosChat->frase; ?></p>

                <div class="chat-conversation">
                    <?php echo $equipes->getInteracoesChat($idGet); ?>
                </div>
            </div>
            <div class="chat-footer">
                <form id="formConversation" action="" method="post">
                    <input type="hidden" id="idMembro" name="idMembro" value="<?php echo $_SESSION['PERSONAGEMID']; ?>" />
                    <input type="hidden" id="idEquipe" name="idEquipe" value="<?php echo $idGet; ?>" />
                    <input type="hidden" id="tipo" name="tipo" value="conversar" />
                    <?php if($dadosChat->status == 1){ ?>
                        <textarea name="mensagem" id="mensagemChatEquipe" placeholder="Escrever mensagem..."></textarea>
                        <button type="button" id="btnEnviarMensagem">
                            <i class="far fa-envelope"></i>
                            <span>Enviar</span>
                        </button>
                    <?php } ?>
                </form>
            </div>
        </div>
    <?php } ?>
<?php } ?>