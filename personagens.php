<?php switch($acao) {
    default: ?>
    <h2 class="title">Administrar Guerreiros</h2>
    
    <ul class="lista-guerreiros">
        <?php $administrar->getListGuerreiros(); ?>
    </ul>
    <?php break; ?>
        
    <?php case 'edit': ?>
        <?php 
            $idGuerreiro = Url::getURL(2);
            $dados = $core->getDados('personagens', 'WHERE id = '.$idGuerreiro);

            if(isset($_REQUEST['salvar'])){
                $campos = array(
                    'nome' => addslashes($_POST['nome']),
                    'raca' => addslashes($_POST['raca']),
                    'hp' => addslashes($_POST['hp']),
                    'mana' => addslashes($_POST['mana']),
                    'energia' => addslashes($_POST['energia']),
                    'liberado' => addslashes($_POST['liberado'])
                );

                $where = 'id="'.$idGuerreiro.'"';

                if($core->update('personagens', $campos, $where)){
                    $core->msg('sucesso', 'Guerreiro Alterado.');
                    header('Location: '.BASE.'personagens/');
                } else {
                    $core->msg('error', 'Ocorreu um Erro ao efetuar Alteração.');
                }
            }
        ?>
        
        <h2 class="title">Editando Personagem</h2>
        
        <form id="formPersonagens" class="forms" action="" method="post">
            <div class="campos block" style="width: 300px;">
                <img src="<?php echo BASE.'assets/cards/'.$dados->foto ?>" alt="<?php echo $dados->nome; ?>" />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Nome: </label>
                <input type="text" name="nome" value="<?php echo $dados->nome; ?>" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Raça: </label>
                <select name="raca" required>
                    <option value="Humano" <?php $core->isNewSelected($dados->raca, 'Humano'); ?>>Humano</option>
                    <option value="Sayajin" <?php $core->isNewSelected($dados->raca, 'Sayajin'); ?>>Sayajin</option>
                    <option value="Namekuseijins" <?php $core->isNewSelected($dados->raca, 'Namekuseijins'); ?>>Namekuseijins</option>
                    <option value="Changeller" <?php $core->isNewSelected($dados->raca, 'Changeller'); ?>>Changeller</option>
                    <option value="Bio Androide" <?php $core->isNewSelected($dados->raca, 'Bio Androide'); ?>>Bio Androide</option>
                    <option value="Majin" <?php $core->isNewSelected($dados->raca, 'Majin'); ?>>Majin</option>
                </select>
            </div>
            <div class="campos block" style="width: 300px;">
                <label>HP: </label>
                <input type="text" name="hp" value="<?php echo $dados->hp; ?>" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>KI: </label>
                <input type="text" name="mana" value="<?php echo $dados->mana; ?>" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Energia: </label>
                <input type="text" name="energia" value="<?php echo $dados->energia; ?>" required />
            </div>
            <div class="campos block check" style="width: 300px">
                <label for="liberado">
                    <input type="checkbox" id="liberado" name="liberado" <?php $core->isNewChecked($dados->liberado, '1'); ?> value="1" />
                    Aparecer no Sistema?
                </label>
            </div>

            <input type="submit" id="salvar" class="bts-form" name="salvar" value="Salvar" />
        </form>
    <?php break; ?>
        
    <?php case 'add': ?>
        <?php 
            if(isset($_REQUEST['cadastrar'])){
                
                $foto = '';
                
                if($_FILES['foto']['error'] != 4){
                    $foto = $_FILES['foto'];
                    
                    $upload = new Upload($foto, 1000, 1000, "assets/cards/");
                    $retorno = $upload->salvar();
                }
                
                $campos = array(
                    'nome' => addslashes($_POST['nome']),
                    'raca' => addslashes($_POST['raca']),
                    'hp' => addslashes($_POST['hp']),
                    'mana' => addslashes($_POST['mana']),
                    'energia' => addslashes($_POST['energia']),
                    'liberado' => addslashes($_POST['liberado']),
                    'foto' => $retorno
                );

                if($core->insert('personagens', $campos)){
                    $core->msg('sucesso', 'Guerreiro Adicionado.');
                    header('Location: '.BASE.'personagens/');
                } else {
                    $core->msg('error', 'Ocorreu um Erro ao efetuar Alteração.');
                }
            }
        ?>
        
        <h2 class="title">Adicionar Personagem</h2>
        
        <form id="formPersonagens" class="forms" action="" method="post" enctype="multipart/form-data">
            <div class="campos block" style="width: 300px;">
                <label>Foto: </label>
                <input type="file" name="foto" value="" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Nome: </label>
                <input type="text" name="nome" value="" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Raça: </label>
                <select name="raca" required>
                    <option value="Humano">Humano</option>
                    <option value="Sayajin">Sayajin</option>
                    <option value="Namekuseijins">Namekuseijins</option>
                    <option value="Changeller">Changeller</option>
                    <option value="Bio Androide">Bio Androide</option>
                    <option value="Majin">Majin</option>
                </select>
            </div>
            <div class="campos block" style="width: 300px;">
                <label>HP: </label>
                <input type="text" name="hp" value="" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>KI: </label>
                <input type="text" name="mana" value="" required />
            </div>
            <div class="campos block" style="width: 300px;">
                <label>Energia: </label>
                <input type="text" name="energia" value="" required />
            </div>
            <div class="campos block check" style="width: 300px">
                <label for="liberado">
                    <input type="checkbox" id="liberado" name="liberado" value="1" />
                    Aparecer no Sistema?
                </label>
            </div>

            <input type="submit" id="salvar" class="bts-form" name="cadastrar" value="Cadastrar" />
        </form>
    <?php break; ?>
<?php } ?>