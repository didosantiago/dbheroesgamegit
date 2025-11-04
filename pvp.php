<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
    
    $idPersonagem = $_SESSION['PERSONAGEMID'];
    $personagem->getGuerreiro($idPersonagem);
    
    if(isset($_POST['encontrar'])){
        $planeta = addslashes($_POST['planeta']);
        $nome = addslashes($_POST['nome']);
        $id_oponente = $personagem->getByName($nome, $planeta);
        
        if($personagem->gold >= 10){
            if(!$batalha->getAtacouRecente($idPersonagem, $id_oponente)){
                if($personagem->nome != addslashes($_POST['nome'])){
                    if($id_oponente != 0){
                        $campos = array(
                            'gold' => $personagem->gold - 10
                        );

                        $where = 'id="'.$idPersonagem.'"';

                        if($core->update('usuarios_personagens', $campos, $where)){
                            header('Location: '.BASE.'publico/'.$id_oponente);
                        }
                    } else {
                        echo "<script type='text/javascript'>
                                    swal({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Nenhum Guerreiro Encontrado!'
                                    })
                                  </script>";
                    }
                } else {
                    echo "<script type='text/javascript'>
                                    swal({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Você não pode lutar contra seu próprio Guerreiro!'
                                    })
                                  </script>";
                }
            } else {
                echo "<script type='text/javascript'>
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'Aguarde a penalidade terminar para atacar novamente!'
                                })
                              </script>";
            }
        } else {
            echo "<script type='text/javascript'>
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Gold Insuficiente para Busca!'
                    })
                  </script>";
        }
    }
    
    if(isset($_POST['encontrar_aleatorio'])){
        $planeta = addslashes($_POST['planeta']);
        $tipo = addslashes($_POST['tipo']);
        $id_oponente = $personagem->getAleatorio($tipo, $planeta, $personagem->nivel, $personagem->id, $user->id);
        
        if($personagem->gold >= 10){
            if(!isset($_SESSION['pvp'])){
                if($id_oponente != 0){
                    $campos = array(
                        'gold' => $personagem->gold - 10
                    );

                    $where = 'id="'.$idPersonagem.'"';

                    if($core->update('usuarios_personagens', $campos, $where)){
                        header('Location: '.BASE.'publico/'.$id_oponente);
                    }
                } else {
                    echo "<script type='text/javascript'>
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'Nenhum Guerreiro Encontrado!'
                                })
                              </script>";
                }
            } else {
                echo "<script type='text/javascript'>
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'Aguarde a penalidade terminar para atacar novamente!'
                                })
                              </script>";
            }
        } else {
            echo "<script type='text/javascript'>
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Gold Insuficiente para Busca!'
                    })
                  </script>";
        }
    }
?>

<h2>PVP Global</h2>

<div class="caca-pvp">
    <img src="<?php echo BASE; ?>assets/img-busca.png" alt="Busca Global" />
</div>

<div id="buscaGlobal">
    <form id="buscaNome" class="forms" method="post">
        <h3>(Player vs Player) Inimigo por nome</h3>
        <p class="info">
            Para encontrar inimigos pelo nome será cobrado um valor de <strong>10 Gold</strong>.
        </p>
        <div class="campos">
            <label>Escolha em qual Planeta deseja encontrar</label>
            <select name="planeta">
                <?php if($user->vip == 1){ ?>
                    <option value="4">Todos Planetas</option>
                <?php } ?>
                <option value="1">Terra</option>
                <option value="2">Vegeta</option>
                <option value="3">Namekusei</option>
            </select>
        </div>
        <div class="campos">
            <label>Nome do Guerreiro</label>
            <input type="text" name="nome" value="" placeholder="Digite o nome aqui..." />
        </div>
        <?php if($batalha->existsPunicao($idPersonagem)){ ?>
            <input type="submit" name="encontrar" disabled value="Encontrar" class="bts-form disabled" />
        <?php } else { ?>
            <input type="submit" name="encontrar" value="Encontrar" class="bts-form" />
        <?php } ?>
        
    </form>
    <form id="buscaAleatoria" class="forms" method="post">
        <h3>(Player vs Player) Inimigo Aleatório</h3>
        <p class="info">
            Para encontrar inimigos aleatórios será cobrado um valor de <strong>10 Gold</strong>.
        </p>
        <div class="campos">
            <label>Escolha em qual Planeta deseja encontrar</label>
            <select name="planeta">
                <?php if($user->vip == 1){ ?>
                    <option value="4">Todos Planetas</option>
                <?php } ?>
                <option value="1">Terra</option>
                <option value="2">Vegeta</option>
                <option value="3">Namekusei</option>
            </select>
        </div>
        <div class="campos">
            <label>Escolhar o Nível</label>
            <select name="tipo">
                <option value="2">Aleatório (Todos acima de Level 10)</option>
                <option value="1">Mesmo Level que meu Guerreiro</option>
            </select>
        </div>
        
        <?php if($batalha->existsPunicao($idPersonagem)){ ?>
            <input type="submit" name="encontrar_aleatorio" disabled value="Encontrar" class="bts-form disabled" />
        <?php } else { ?>
            <input type="submit" name="encontrar_aleatorio" value="Encontrar" class="bts-form" />
        <?php } ?>
    </form>
</div>