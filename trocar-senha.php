<?php 
    $dados = $core->getDados('usuarios', 'WHERE id = '.$user->id);
    
    if(isset($_REQUEST['salvar'])){
        
        if(addslashes($_POST['senha']) == addslashes($_POST['confirmacao'])){
            $campos = array(
                'senha' => md5(addslashes($_POST['senha']))
              );

            $where = 'id="'.$user->id.'"';

            if($core->update('usuarios', $campos, $where)){
                $core->msg('sucesso', 'Senha Alterada.');
                header('Location: '.BASE.'trocar-senha/');
            } else {
                $core->msg('error', 'Erro na Alteração.');
            }
        } else {
            $core->msg('error', 'Senhas Divergentes');
        }
    }
?>

<h2 class="title">Trocar Minha Senha</h2>

<form id="formSenha" class="forms" action="" method="post">
    <div class="campos block" style="width: 500px;">
        <label>Senha: </label>
        <input type="password" name="senha" value="" required />
    </div>
    
    <div class="campos block" style="width: 500px;">
        <label>Confirme a Nova Senha: </label>
        <input type="password" name="confirmacao" value="" required />
    </div>
    
    <input type="submit" id="salvar" class="bts-form" name="salvar" value="Trocar Senha" />
</form>

