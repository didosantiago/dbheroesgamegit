<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<ul class="menu-sorteio">
    <li>
        <a href="<?php echo BASE; ?>portal">Ínicio</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>sorteios">Regras</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>doacao">Quero ser VIP</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>sorteios/realizados">Sorteios Realizados</a>
    </li>
</ul>

<?php switch($acao) {
    default: ?>
        <div class="regras">
            <h2>Regras do Sorteio</h2>
            
            <p style="color: #f72e2e;">- Somente membros vip poderão participar do sorteio.</p>
            
            <p>- O participante deverá estar com seu cadastro preenchido 100%, sendo Endereço para entrega do prêmio.</p>
            
            <p>- Cada usuário poderá participar apenas 1 vez de cada sorteio.</p>
            
            <p>- Após o sorteio será liberado para o vencedor um tela onde estará disponível o valor do frete que deverá ser pago pelo vencedor, e demais informações referente ao produto.</p>
            
            <p>- Caso o prazo para solicitação de retirada do prêmio expirar e o vencedor não solicitar, um novo sorteio será realizado.</p>
            
            <p>- Não será possível efetuar a troca do produto.</p>
            
            <p>- O prazo para postagem do prêmio é de até 5 dias úteis.</p>
            
            <p>- As imagens dos produtos sorteados são ilustrativas, não representando o produto real.</p>
            
            <h2 style="margin-top: 50px; text-align: center; font-size: 22px;">Informações do Sorteio em Andamento</h2>
            
            <?php if($sorteios->existeSorteioAtivo()){ ?>
                <?php 
                    $dadosSorteio = $core->getDados('adm_sorteios', "WHERE status = 1");
                    $dadosProduto = $core->getDados('adm_sorteios_produto', "WHERE id = ".$dadosSorteio->idProduto);
                ?>
            
                <div class="dados-sorteio">
                    <img src="<?php echo BASE; ?>assets/sorteios/<?php echo $dadosProduto->foto; ?>" />
                    <div class="info">
                        <h2><?php echo $dadosSorteio->titulo; ?></h2>
                        <span class="data">Data do Sorteio: <?php echo $core->dataTimeBR($dadosSorteio->data_sorteio); ?></span>
                        <div class="numero-participantes">
                            <h3>Participantes</h3>
                            <span><?php echo $sorteios->getTotalParticipantes($dadosSorteio->id); ?></span>
                        </div>
                    </div>
                </div>
            
                <?php if($sorteios->existeVencedor($dadosSorteio->id) != null){ ?>
                    <?php 
                        $dadosVencedor = $core->getDados('usuarios', "WHERE id = ".$dadosSorteio->vencedor);
                        $bilheteVencedor = $core->getDados('adm_sorteios_participantes', "WHERE idUsuario = ".$dadosSorteio->vencedor);
                    ?>
                    <div class="dados-vencedor">
                        <img src="<?php echo BASE.$dadosVencedor->foto; ?>" />
                        <div class="info">
                            <h2><?php echo $dadosVencedor->nome; ?></h2>
                            <span class="data">Data de Cadastro: <?php echo $core->dataBR($dadosVencedor->data_cadastro); ?></span>
                            <h2>VENCEDOR</h2>
                            <?php if($sorteios->souVencedor($dadosSorteio->id, $user->id)){ ?>
                                <div class="solicitar">
                                    <?php if(!$sorteios->existeSolicitacaoRetirada($dadosSorteio->id, $user->id)){ ?>
                                        <p>Parabéns você venceu, solicite a retirada de seu prêmio!</p>
                                        <a href="<?php echo BASE; ?>sorteios/retirar/<?php echo $dadosSorteio->id; ?>" class="solicitar-retirada">Solicitar Retirada</a>
                                    <?php } else { ?>
                                        <?php $dadosRetirada = $core->getDados('adm_sorteios_retiradas', "WHERE idSorteio = $dadosSorteio->id AND idUsuario = ".$user->id); ?>
                                        <p>Parabéns você venceu, e já solicitou a retirada agora é so aguardar!</p>
                                        <a href="javascript:void(0);" class="solicitar-retirada">Previsão <?php echo $core->dataBR($dadosRetirada->data_previsao); ?></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="bilhete-gerado">
                            <h3>Bilhete Premiado</h3>
                            <span><?php echo $bilheteVencedor->bilhete; ?></span>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php
                        $ticket = $sorteios->existeBilhete($user->id, $dadosSorteio->id);
                        if($ticket == null){ 
                    ?>
                        <a href="<?php echo BASE; ?>sorteios/participar" class="bt-invasor">Adquirir Bilhete</a>
                    <?php } else { ?>
                        <div class="bilhete-gerado">
                            <h3>Meu Bilhete</h3>
                            <h4>Data do Sorteio : <?php echo $core->dataTimeBR($dadosSorteio->data_sorteio); ?></h4>
                            <span><?php echo $ticket->bilhete; ?></span>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <span class="nenhum-sorteio">Não há sorteios disponíveis no momento, aguarde!</span>
            <?php } ?>
        </div>
    <?php break; ?>

    <?php case 'participar': ?>

        <?php 
            if($sorteios->existeSorteioAtivo()){
                $dadosSorteio = $core->getDados('adm_sorteios', "WHERE status = 1");
            }
            
            $sorteioValidado = 1;
                
            if($user->vip == 0){
                $sorteioValidado = 0;
                $core->msg('error', 'Somente jogadores vip podem participar do Sorteio.');
                header('Location: '.BASE.'doacao');
            } else {
                if(!$user->validaCamposCadastro($user->id)){
                    $sorteioValidado = 0;
                    $core->msg('error', 'Para participar do sorteio complete seu endereço.');
                    header('Location: '.BASE.'perfil');
                } else {
                    if($sorteios->validaParticipacao($user->id, $dadosSorteio->id)){
                        $sorteioValidado = 0;
                        $core->msg('error', 'Você ja está participando do sorteio.');
                        header('Location: '.BASE.'sorteios');
                    } else {
                        if(!$sorteios->existeSorteioAtivo()){
                            $sorteioValidado = 0;
                            $core->msg('error', 'Não existe nenhum sorteio Ativo');
                            header('Location: '.BASE.'sorteios');
                        }
                    }
                }
            }
            
            if($sorteioValidado == 1){
                $ticket = $sorteios->geraBilhete();
                $sorteio_ativo = $core->getDados('adm_sorteios', "WHERE status = 1");

                $campos = array(
                    'idSorteio' => $sorteio_ativo->id,
                    'idUsuario' => $user->id,
                    'bilhete' => $ticket
                );

                if($core->insert('adm_sorteios_participantes', $campos)){
                    $core->msg('sucesso', 'Bilhete Gerado com Sucesso');
                    header('Location: '.BASE.'sorteios');
                }
            }
        ?>
    <?php break; ?>

    <?php case 'realizados': ?>
         <div class="regras">
            <h2>Sorteios Realizados</h2>
            
            <ul class="lista-sorteios">
                <?php $sorteios->getList($pc, 10); ?>
            </ul>
         </div>
    <?php break; ?>

    <?php case 'retirar': ?>
        <?php 
            $id = Url::getURL(2);
            $dadosSorteio = $core->getDados('adm_sorteios', "WHERE id = $id");
            $dadosProduto = $core->getDados('adm_sorteios_produto', "WHERE id = ".$dadosSorteio->idProduto);
            
            if($sorteios->existeSolicitacaoRetirada($dadosSorteio->id, $user->id)){
                $core->msg('error', 'Você já solicitou a retirada, aguarde a entrega.');
                header('Location: '.BASE.'sorteios');
            }
                    
            if(isset($_POST['observacoes'])){
                $campos = array(
                    'idSorteio' => $id,
                    'idUsuario' => $user->id,
                    'observacoes' => addslashes($_POST['observacoes']),
                    'data' => date('Y-m-d H:i:s'),
                    'data_previsao' => date('Y-m-d', strtotime(date('Y-m-d'). ' + 12 days'))
                );

                if($core->insert('adm_sorteios_retiradas', $campos)){
                    $core->msg('sucesso', 'Solicitação de retirada enviada');
                    header('Location: '.BASE.'sorteios');
                }
            }
        ?>

        <div class="regras">
            <h2>Informações para Retirada</h2>
            
            <div class="leia">
                <h3>Leia com Atenção!</h3>
                
                <p>
                    <?php echo $dadosProduto->observacoes; ?>
                </p>
                
                <div class="valor-frete">
                    <h4>Valor do Frete</h4>
                    <span><?php echo $core->formataMoeda($dadosProduto->frete); ?></span>
                </div>
                
            </div>
            
            <form id="formRetirada" action="" method="post">
                <textarea name="observacoes" rows="20" placeholder="Informe aqui os dados solicitados" required></textarea>
                <button type="submit" id="btnRetira">Confirmar</button>
            </form>
         </div>
    <?php break; ?>
<?php } ?>