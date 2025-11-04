<?php 
    if(isset($_POST['confirmar'])){
        $dados_atual = $core->getDados('usuarios_personagens', 'WHERE id = '.$_SESSION["PERSONAGEMID"]);
        $dados_guerreiro = $core->getDados('personagens', 'WHERE id = '.addslashes($_POST['idPersonagem']));
        
        if($dados_atual->idPersonagem != addslashes($_POST['idPersonagem'])){
            $campos = array(
                'idPersonagem' => addslashes($_POST['idPersonagem']),
                'foto' => $dados_guerreiro->foto
            );
            
            $where = 'id="'.$_SESSION["PERSONAGEMID"].'"';

            if($core->update('usuarios_personagens', $campos, $where)){
                $core->msg('sucesso', 'Guerreiro Alterado.');
                if(Url::getURL(1) != null){
                    header('Location: '.BASE.'loja');
                } else {
                    header('Location: '.BASE.'portal/');
                }
                
            } else {
                $core->msg('error', 'Erro na Alteração.');
            }
        } else {
            $core->msg('error', 'Você não pode trocar pelo mesmo guerreiro.');
        }
    }
?>

<h2 class="title">Escolha o guerreiro</h2>

<form id="formTrocaPersonagem" class="forms" action="" method="post">
    <?php 
        $personagem->getList();
    ?>
    
    <input type="submit" class="btn-confirmar bts-form" name="confirmar" value="Confirmar" />
</form>
