<?php switch($acao) {
    default: ?>
        <?php 
            $chave = Url::getURL(2);
            $campos = array(
                'email_validado' => 1
            );

            $where = 'id="'.$user->id.'"';

            if($core->update('usuarios', $campos, $where)){
                $core->msg('sucesso', 'E-mail Validado.');
                header('Location: '.BASE.'perfil/');
            } else {
                $core->msg('error', 'Erro ao validar e-mail.');
                header('Location: '.BASE.'perfil/');
            }
        ?>
    <?php break; ?>

    <?php case 'enviar': ?>
        <?php 
            if($user->enviaConfirmacao($user->id)){
                $core->msg('sucesso', 'Confirmação enviada por e-mail.');
                header('Location: '.BASE.'perfil/');
            } else {
                $core->msg('error', 'Erro no envio.');
                header('Location: '.BASE.'perfil/');
            }
        ?>
    <?php break; ?>
<?php } ?>