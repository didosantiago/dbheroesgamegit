<?php switch($acao) {
    default: ?>
        <h2 class="title">Configurações</h2>
        
        <?php 
            $dados = $core->getDados('configuracoes', '');

            if(isset($_REQUEST['salvar'])){

                $campos = array(
                    'titulo' => addslashes($_POST['titulo']),
                    'meta_description' => addslashes($_POST['meta_description']),
                    'meta_keywords' => addslashes($_POST['meta_keywords']),
                    'teste' => addslashes($_POST['teste']),
                    'erros' => addslashes($_POST['erros']),
                    'meta_doacao' => addslashes($_POST['meta_doacao']),
                    'vencimento_doacao' => addslashes($_POST['vencimento_doacao'])
                );

                $where = 'id="'.$dados->id.'"';

                if($core->update('configuracoes', $campos, $where)){
                    $core->msg('sucesso', 'Dados Alterados.');
                    header('Location: '.BASE.'configuracoes/');
                } else {
                    $core->msg('error', 'Erro na Alteração.');
                }
            }
        ?>
        
        <form id="formConfiguracoes" class="forms" action="" method="post" style="width: 90%; margin: 0 auto;">
            <div class="campos">
                <label>Título: </label>
                <input type="text" name="titulo" value="<?php echo $dados->titulo; ?>" />
            </div>

            <div class="campos">
                <label>Meta Description: </label>
                <textarea rows="8" name="meta_description"><?php echo $dados->meta_description; ?></textarea>
            </div>
            
            <div class="campos">
                <label>Palavras Chave: </label>
                <textarea rows="15" name="meta_keywords"><?php echo $dados->meta_keywords; ?></textarea>
            </div>
            
            <div class="campos" style="width: 200px;">
                <label>Meta Doação: </label>
                <input type="text" name="meta_doacao" value="<?php echo $dados->meta_doacao; ?>" />
            </div>

            <div class="campos" style="width: 200px;">
                <label>Dia de Vencimento da Doação: </label>
                <input type="text" name="vencimento_doacao" value="<?php echo $dados->vencimento_doacao; ?>" />
            </div>

            <div class="campos" style="width: 250px;">
                <label>Ambiente em Teste:</label>
                <select id="teste" name="teste">
                    <option value="1" <?php echo ($dados->teste == '1') ? 'selected' : '';  ?>>Sim</option>
                    <option value="0" <?php echo ($dados->teste == '0') ? 'selected' : '';  ?>>Não</option>
                </select>
            </div>

            <div class="campos" style="width: 250px;">
                <label>Mostrar Erros:</label>
                <select id="erros" name="erros">
                    <option value="1" <?php echo ($dados->erros == '1') ? 'selected' : '';  ?>>Sim</option>
                    <option value="0" <?php echo ($dados->erros == '0') ? 'selected' : '';  ?>>Não</option>
                </select>
            </div>
            
            <input type="submit" id="salvar" class="bts-form" name="salvar" value="Salvar" />
        </form>
    <?php break; ?>
        
    <?php case 'pagseguro': ?>
        <h2 class="title">PAGSEGURO</h2>
        
        <?php 
            $dados = $core->getDados('configuracoes', '');

            if(isset($_POST['salvar'])){

                $campos = array(
                    'pagseguro_env' => addslashes($_POST['pagseguro_env']),
                    'PAGSEGURO_EMAIL' => addslashes($_POST['PAGSEGURO_EMAIL']),
                    'PAGSEGURO_TOKEN_PRODUCTION' => addslashes($_POST['PAGSEGURO_TOKEN_PRODUCTION']),
                    'PAGSEGURO_APP_ID_PRODUCTION' => addslashes($_POST['PAGSEGURO_APP_ID_PRODUCTION']),
                    'PAGSEGURO_APP_KEY_PRODUCTION' => addslashes($_POST['PAGSEGURO_APP_KEY_PRODUCTION']),
                    'PAGSEGURO_TOKEN_SANDBOX' => addslashes($_POST['PAGSEGURO_TOKEN_SANDBOX']),
                    'PAGSEGURO_APP_ID_SANDBOX' => addslashes($_POST['PAGSEGURO_APP_ID_SANDBOX']),
                    'PAGSEGURO_APP_KEY_SANDBOX' => addslashes($_POST['PAGSEGURO_APP_KEY_SANDBOX'])
                );

                $where = 'id="'.$dados->id.'"';

                if($core->update('configuracoes', $campos, $where)){
                    $core->msg('sucesso', 'Dados Alterados.');
                    header('Location: '.BASE.'configuracoes/');
                } else {
                    $core->msg('error', 'Erro na Alteração.');
                }
            }
        ?>
        
        <form id="formConfiguracoes" class="forms" action="" method="post" style="width: 90%; margin: 0 auto;">
            <div class="campos" style="width: 500px;">
                <label>Tipo de Integração:</label>
                <select id="pagseguro_env" name="pagseguro_env">
                    <option value="production" <?php echo ($dados->PAGSEGURO_ENV == 'production') ? 'selected' : '';  ?>>Produção</option>
                    <option value="sandbox" <?php echo ($dados->PAGSEGURO_ENV == 'sandbox') ? 'selected' : '';  ?>>Sandbox</option>
                </select>
            </div>

            <div class="campos" style="width: 500px;">
                <label>Pagseguro E-mail: </label>
                <input type="text" name="PAGSEGURO_EMAIL" value="<?php echo $dados->PAGSEGURO_EMAIL; ?>" />
            </div>

            <div class="campos" style="width: 500px;">
                <label>TOKEN (PRODUÇÃO): </label>
                <input type="text" name="PAGSEGURO_TOKEN_PRODUCTION" value="<?php echo $dados->PAGSEGURO_TOKEN_PRODUCTION; ?>" />
            </div>

            <div class="campos" style="width: 500px;">
                <label>APP ID (PRODUÇÃO): </label>
                <input type="text" name="PAGSEGURO_APP_ID_PRODUCTION" value="<?php echo $dados->PAGSEGURO_APP_ID_PRODUCTION; ?>" />
            </div>

            <div class="campos" style="width: 500px;">
                <label>APP KEY (PRODUÇÃO): </label>
                <input type="text" name="PAGSEGURO_APP_KEY_PRODUCTION" value="<?php echo $dados->PAGSEGURO_APP_KEY_PRODUCTION; ?>" />
            </div>

            <h3>MODO SANDBOX</h3>

            <div class="campos" style="width: 500px;">
                <label>TOKEN (SANDBOX): </label>
                <input type="text" name="PAGSEGURO_TOKEN_SANDBOX" value="<?php echo $dados->PAGSEGURO_TOKEN_SANDBOX; ?>" />
            </div>

            <div class="campos" style="width: 500px;">
                <label>APP ID (SANDBOX): </label>
                <input type="text" name="PAGSEGURO_APP_ID_SANDBOX" value="<?php echo $dados->PAGSEGURO_APP_ID_SANDBOX; ?>" />
            </div>

            <div class="campos" style="width: 500px;">
                <label>APP KEY (SANDBOX): </label>
                <input type="text" name="PAGSEGURO_APP_KEY_SANDBOX" value="<?php echo $dados->PAGSEGURO_APP_KEY_SANDBOX; ?>" />
            </div>
            
            <input type="submit" id="salvar" class="bts-form" name="salvar" value="Salvar" />
        </form>
    <?php break; ?>
<?php } ?>
