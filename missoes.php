<?php
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
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
    
    if(isset($_SESSION['cacada']) || isset($_SESSION['missao'])){ 
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">Iniciar uma Missão</h2>

<p class="informativo">
    
</p>

<ul class="missoes-list">
   <?php $missoes->getList($_SESSION['PERSONAGEMID'], $user->vip); ?> 
</ul>