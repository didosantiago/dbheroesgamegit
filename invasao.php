<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    $dadosInvasor = $invasao->getInvasaoSemanal();
    $porcentagem_hp_boss = $treino->getPorcentagemHP($dadosInvasor->hp_total, $dadosInvasor->hp_usado);
    $porcentagem_ki_boss = $treino->getPorcentagemKI($dadosInvasor->ki, $dadosInvasor->ki_usado);
?>

<ul class="menu-invasao">
    <li>
        <a href="<?php echo BASE; ?>portal">Ínicio</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>invasao">Regras</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>invasao/boss">Invasor (Boss)</a>
    </li>
    <li class="hp">
        <strong>Meu HP </strong>
        <div class="meter animate red">
            <?php 
                if($personagem->nivel > 1){
                    $nivel_hp = 150 + ((intval($personagem->nivel) - 1) * 50);
                } else {
                    $nivel_hp = 150;
                }

                $porcentagem_hp = $treino->getPorcentagemHP($nivel_hp, $nivel_hp - $personagem->hp);
            ?>
            <em><?php echo $personagem->hp; ?> / <strong><?php echo $nivel_hp; ?></strong></em>
            <span style="width: <?php echo $porcentagem_hp; ?>%"><span></span></span>
        </div>
    </li>
    <li class="ki">
        <strong>Meu KI </strong>
        <div class="meter animate blue">
            <em><?php echo $personagem->mana - $personagem->ki_usado; ?> / <strong><?php echo $personagem->mana; ?></strong></em>
            <?php 
                $porcentagem_ki = $treino->getPorcentagemKI($personagem->mana, $personagem->ki_usado);
            ?>
            <span style="width: <?php echo $porcentagem_ki; ?>%"><span></span></span>
        </div>
    </li>
    <li class="energia">
        <strong>Minha Energia </strong>
        <div class="meter animate">
            <em><?php echo $personagem->energia - $personagem->energia_usada; ?> / <strong><?php echo $personagem->energia; ?></strong></em>
            <?php 
                $porcentagem_energia = $treino->getPorcentagemEnergia($personagem->energia, $personagem->energia_usada);
            ?>
            <span style="width: <?php echo $porcentagem_energia; ?>%"><span></span></span>
        </div>
    </li>
</ul>

<?php switch($acao) {
    default: ?>
        <div class="regras">
            <h2>Regras do Invasor</h2>
            
            <p>- O invasor pode ser atacado somente 1 vez a cada 10 minutos.</p>
            
            <p>- O poder ofensivo do invasor (atributos) é sempre 50% maior do que os atributos do Guerreiro atacante. Isto significa que o mesmo invasor terá atributos diferentes para cada
            jogador que o atacar, mas sempre seguindo o valor já informado.</p>
            
            <p>- O Nome do Guerreiro do vencedor será exibibo para todos jogadores.</p>
            
            <p>- Somente o vencedor irá receber as recompensas descritas na tela do invasor.</p>
            
            <p>- Após derrotado o invasor não poderá mais ser atacado e o evento será finalizado.</p>
            
            <p>- Os eventos de invasão acontecerão semanalmente.</p>
            
            <p>- Caso exista nas recompensas uma Foto de Perfil, a mesma será exclusiva, somente o vencedor terá aquela foto no jogo.</p>
            
            <p>- O vencedor também tera uma chance de dropar um selo exclusivo do evento.</p>
            
            <p>- Caso a foto de recompensa não seja de seu guerreiro atual, quando houver a troca de guerreiro a foto estará disponível.</p>
            
            <a href="<?php echo BASE; ?>invasao/boss" class="bt-invasor">Entrar no Evento</a>
        </div>
    <?php break; ?>

    <?php case 'boss': ?>
        <?php 
            if($personagem->time_invasao == 0){
                if($dadosInvasor && $dadosInvasor->id && !$invasao->getBatalhaRunning($dadosInvasor->id, $_SESSION['PERSONAGEMID'])){

                    $campos = array(
                        'idInvasao' => $dadosInvasor->id,
                        'idPersonagem' => $_SESSION['PERSONAGEMID'],
                        'idUsuario' => $user->id,
                        'hp_boss_inicial' => $dadosInvasor->hp_total,
                        'hp_boss_atual' => $dadosInvasor->hp_total,
                        'dano_total' => 0,
                        'tempo_inicio' => time(),
                        'tempo_fim' => time() + (10 * 60),
                        'concluida' => 0,
                        'finalizado' => 0
                    );

                    $core->insert('adm_invasao_batalhas', $campos);
                }
            }
                
            if($invasao->getBatalhaRunning($dadosInvasor->id, $_SESSION['PERSONAGEMID'])){
                if($personagem->time_invasao < time()){
                    $time_atual = time();

                    if($invasao->getExistsLastBatalha($dadosInvasor->id, $_SESSION['PERSONAGEMID'])){
                        $dadosBatalha = $core->getDados('adm_invasao_batalhas', "WHERE idInvasao = $dadosInvasor->id AND idPersonagem = ".$_SESSION['PERSONAGEMID']." ORDER BY id DESC LIMIT 1");
                        $dadosAtaqueInvasor = $core->getDados('adm_invasao_ataques', "WHERE idBatalha = ".$dadosBatalha->id." ORDER BY id DESC LIMIT 1");
                        $tempo = $dadosAtaqueInvasor->time_ataque;
                    }

                    $dadosBatalha = $invasao->getInfoBatalhaRunning($dadosInvasor->id, $_SESSION['PERSONAGEMID']);

                    if($invasao->verificaDanoHP($personagem->hp)){
                        if($invasao->getExistsAtaque($dadosBatalha->id)){
                            $dadosAtaqueInvasor = $core->getDados('adm_invasao_ataques', "WHERE idBatalha = ".$dadosBatalha->id." ORDER BY id DESC LIMIT 1");
                            $tempo = $dadosAtaqueInvasor->time_ataque;
                        } else {
                            $tempo = 0;
                        }
                    } else {
                        $campos = array(
                            'finalizado' => 1
                        );

                        $where = 'id="'.$dadosBatalha->id.'"';

                        $core->update('adm_invasao_batalhas', $campos, $where);
                        
                        if($config->teste == 1){
                            $campos = array(
                                'time_invasao' => time() + 10
                            );
                        } else {
                            $campos = array(
                                'time_invasao' => time() + 600
                            );
                        }

                        $where = 'id = '.$_SESSION['PERSONAGEMID'];

                        $core->update('usuarios_personagens', $campos, $where);
                    }

                    $tempoRestante = $tempo - $time_atual;
                }
            } else {
                if($personagem->time_invasao < time()){
                    $campos = array(
                        'idInvasao' => $dadosInvasor->id,
                        'idPersonagem' => $_SESSION['PERSONAGEMID'],
                        'idUsuario' => $user->id,
                        'hp_boss_inicial' => $dadosInvasor->hp_total,
                        'hp_boss_atual' => $dadosInvasor->hp_total,
                        'dano_total' => 0,
                        'tempo_inicio' => time(),
                        'tempo_fim' => time() + (10 * 60),
                        'concluida' => 0,
                        'finalizado' => 0
                    );
                }
                
                $tempoRestante = $personagem->time_invasao - time();
            }
            
            $dadosBatalha = $core->getDados('adm_invasao_batalhas', "WHERE idInvasao = $dadosInvasor->id AND idPersonagem = ".$_SESSION['PERSONAGEMID']." ORDER BY id DESC LIMIT 1");
            
            if($dadosInvasor->vencedor != null && $dadosInvasor->vencedor == $_SESSION['PERSONAGEMID']){
                if($core->isExists('usuarios_personagens_fotos', "WHERE visualizado = 0 AND idPersonagem = ".$_SESSION['PERSONAGEMID'])){
                    header('Location: '.BASE.'minhas-fotos');
                }
            }
        ?>
        <div class="invasor">
            <img src="<?php echo BASE.'assets/boss/'.$dadosInvasor->imagem; ?>" alt="<?php echo $dadosInvasor->nome; ?>" />

            <?php if(!$invasao->getDerrotado($dadosInvasor->id)){ ?>
                <div class="boss-atributo hp">
                    <strong>HP </strong>
                    <div class="meter animate red">
                        <em><?php echo $dadosInvasor->hp_total - $dadosInvasor->hp_usado; ?> / <strong><?php echo $dadosInvasor->hp_total; ?></strong></em>
                        <span style="width: <?php echo $porcentagem_hp_boss; ?>%"><span></span></span>
                    </div>
                </div>
                <div class="boss-atributo ki">
                    <strong>KI </strong>
                    <div class="meter animate blue">
                        <em><?php echo $dadosInvasor->ki - $dadosInvasor->ki_usado; ?> / <strong><?php echo $dadosInvasor->ki; ?></strong></em>
                        <span style="width: <?php echo $porcentagem_ki_boss; ?>%"><span></span></span>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="log-ataques">
            <ul>
                <?php echo $invasao->getLogInvasao($dadosInvasor->id); ?>
            </ul>
        </div>

        <div class="insavao-info">
            <div class="bonus">
                <h2>O Vencedor receberá as recompensas abaixo</h2>

                <?php echo $invasao->getRecompensas($dadosInvasor->id); ?>
            </div>

            <?php if(!$invasao->getDerrotado($dadosInvasor->id)){ ?>
                <div class="meus-ataques">
                    <h2>Meus Golpes</h2>

                    <?php 
                        $esgotado = 0;
                        
                        if(!$invasao->verificaDanoHP($personagem->hp)){
                            $esgotado = 1;
                        }
                        
                        if($esgotado == 0){ 
                    ?>
                        <ul>
                            <input type="hidden" id="idInvasor" value="<?php echo $dadosInvasor->id; ?>" />
                            <input type="hidden" id="idBatalha" value="<?php echo $dadosBatalha->id; ?>" />
                            
                            <?php echo $invasao->getAtaques($personagem->graduacao_id, $personagem->mana, $personagem->nivel, $personagem->id); ?>
                        </ul>
                    <?php } else { ?>
                        <div class="time-restante">
                             <h4>Seu HP esgotou, aguarde 10 minutos para atacar novamente</h4>
                             <div class="cont">
                                <div class="horas">
                                    <span></span>
                                    <p>Horas</p>
                                </div>
                                <div class="sep">:</div>
                                <div class="minutos">
                                    <span></span>
                                    <p>Minutos</p>
                                </div>
                                <div class="sep">:</div>
                                <div class="segundos">
                                    <span></span>
                                    <p>Segundos</p>
                                </div>
                             </div>
                        </div>
                    <?php } ?>

                    <script type="text/javascript">
                        $(document).ready(function(){
                            // ✅ FIXED: Changed 'dataid' to 'data-id'
                            $('.invasao .meus-ataques .bt-atacar').on('click', function(e){
                                e.preventDefault();
                                
                                console.log('Attack button clicked!'); // Debug
                                
                                if(!$(this).hasClass('inativo')){
                                    var idBatalha = $('#idBatalha').val();
                                    var idInvasor = $('#idInvasor').val();
                                    var idPersonagem = $('#personagemLogged').val();
                                    var idGolpe = $(this).attr('data-id'); // ✅ FIXED: was 'dataid'

                                    console.log('Battle data:', {
                                        idBatalha: idBatalha,
                                        idInvasor: idInvasor,
                                        idPersonagem: idPersonagem,
                                        idGolpe: idGolpe
                                    }); // Debug

                                    var data_string = 'idPersonagem=' + idPersonagem + '&idInvasor=' + idInvasor + '&idBatalha=' + idBatalha + '&idGolpe=' + idGolpe;

                                    $.ajax({
                                        type: 'POST',
                                        url: "<?php echo BASE; ?>ajax/ajaxInvasao.php",
                                        data: data_string,
                                        success: function (res) {
                                            console.log('Attack successful!', res); // Debug
                                            window.location.href = "<?php echo BASE; ?>invasao/boss";
                                        },
                                        error: function(xhr, status, error){
                                            console.error('AJAX Error:', error); // Debug
                                            alert('Erro ao atacar: ' + error);
                                        }
                                    });
                                } else {
                                    console.log('Button is inactive'); // Debug
                                }
                                
                                return false;
                            });
                        });

                        startCountdown(<?php echo $tempoRestante; ?>);

                        function startCountdown(tempo){
                            if((tempo - 1) >= 0){

                                var min = parseInt(tempo/60);
                                var horas = parseInt(min/60);
                                min = min % 60;
                                var seg = tempo%60;

                                if(min < 10){
                                    min = "0"+min;
                                    min = min.substr(0, 2);
                                }

                                if(seg <=9){
                                    seg = "0"+seg;
                                }

                                if(horas <=9){
                                    horas = "0" + horas;
                                }

                                horaImprimivel = horas + ':' + min + ':' + seg;

                                $(".time-restante .cont .horas span").html(horas);
                                $(".time-restante .cont .minutos span").html(min);
                                $(".time-restante .cont .segundos span").html(seg);

                                setTimeout(function(){ 
                                    startCountdown(tempo);
                                }, 1000);

                                tempo --;
                            } else {
                                if($('.time-restante').length > 0){
                                    location.reload(true);
                                }
                            }
                        }
                    </script>
                </div>
            
                <div class="meu-log">
                    <h2>Meu Log</h2>
                    <ul>
                        <?php echo $invasao->getMeuLog($dadosInvasor->id, $_SESSION['PERSONAGEMID']); ?>
                    </ul>
                </div>
            <?php } else { ?>
                <div class="invasor-derrotado">
                    <h2>Invasor foi derrotado por</h2>
                    <div class="vencedor">
                        <?php if($dadosInvasor->vencedor != null){ ?>
                            <a href="<?php echo BASE; ?>publico/<?php echo $dadosInvasor->vencedor; ?>">
                                <?php  
                                    $dadosVencedor = $core->getDados('usuarios_personagens', "WHERE id = ".$dadosInvasor->vencedor);
                                ?>
                                <img src="<?php echo BASE.'assets/cards/'.$dadosVencedor->foto; ?>" alt="<?php echo $dadosVencedor->nome; ?>" />
                                <h3><?php echo $dadosVencedor->nome; ?></h3>
                                <span class="level">Level: <strong><?php echo $dadosVencedor->nivel; ?></strong></span>
                            </a>
                        <?php } else { ?>
                            <p>Nenhum vencedor ainda!</p>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php break; ?>
<?php } ?>
