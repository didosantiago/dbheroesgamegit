<?php 
require_once 'core/CharacterRequired.php';
$errorMessages = array();
$successMessage = false;

if(isset($_POST['salvar']) && isset($_POST['golpes']) && !empty($_POST['golpes'])){

    $core = new Core();
    $batalha = new Batalha();
    $idPersonagem = (int)$_SESSION['PERSONAGEMID'];

    // ✅ DEBUG: Get character level - try different column names
    $personagemData = $core->getDados('usuarios_personagens', "WHERE id = $idPersonagem");
    $personagemInfo = $core->getDados('personagens', "WHERE id = " . $personagemData->idPersonagem);

    // ✅ FIX: Check which column name exists (nivel, level, or lvl)
    if(isset($personagemInfo->nivel)){
        $characterLevel = (int)$personagemInfo->nivel;
    } elseif(isset($personagemInfo->level)){
        $characterLevel = (int)$personagemInfo->level;
    } elseif(isset($personagemInfo->lvl)){
        $characterLevel = (int)$personagemInfo->lvl;
    } else {
        // Fallback: get from $personagem object
        $characterLevel = (int)$personagem->nivel;
    }

    // ✅ DEBUG: Log the character level (remove after testing)
    error_log("Character Level: " . $characterLevel);

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
            // ✅ DEBUG: Log comparison
            error_log("Golpe: " . $golpeData->nome . " | Required Level: " . $golpeData->level . " | Character Level: " . $characterLevel);

            // ✅ FIXED: Proper level comparison with explicit cast
            if((int)$characterLevel >= (int)$golpeData->level){
                // Character CAN learn this - add to valid list
                $validGolpes[] = $golpeId;
                error_log("✓ CAN learn: " . $golpeData->nome);
            } else {
                // Character CANNOT learn this yet - add to error list
                $invalidGolpes[] = $golpeData->nome . " (requer level " . $golpeData->level . ")";
                error_log("✗ CANNOT learn: " . $golpeData->nome);
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
                <!-- ✅ DEBUG: Show character level -->
                <p style="margin-top: 10px; font-size: 12px; color: #ffa726;">
                    Debug: Seu nível atual é <?php echo $characterLevel; ?>
                </p>
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



<style>
/* === DIVINE GOLD THEME FOR GOLPES === */
body.golpes { 
    background: radial-gradient(circle at center, #2b2000, #000); 
}

/* Grid Layout */
body.golpes .lista-golpes {
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
    gap: 20px; 
    padding: 20px; 
    list-style: none; 
    max-width: 1100px; 
    margin: 0 auto;
}

/* Card Styling */
body.golpes .lista-golpes li {
    display: flex;
    align-items: center;
    gap: 15px;
    text-align: left; /* Fix text alignment */
    background: linear-gradient(180deg, #1a1a1a 0%, #000 100%);
    border: 1px solid #665500;
    border-top: 3px solid #ffd700; /* Golden Top Bar */
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

/* Shine Effect on Hover */
body.golpes .lista-golpes li::before {
    content: ''; 
    position: absolute; 
    top: 0; 
    left: -100%; 
    width: 100%; 
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.2), transparent);
    transition: 0.5s;
    pointer-events: none;
}

body.golpes .lista-golpes li:hover::before { 
    left: 100%; 
}

body.golpes .lista-golpes li:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
    border-color: #ffd700;
}

/* Golpe Image */
body.golpes .lista-golpes li img {
    display: inline-block;
    vertical-align: middle;
    width: 60px;
    margin-right: 0px;
    border: 1px solid #ffd700; /* Gold border */
    border-radius: 4px;
    box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    transition: all 0.3s ease;
}

body.golpes .lista-golpes li:hover img {
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
    transform: scale(1.1);
}

/* Golpe Name/Title */
body.golpes .lista-golpes li h3,
body.golpes .lista-golpes li strong {
    background: -webkit-linear-gradient(#ffd700, #bf953f);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 800;
    font-size: 1.1em;
    margin-bottom: 10px;
}

/* Golpe Details */
body.golpes .lista-golpes li p,
body.golpes .lista-golpes li span {
    color: #ccc;
    font-size: 0.9em;
    margin: 5px 0;
}

/* Checkbox Styling */
body.golpes .lista-golpes li input[type="checkbox"] {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #ffd700;
    border-radius: 3px;
    background: #1a1a1a;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

body.golpes .lista-golpes li input[type="checkbox"]:checked {
    background: linear-gradient(to bottom, #ffd700, #b8860b);
    border-color: #fff;
    box-shadow: 0 0 15px #ffd700;
}

body.golpes .lista-golpes li input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #000;
    font-weight: bold;
    font-size: 14px;
}

/* Submit Button */
body.golpes button[type="submit"],
body.golpes .btn-salvar {
    background: linear-gradient(to bottom, #ffd700, #b8860b);
    color: #000; 
    font-weight: bold; 
    border: 1px solid #fff;
    box-shadow: 0 0 15px #b8860b;
    padding: 12px 30px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

body.golpes button[type="submit"]:hover,
body.golpes .btn-salvar:hover {
    background: linear-gradient(to bottom, #ffed4e, #ffd700);
    box-shadow: 0 0 25px #ffd700;
    transform: translateY(-2px);
}

/* Disabled/Locked Golpes */
body.golpes .lista-golpes li.bloqueado {
    opacity: 0.5;
    border-color: #333;
    border-top-color: #555;
}

body.golpes .lista-golpes li.bloqueado:hover {
    transform: none;
    box-shadow: none;
}
</style>
