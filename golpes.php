<?php 
$errorMessages = array();
$successMessage = false;

if(isset($_POST['salvar']) && !empty($_POST['golpes'])){
    
    $core = new Core();
    $batalha = new Batalha();
    $idPersonagem = (int)$_SESSION['PERSONAGEMID'];
    
    // Get character level
    $personagemData = $core->getDados('usuarios_personagens', "WHERE id = $idPersonagem");
    $personagemInfo = $core->getDados('personagens', "WHERE id = " . $personagemData->idPersonagem);
    $characterLevel = $personagemInfo->nivel;
    
    // ✅ Get currently learned golpes
    $sql = "SELECT idGolpe FROM personagens_golpes WHERE idPersonagem = $idPersonagem";
    $stmt = DB::prepare($sql);
    $stmt->execute();
    $learnedGolpes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Validate each selected golpe
    $validGolpes = array();
    $invalidGolpes = array();
    
    foreach($_POST['golpes'] as $golpeId){
        $golpeId = (int)$golpeId;
        
        // Get golpe requirements
        $golpeData = $core->getDados('ataques', "WHERE id = $golpeId");
        
        if($golpeData){
            // Check level requirement
            if($characterLevel >= $golpeData->level){
                // ✅ Character CAN learn this - add to valid list
                $validGolpes[] = $golpeId;
            } else {
                // ❌ Character CANNOT learn this yet - add to error list
                $invalidGolpes[] = $golpeData->nome . " (requer level " . $golpeData->level . ")";
            }
        }
    }
    
    // Show error messages only if there are invalid golpes
    if(!empty($invalidGolpes)){
        $errorMessages = $invalidGolpes;
    }
    
    // Save valid golpes even if there are some invalid ones
    if(!empty($validGolpes)){
        // Delete old golpes except Soco (ID 1)
        $core->delete('personagens_golpes', "idPersonagem = $idPersonagem AND idGolpe != 1");
        
        // Insert valid golpes
        foreach($validGolpes as $golpeId){
            if(!$batalha->getGolpeExiste($golpeId, $idPersonagem)){
                $campos = array(
                    'idGolpe' => $golpeId,
                    'idPersonagem' => $idPersonagem
                );
                $core->insert('personagens_golpes', $campos);
            }
        }
        
        // Only show success if ALL selected golpes were valid
        if(empty($invalidGolpes)){
            $successMessage = true;
        }
    }
}
?>


<!-- ✅ BEAUTIFUL NOTIFICATION CONTAINER -->
<?php if(!empty($errorMessages) || $successMessage): ?>
<div id="notification-overlay" class="notification-overlay">
    <div class="notification-box <?php echo $successMessage ? 'success' : 'error'; ?>">
        <div class="notification-icon">
            <?php if($successMessage): ?>
                <i class="fas fa-check-circle"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-triangle"></i>
            <?php endif; ?>
        </div>
        
        <div class="notification-content">
            <?php if($successMessage): ?>
                <h3><i class="fas fa-check"></i> SUCESSO!</h3>
                <p>Seus golpes foram salvos com sucesso!</p>
            <?php else: ?>
                <h3><i class="fas fa-times"></i> LEVEL INSUFICIENTE!</h3>
                <p>Você não pode aprender os seguintes golpes:</p>
                <ul>
                    <?php foreach($errorMessages as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <button class="notification-close" type="button" aria-label="Fechar">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

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
        // Auto-close success after 3 seconds
        if(overlay.querySelector('.notification-box.success')){
            setTimeout(closeNotification, 3000);
        }
        
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
