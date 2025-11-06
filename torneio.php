<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
?>

<script type="text/javascript">
    var nav = $('.guerreiros-list li:nth-child(4)');
    if (nav.length) {
      var contentNav = nav.offset().top;
      $('html, body').animate({scrollTop: contentNav}, 'slow');
    }
</script>

<div class="arena">
    <h2 class="title">Torneio de Artes Marciais (NPC)</h2>
    
    <ul class="guerreiros-list">
        <?php 
            $torneio->getList($personagem->nivel); 
        ?> 
    </ul>
    <!-- Energy Warning Popup -->
<div id="energiaPopup" class="modal-overlay" style="display: none;">
    <div class="modal-content energia-modal">
        <div class="modal-header">
            <i class="fas fa-battery-empty" style="color: #f44336; font-size: 48px;"></i>
            <h2>Energia Insuficiente!</h2>
        </div>
        <div class="modal-body">
            <p>Você não tem energia suficiente para batalhar.</p>
            <p><strong>Energia necessária:</strong> 10 pontos</p>
            <p><strong>Sua energia atual:</strong> <span id="energiaAtual"><?php echo $energia_restante; ?></span> / <?php echo $personagem->energia; ?></p>
            
            <div class="info-rest">
                <i class="fas fa-bed"></i>
                <p><strong>Como recuperar energia:</strong></p>
                <ul>
                    <li>Descanse e aguarde um tempo</li>
                    <li>A energia se recupera automaticamente</li>
                    <li>Cada hora recupera <strong>10 pontos</strong> de energia</li>
                </ul>
            </div>
            
            <div class="tempo-recuperacao">
                <i class="fas fa-clock"></i>
                <p>Tempo estimado para energia completa: <strong><?php 
                    $energia_faltante = $personagem->energia - $energia_restante;
                    $horas = ceil($energia_faltante / 10);
                    echo $horas . ' hora' . ($horas > 1 ? 's' : '');
                ?></strong></p>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeEnergiaPopup()" class="bts-form btn-primary">Entendi</button>
        </div>
    </div>
</div>

<script>
function showEnergiaPopup() {
    document.getElementById('energiaPopup').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEnergiaPopup() {
    document.getElementById('energiaPopup').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close popup when clicking outside
document.getElementById('energiaPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEnergiaPopup();
    }
});

// Close with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEnergiaPopup();
    }
});
</script>

<style>
/* Small font for "Sem Energia" button */
.btn-text-small {
    font-size: 11px;
    line-height: 1.2;
    display: inline-block;
    vertical-align: middle;
}

.btn-sem-energia {
    background: #f44336 !important;
    cursor: pointer !important;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 8px 12px !important;
}

.btn-sem-energia:hover {
    background: #d32f2f !important;
    transform: scale(1.05);
}

.btn-sem-energia i {
    font-size: 16px;
}

/* Modal Overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Modal Content */
.modal-content {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 3px solid #f44336;
    border-radius: 15px;
    padding: 30px;
    max-width: 550px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(244, 67, 54, 0.5);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Modal Header */
.modal-header {
    text-align: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    color: #f44336;
    margin-top: 15px;
    font-size: 28px;
    text-shadow: 0 0 10px rgba(244, 67, 54, 0.5);
}

/* Modal Body */
.modal-body {
    color: #fff;
    text-align: center;
    margin-bottom: 25px;
}

.modal-body p {
    margin: 10px 0;
    font-size: 16px;
}

.modal-body strong {
    color: #ffc107;
}

.info-rest {
    margin-top: 20px;
    padding: 20px;
    background: rgba(76, 175, 80, 0.1);
    border-left: 4px solid #4caf50;
    border-radius: 5px;
    text-align: left;
}

.info-rest i {
    color: #4caf50;
    font-size: 24px;
    margin-bottom: 10px;
}

.info-rest ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.info-rest li {
    padding: 5px 0;
    padding-left: 25px;
    position: relative;
}

.info-rest li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #4caf50;
    font-weight: bold;
}

.tempo-recuperacao {
    margin-top: 15px;
    padding: 15px;
    background: rgba(255, 193, 7, 0.1);
    border-left: 4px solid #ffc107;
    border-radius: 5px;
}

.tempo-recuperacao i {
    color: #ffc107*

</div>
