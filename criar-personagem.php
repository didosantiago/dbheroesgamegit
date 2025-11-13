<?php 
if(isset($_POST['criar'])){
    if(!$personagem->esgotado($user->id)){
        $nomeGuerreiro = str_replace(" ","",addslashes($_POST['nomeGuerreiro']));
        
        // ✅ VALIDATE: Check if planeta was selected
        if(empty($_POST['idPlaneta'])){
            $_SESSION['error_message'] = 'Você precisa selecionar um planeta!';
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        } else if(!$personagem->nomeGuerreiroExists($nomeGuerreiro)){
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
                    // Get newly created character
                    $userId = intval($user->id);
                    $sql = "SELECT * FROM usuarios_personagens WHERE idUsuario = {$userId} ORDER BY id DESC LIMIT 1";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();
                    $item = $stmt->fetch();

                    // ✅ CREATE ALL INVENTORY SLOTS
                    try {
                        $success = $personagem->createInventorySlots($item->id);
                        if ($success) {
                            error_log("✅ Inventory slots created for character ID: " . $item->id);
                        }
                    } catch (Exception $e) {
                        error_log("❌ Inventory slots creation error: " . $e->getMessage());
                    }

                    // Get personagem base data
                    $personagemId = intval($item->idPersonagem);
                    $sql = "SELECT * FROM personagens WHERE id = {$personagemId}";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();
                    $personagem_principal = $stmt->fetch();

                    // Create training record
                    $campos_treino = array('idPersonagem' => $item->id);
                    $core->insert('personagens_treino', $campos_treino);

                    // Give Soco (ID 1) - Basic punch
                    $campos_golpe = array(
                        'idPersonagem' => $item->id,
                        'idGolpe' => 1
                    );
                    $core->insert('personagens_golpes', $campos_golpe);

                    // ✅ SET SUCCESS SESSION AND REDIRECT
                    $_SESSION['character_created'] = true;
                    $_SESSION['character_name'] = $nomeGuerreiro;
                    echo '<script>window.location.href = "'.BASE.'meus-personagens";</script>';
                    exit;
                    
                } else {
                    $_SESSION['error_message'] = 'Ocorreu um erro ao criar o personagem.';
                    echo '<script>window.location.href = window.location.href;</script>';
                    exit;
                }
            } else {
                $_SESSION['error_message'] = 'Nome contém palavras não permitidas.';
                echo '<script>window.location.href = window.location.href;</script>';
                exit;
            }
        } else {
            $_SESSION['error_message'] = 'Já existe um guerreiro com este nome.';
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Você já possui o número máximo de guerreiros.';
        echo '<script>window.location.href = window.location.href;</script>';
        exit;
    }
}

// ✅ SHOW ERROR NOTIFICATION IF EXISTS
$showErrorNotification = false;
$errorMessage = '';
if(isset($_SESSION['error_message'])) {
    $showErrorNotification = true;
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!-- ✅ ERROR NOTIFICATION -->
<?php if($showErrorNotification): ?>
<div id="notification-overlay" class="notification-overlay">
    <div class="notification-box error">
        <div class="notification-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="notification-content">
            <h3><i class="fas fa-times"></i> ATENÇÃO!</h3>
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
        
        <button class="notification-close" type="button" aria-label="Fechar">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
/* Dragon Ball Themed Notification */
.notification-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.notification-box {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 20px;
    padding: 40px;
    max-width: 550px;
    width: 90%;
    box-shadow: 0 0 50px rgba(255, 165, 0, 0.3), 0 20px 60px rgba(0, 0, 0, 0.8);
    position: relative;
    animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: 4px solid;
}

@keyframes zoomIn {
    from {
        transform: scale(0.5) rotate(-5deg);
        opacity: 0;
    }
    to {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.notification-box.error {
    border-image: linear-gradient(45deg, #ff4444, #ff6b6b, #ff4444) 1;
    box-shadow: 0 0 50px rgba(255, 68, 68, 0.4), 0 20px 60px rgba(0, 0, 0, 0.8);
}

.notification-icon {
    text-align: center;
    margin-bottom: 25px;
}

.notification-icon i {
    font-size: 70px;
    color: #ff6b35;
    text-shadow: 0 0 20px rgba(255, 107, 53, 0.8), 0 0 40px rgba(255, 107, 53, 0.5);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.notification-content {
    color: #FFF;
    text-align: center;
}

.notification-content h3 {
    font-size: 28px;
    margin: 0 0 20px 0;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #ff6b35;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(255, 107, 53, 0.3);
}

.notification-content p {
    font-size: 16px;
    margin: 15px 0;
    line-height: 1.8;
    color: #e0e0e0;
}

.notification-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: #FFF;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-close:hover {
    background: rgba(255, 68, 68, 0.8);
    border-color: #ff4444;
    transform: rotate(90deg) scale(1.1);
}
</style>

<script>
function closeNotification() {
    const overlay = document.getElementById('notification-overlay');
    if(overlay){
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease';
        setTimeout(function() {
            overlay.remove();
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notification-overlay');
    
    if(overlay){
        // Click background to close
        overlay.addEventListener('click', function(e) {
            if(e.target === this){
                closeNotification();
            }
        });
        
        // Click X button to close
        const closeBtn = overlay.querySelector('.notification-close');
        if(closeBtn){
            closeBtn.addEventListener('click', closeNotification);
        }
        
        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape'){
                closeNotification();
            }
        });
    }
});
</script>
<?php endif; ?>

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
                if (!is_object($personagem) || !method_exists($personagem, 'getList')) {
                    $personagem = new Personagens();
                }
                $personagem->getList();
                ?>
                
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
                <input type="hidden" name="criar" value="1"/>
                <button type="submit" id="criar" class="bts-form">Começar o Jogo <i class="fas fa-play"></i></button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnStep2 = document.querySelector('.btn-step-2');
    const nomeInput = document.querySelector('#nomeGuerreiro');
    
    if(btnStep2){
        btnStep2.addEventListener('click', function(e) {
            const nome = nomeInput ? nomeInput.value.trim() : '';
            const planetaChecked = document.querySelector('input[name="idPlaneta"]:checked');
            
            if(!nome){
                e.preventDefault();
                alert('⚠️ ATENÇÃO!\n\nVocê precisa digitar um nome para o guerreiro!');
                if(nomeInput) nomeInput.focus();
                return false;
            }
            
            if(!planetaChecked){
                e.preventDefault();
                alert('⚠️ ATENÇÃO!\n\nVocê precisa selecionar um planeta antes de continuar!');
                return false;
            }
        }, true);
    }
    
    // Validate on form submit
    const form = document.querySelector('#formPersonagem');
    if(form){
        form.addEventListener('submit', function(e) {
            const nome = nomeInput ? nomeInput.value.trim() : '';
            const planetaChecked = document.querySelector('input[name="idPlaneta"]:checked');
            
            if(!nome || !planetaChecked){
                e.preventDefault();
                
                if(!nome){
                    alert('⚠️ ATENÇÃO!\n\nVocê precisa digitar um nome para o guerreiro!');
                } else {
                    alert('⚠️ ATENÇÃO!\n\nVocê precisa selecionar um planeta antes de finalizar!');
                }
                return false;
            }
        }, true);
    }
    
    // Visual feedback for planet selection
    const planetButtons = document.querySelectorAll('input[name="idPlaneta"]');
    if(planetButtons.length > 0){
        planetButtons.forEach(function(btn) {
            btn.addEventListener('change', function() {
                planetButtons.forEach(b => {
                    const container = b.closest('.planeta-item') || b.parentElement;
                    if(container){
                        container.style.border = '2px solid transparent';
                        container.style.boxShadow = 'none';
                    }
                });
                
                if(this.checked){
                    const container = this.closest('.planeta-item') || this.parentElement;
                    if(container){
                        container.style.border = '3px solid #ffcc00';
                        container.style.borderRadius = '15px';
                        container.style.boxShadow = '0 0 20px rgba(255, 204, 0, 0.5)';
                    }
                }
            });
        });
    }
});
</script>
