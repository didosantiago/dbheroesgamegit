<?php 
    $dados = $core->getDados('usuarios', 'WHERE id = '.$user->id);
    
    if(isset($_REQUEST['salvar'])){
        
        $fotoAnterior = $user->foto;
        $retorno = $fotoAnterior;
        
        if($_FILES['foto']['error'] != 4){
            if($core->validaTamanhoImagem('foto', 300)){
                if($fotoAnterior != 'assets/user-blank.jpg'){
                    unlink($fotoAnterior);
                }

                $foto = $_FILES['foto'];

                $upload = new Upload($foto, 1000, 1000, "uploads/user/");
                $newPhoto = $upload->salvar();
                $retorno = 'uploads/user/'.$newPhoto;
                
                $campos = array(
                        'nome' => addslashes($_POST['nome']),
                        'data_aniversario' => $core->dataMysql(addslashes($_POST['data_aniversario'])),
                        'email' => addslashes($_POST['email']),
                        'cep' => addslashes($_POST['cep']),
                        'endereco' => addslashes($_POST['endereco']),
                        'bairro' => addslashes($_POST['bairro']),
                        'cidade' => addslashes($_POST['cidade']),
                        'estado' => addslashes($_POST['estado']),
                        'receber_email' => addslashes($_POST['receber_email']),
                        'foto' => $retorno
                      );

                $where = 'id="'.$user->id.'"';

                if($core->update('usuarios', $campos, $where)){
                    $core->msg('sucesso', 'Dados Alterados.');
                    header('Location: '.BASE.'perfil/');
                } else {
                    $core->msg('error', 'Erro na Alteração.');
                }
            } else {
                $core->msg('error', 'Tamanho de Imagem não permitido.');
            }
        } else {
            $campos = array(
                    'nome' => addslashes($_POST['nome']),
                    'data_aniversario' => $core->dataMysql(addslashes($_POST['data_aniversario'])),
                    'email' => addslashes($_POST['email']),
                    'cep' => addslashes($_POST['cep']),
                    'endereco' => addslashes($_POST['endereco']),
                    'bairro' => addslashes($_POST['bairro']),
                    'cidade' => addslashes($_POST['cidade']),
                    'estado' => addslashes($_POST['estado']),
                    'receber_email' => addslashes($_POST['receber_email']),
                    'foto' => $retorno
                  );

            $where = 'id="'.$user->id.'"';

            if($core->update('usuarios', $campos, $where)){
                $core->msg('sucesso', 'Dados Alterados.');
                header('Location: '.BASE.'perfil/');
            } else {
                $core->msg('error', 'Erro na Alteração.');
            }
        }
    }
?>

<h2 class="title">Meu Perfil</h2>

<?php if(!$user->getEmailVerificado($user->id)){ ?>
    <div class="nao-validado">
        <p>Seu e-mail ainda não foi confirmado, confirme clicando no link enviado para seu email. Para reenviar o link <a href="<?php echo BASE; ?>autenticar/enviar">Clique Aqui.</a></p>
        <p style="margin-bottom: 0;">*Preencha seu Cadastro completo.</p>
    </div>
<?php } ?>

<form id="formPerfil" class="forms" action="" method="post" enctype="multipart/form-data">
    <div class="foto-user">
        <img src="<?php echo BASE.$user->foto; ?>" alt="" />
    </div>
    
    <p style="display: block; padding: 20px 0; color: #555;">
        Atenção: A imagem deve ter no mínimo 300 x 300 pixels.
    </p>
    
    <div class="campos block" style="width: 500px;">
        <label>Foto: </label>
        <input type="file" name="foto" value="" />
    </div>
    <div class="campos" style="width: 300px;">
        <label>Nome: </label>
        <input type="text" name="nome" value="<?php echo $dados->nome; ?>" required />
    </div>
    <div class="campos" style="width: 200px;">
        <label>Data de Aniversário: </label>
        <input type="text" name="data_aniversario" value="<?php echo $core->dataBR($dados->data_aniversario); ?>" />
    </div>
    <div class="campos block" style="width: 500px;">
        <label>E-mail: </label>
        <input type="text" name="email" value="<?php echo $dados->email; ?>" required />
    </div>
    <div class="campos" style="width: 150px;">
        <label>CEP: </label>
        <input type="text" name="cep" value="<?php echo $dados->cep; ?>" />
    </div>
    <div class="campos" style="width: 350px;">
        <label>Endereço: </label>
        <input type="text" name="endereco" value="<?php echo $dados->endereco; ?>" />
    </div>
    <div class="campos block" style="width: 500px;">
        <label>Bairro: </label>
        <input type="text" name="bairro" value="<?php echo $dados->bairro; ?>" />
    </div>
    <div class="campos" style="width: 300px;">
        <label>Cidade: </label>
        <input type="text" name="cidade" value="<?php echo $dados->cidade; ?>" />
    </div>
    <div class="campos" style="width: 200px;">
        <label>Estado: </label>
        <input type="text" name="estado" value="<?php echo $dados->estado; ?>" />
    </div>
    <div class="campos block check" style="width: 500px">
        <label for="receber_email">
            <input type="checkbox" id="receber_email" name="receber_email" <?php $core->isNewChecked($dados->receber_email, '1') ?> value="1" />
            Desejo receber E-mails com Novidades e Atualizações
        </label>
    </div>
    <div class="campos block check" style="width: 500px">
        <label for="aceite">
            <input type="checkbox" id="aceite" name="aceite" disabled <?php $core->isNewChecked($dados->aceite, '1') ?> value="1" required />
            Declaro que li e aceito as regras do jogo
        </label>
    </div>
    
    <input type="submit" id="salvar" class="bts-form" name="salvar" value="Salvar Dados" />
</form>

