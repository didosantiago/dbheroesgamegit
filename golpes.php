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
                $validGolpes[] = $golpeId;
            } else {
                $invalidGolpes[] = $golpeData->nome . " (requer level " . $golpeData->level . ")";
            }
        }
    }
    
    // Store error messages
    if(!empty($invalidGolpes)){
        $errorMessages = $invalidGolpes;
    }
    
    // Only proceed if we have valid golpes
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
        
        $successMessage = true;
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
                <h3>✅ Sucesso!</h3>
                <p>Seus golpes foram salvos com sucesso!</p>
            <?php else: ?>
                <h3>❌ Level Insuficiente!</h3>
                <p>Você não pode aprender os seguintes golpes:</p>
                <ul>
                    <?php foreach($errorMessages as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <button class="notification-close" onclick="closeNotification()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
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

<script>
function closeNotification() {
    const overlay = document.getElementById('notification-overlay');
    if(overlay){
        overlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(function() {
            overlay.remove();
        }, 300);
    }
}

// Auto-close success messages after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notification-overlay');
    if(overlay && overlay.querySelector('.notification-box.success')){
        setTimeout(function() {
            closeNotification();
        }, 3000);
    }
});

// Close on clicking overlay background
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('notification-overlay')){
        closeNotification();
    }
});
</script>

<style>
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>
