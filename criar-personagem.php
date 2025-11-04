<?php 
    if(isset($_POST['criar'])){
        if(!$personagem->esgotado($user->id)){
            $nomeGuerreiro = str_replace(" ","",addslashes($_POST['nomeGuerreiro']));
            
            if(!$personagem->nomeGuerreiroExists($nomeGuerreiro)){
                $dados = $personagem->getInfoPersonagem(addslashes($_POST['idPersonagem']));
                
                // Get photo directly from personagens table
                $select_foto = $core->getDados('personagens', "WHERE id=".addslashes($_POST['idPersonagem']));

                $campos = array(
                    'idPersonagem' => addslashes($_POST['idPersonagem']),
                    'idPlaneta' => addslashes($_POST['idPlaneta']),
                    'idUsuario' => addslashes($_POST['idUsuario']),
                    'data_cadastro' => date('Y-m-d'),
                    'nome' => $nomeGuerreiro,
                    'foto' => $select_foto->foto,
                    'hp' => 150,
                    'gold' => 1000
                );

                if($core->filtrarPalavrasOfensivas($nomeGuerreiro)){
                    if($core->insert('usuarios_personagens', $campos)){
                        // Fixed: Properly interpolate user ID
                        $userId = intval($user->id);
                        $sql = "SELECT * FROM usuarios_personagens WHERE idUsuario = {$userId} ORDER BY id DESC LIMIT 1";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $item = $stmt->fetch();

                        // Fixed: Properly interpolate personagem ID
                        $personagemId = intval($item->idPersonagem);
                        $sql = "SELECT * FROM personagens WHERE id = {$personagemId}";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $personagem_principal = $stmt->fetch();

                        $campos_treino = array(
                            'idPersonagem' => $item->id,
                        );

                        $core->insert('personagens_treino', $campos_treino);

                        $core->msg('sucesso', 'Personagem Criado.');
                        
                        $campos_golpe = array(
                            'idPersonagem' => $item->id,
                            'idGolpe' => 4
                        );

                        $core->insert('personagens_golpes', $campos_golpe);
                        
                        $campos_golpe2 = array(
                            'idPersonagem' => $item->id,
                            'idGolpe' => 21
                        );

                        $core->insert('personagens_golpes', $campos_golpe2);

                        $core->msg('sucesso', 'Personagem Criado.');
                        
                        header('Location: '.BASE.'portal/');
                    } else {
                        header('Location: '.BASE.'criar-personagem');
                        $core->msg('error', 'Ocorreu um Erro.');
                    }
                } else {
                    header('Location: '.BASE.'criar-personagem');
                    $core->msg('error', 'Não é permitido palavras ofensivas ou bloqueadas.');
                }
            } else {
                header('Location: '.BASE.'criar-personagem');
                $core->msg('error', 'Já existe um Guerreiro com este Nome.');
            }
        } else {
            header('Location: '.BASE.'portal');
            $core->msg('error', 'Quantidade de Guerreiros esgotada.');
        }
    }
?>

<form id="formPersonagem" class="forms" action="" method="post">
    <input type="hidden" name="idUsuario" value="<?php echo $user->id; ?>" />
    <input type="hidden" name="foto" id="fotoPersonagem" value="" />
    
    <div id="wizard-personagem">
        <ul>
            <li class="lk-etapa-1 active"><a href="#etapa-1">Escolha o Personagem<br /><small>Selecione seu Preferido</small></a></li>
            <li class="lk-etapa-2"><a href="#etapa-2">Preencha as Informações<br /><small>Digite um nome e escolha um Planeta</small></a></li>
            <li class="lk-etapa-3"><a href="#etapa-3">Finalização<br /><small>Seu Guerreiro foi criado</small></a></li>
        </ul>
        
        <div class="contents-wizard">
            <div id="load-wizard">
                <img src="<?php echo BASE; ?>assets/load.gif" alt="Carregando..." />
            </div>
            
            <div id="etapa-1">
                <?php
                    // --- add this guard before calling getList() ---
                    if (!is_object($personagem) || !method_exists($personagem, 'getList')) {
                        $personagem = new Personagens();
                    }
                    ?>
                <?php $personagem->getList(); ?>
                
                <div class="footer-bottom">
                    <button type="button" class="btn-step-1 bts-form">Continuar <i class="fas fa-forward"></i></button>
                </div>
            </div>

            <div id="etapa-2">
                <div class="area-nome">
                    <h2 class="title">Qual será o nome de seu Guerreiro?</h2>
                    <p>Obs. Lembrando que não é permitido nome com palavras ofensivas.</p>
                    <input type="text" name="nomeGuerreiro" id="nomeGuerreiro" placeholder="Digite o Nome" required />
                </div>

                <div class="area-planeta">
                    <h2 class="title">De qual planeta?</h2>
                    <p>O planeta é onde seu Guerreiro Habita</p>
                    <?php $personagem->getPlanetas(); ?>
                </div>
                
                <div class="footer-bottom">
                    <button type="button" class="btn-step-2 bts-form">Próximo Passo <i class="fas fa-forward"></i></button>
                </div>
            </div>

            <div id="etapa-3">
                <img class="img-success" src="<?php echo BASE; ?>assets/success.png" alt="Sucesso" />
                <h4>Parabéns, clique no botão abaixo para concluir e Iniciar o Jogo</h4>
                <input type="hidden" name="criar" value=""/>
                <button type="submit" id="criar" class="bts-form">Começar o Jogo <i class="fas fa-play"></i></button>
            </div>
        </div>
    </div>
</form>