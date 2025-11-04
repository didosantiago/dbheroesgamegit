<?php 
    if(isset($_POST['salvar'])){
        $idPersonagem = $_SESSION['PERSONAGEMID'];
        
        if(isset($_POST['golpe'])){
            foreach(addslashes($_POST['golpe']) as $value){
                $dadosG = $core->getDados('personagens_golpes', "WHERE idPersonagem = '.$idPersonagem.' AND idGolpe != ".$value);
                
                if($dadosG->idGolpe != 4){
                    $core->delete('personagens_golpes', 'idPersonagem = '.$idPersonagem.' AND idGolpe = '.$dadosG->idGolpe);
                }
                
                if(!$batalha->getGolpeExiste($value, $_SESSION['PERSONAGEMID'])){
                    $campos = array(
                        'idGolpe' => $value,
                        'idPersonagem' => $_SESSION['PERSONAGEMID']
                    );

                    $core->insert('personagens_golpes', $campos);
                }
            }
        }
    }
?>

<h2 class="title">Escolha aqui os golpes que irá usar nas batalhas</h2>

<ul class="lista-golpes">
    <form method="post">
        <p class="informativo">
            - Escolha aqui os golpes que irá usar nas batalhas, selecione o Golpe desejado e clique em salvar! 
            <input type="submit" name="salvar" style="float: right; background: #29b217;" class="bts-form" value="Salvar" />
        </p>
        <div style="clear: both;"></div>
        <?php $batalha->getListaGolpes($personagem->mana, $personagem->nivel); ?>
    </form>
</ul>