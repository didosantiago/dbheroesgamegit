<?php
// Initialize personagem object
if (!is_object($personagem) || !method_exists($personagem, 'getMeusPersonagens')) {
    $personagem = new Personagens();
}

// We'll check after getMeusPersonagens() is called
ob_start(); // Start output buffering
$personagem->getMeusPersonagens($user->id);
$charactersList = ob_get_clean(); // Get the output

// Check if there are characters (if output is empty or just whitespace)
$hasCharacters = (trim($charactersList) !== '');
?>

<?php
// ✅ HANDLE NOTIFICATIONS
$showSuccessNotification = false;
$showErrorNotification = false;
$notificationMessage = '';
$characterName = '';
$shouldRedirect = false;

// Check for error parameter
if(isset($_GET['error']) && $_GET['error'] === 'no_character') {
    $showErrorNotification = true;
    $notificationMessage = 'Você precisa selecionar um personagem antes de jogar!';
}

// Check if character was just created
if(isset($_SESSION['character_created']) && $_SESSION['character_created'] === true) {
    $showSuccessNotification = true;
    $characterName = isset($_SESSION['character_name']) ? $_SESSION['character_name'] : 'seu guerreiro';
    $notificationMessage = '<strong>' . htmlspecialchars($characterName) . '</strong> foi criado com sucesso!';
    unset($_SESSION['character_created']);
    unset($_SESSION['character_name']);
}

// Handle character selection (JOGAR button)
if(isset($_POST['jogar'])){
    if(!empty($_POST['idPersonagem'])){
        $idPersonagem = (int)$_POST['idPersonagem'];
        
        // Verify character belongs to user
        $core = new Core();
        $check = $core->getDados('usuarios_personagens', "WHERE id = {$idPersonagem} AND idUsuario = {$user->id}");
        
        if($check){
            $_SESSION['PERSONAGEMID'] = $idPersonagem;
            $showSuccessNotification = true;
            $characterName = htmlspecialchars($check->nome);
            $notificationMessage = 'Personagem <strong>' . $characterName . '</strong> selecionado com sucesso!';
            $shouldRedirect = true;
        } else {
            $showErrorNotification = true;
            $notificationMessage = 'Personagem inválido ou não encontrado!';
        }
    } else {
        $showErrorNotification = true;
        $notificationMessage = 'Você precisa selecionar um personagem antes de jogar!';
    }
}
?>


<div class="personagem-atual">
    <div class="foto-personagem">
        <img src="<?php echo BASE; ?>assets/guerreiro_blank.jpg" alt="Selecione seu Guerreiro" />
    </div>
    <div class="info">
        <h3>Não Selecionado</h3>
        <div class="atributos raca">
            <strong>Raça: </strong>
            Não Selecionado
        </div>
        <div class="atributos planeta">
            <strong>Planeta: </strong>
            Não Selecionado
        </div>
        <div class="atributos graduacao">
            <strong>Graduação: </strong>
            Não Selecionado
        </div>
        <div class="atributos nivel">
            <strong>Nível: </strong>
            Não Selecionado
        </div>
        <div class="atributos hp at-meter">
            <strong>HP </strong>
            <div class="meter animate red">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos mana at-meter">
            <strong>KI </strong>
            <div class="meter animate blue">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos energia at-meter">
            <strong>Energia </strong>
            <div class="meter animate">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
    </div>
</div>

<h2 class="title">Escolha um Guerreiro</h2>

<div class="lista-meus-personagens">
    <?php if(!$hasCharacters): ?>
        <!-- NO CHARACTERS MESSAGE -->
        <div class="empty-characters-state" style="text-align: center; padding: 60px 20px; background: rgba(0, 0, 0, 0.4); border-radius: 15px; margin: 20px auto; max-width: 600px;">
            <div style="font-size: 60px; margin-bottom: 20px;">⚠️</div>
            <p style="font-size: 22px; color: #ffcc00; margin-bottom: 30px; font-weight: bold; letter-spacing: 1px;">
                Você ainda não possui nenhum guerreiro!
            </p>
            <a href="<?php echo BASE; ?>criar-personagem" 
               style="display: inline-block; 
                      background: linear-gradient(135deg, #58e945ff 0%, #38c27dff 100%); 
                      color: #ffffff; 
                      padding: 18px 50px; 
                      border-radius: 30px; 
                      text-decoration: none; 
                      font-weight: bold; 
                      font-size: 20px; 
                      text-transform: uppercase; 
                      letter-spacing: 1.5px;
                      box-shadow: 0 5px 20px rgba(196, 228, 203, 0.5); 
                      transition: all 0.3s ease;
                      border: 2px solid rgba(255, 255, 255, 0.1);"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(208, 255, 176, 0.7)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(215, 245, 108, 0.5)';">
                🔥 CRIAR GUERREIRO
            </a>
        </div>
    <?php else: ?>
        <!-- EXISTING CHARACTER LIST -->
        <?php echo $charactersList; ?>
    <?php endif; ?>
</div>

<style>
.empty-characters-state a {
    cursor: pointer;
}

.empty-characters-state p {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {
    .empty-characters-state {
        padding: 40px 15px !important;
    }
    
    .empty-characters-state p {
        font-size: 18px !important;
    }
    
    .empty-characters-state a {
        padding: 15px 35px !important;
        font-size: 16px !important;
    }
}
</style>
