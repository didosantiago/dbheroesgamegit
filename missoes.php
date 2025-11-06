<?php
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
    
    // Display any messages (success/error)
    if(isset($_SESSION['msg']) && $_SESSION['msg'] != ''){
        echo '<div class="message-display" style="padding: 15px; margin: 10px 0; border-radius: 5px; text-align: center;">';
        echo $_SESSION['msg'];
        echo '</div>';
        $_SESSION['msg'] = ''; // Clear message
    }
    
    if(isset($_POST['iniciar'])){
        if(!isset($_SESSION['missao'])){
            if(!isset($_SESSION['cacada']) || !isset($_SESSION['missao'])){          
                if(isset($_SESSION['PERSONAGEMID'])){
                    if(addslashes($_POST['tempo']) == 24){
                        if($user->vip == 0){
                            $core->msg('error', 'Missões de 24 horas são restritas a Jogadores VIP.');
                        } else {
                            $personagem->getGuerreiro($_SESSION['PERSONAGEMID']);
                            $missoes->iniciaMissao($user->id, $_POST, $_SESSION['PERSONAGEMID'], $user->vip);
                        }
                    } else {
                        $personagem->getGuerreiro($_SESSION['PERSONAGEMID']);
                        $missoes->iniciaMissao($user->id, $_POST, $_SESSION['PERSONAGEMID'], $user->vip);
                    }
                }
            }
        }
    }
    
    // Only redirect to portal if there's an ACTIVE mission, not completed ones
    if(isset($_SESSION['cacada'])){ 
        header('Location: '.BASE.'portal');
    }

    // Check if there's an active mission (not completed)
    if(isset($_SESSION['missao'])){
        // Check if mission is still active
        $sql_check = "SELECT * FROM missoes WHERE idPersonagem = '".$_SESSION['PERSONAGEMID']."' AND status = 'ativa'";
        $stmt_check = DB::prepare($sql_check);
        $stmt_check->execute();
        
        if($stmt_check->rowCount() > 0){
            // Active mission exists, redirect to portal
            header('Location: '.BASE.'portal');
        } else {
            // Mission completed, stay on missions page to show popup
            unset($_SESSION['missao']);
            unset($_SESSION['missao_id']);
        }
    }

?>

<h2 class="title">Iniciar uma Missão</h2>

<p class="informativo">
    
</p>

<ul class="missoes-list">
   <?php $missoes->getList($_SESSION['PERSONAGEMID'], $user->vip); ?> 
</ul>

<style>
.content-img {
    position: relative;
    display: inline-block;
}

.custom-tooltip {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
    color: #ffffff;
    font-size: 16px;
    font-weight: 500;
    padding: 15px 20px;
    border-radius: 10px;
    border: 2px solid #555;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    z-index: 9999;
    min-width: 300px;
    max-width: 400px;
    text-align: center;
    line-height: 1.6;
    transition: all 0.3s ease;
    pointer-events: none;
}

.custom-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid #2a2a2a;
}

.mission-tooltip-img:hover + .custom-tooltip {
    visibility: visible;
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltipImages = document.querySelectorAll('.mission-tooltip-img');
    
    tooltipImages.forEach(function(img) {
        const tooltip = img.nextElementSibling;
        const tooltipText = img.getAttribute('data-tooltip');
        tooltip.innerHTML = tooltipText.replace(/\|/g, '<br>');
        
        img.addEventListener('mouseenter', function() {
            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        });
        
        img.addEventListener('mouseleave', function() {
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        });
    });
});
</script>


