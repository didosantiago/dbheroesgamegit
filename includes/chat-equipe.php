<?php
// ✅ SAFETY CHECK 1: Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ SAFETY CHECK 2: Check if user is logged in
$idPersonagemChat = null;
if(isset($_SESSION['PERSONAGEM']['ID']) && !empty($_SESSION['PERSONAGEM']['ID'])) {
    $idPersonagemChat = $_SESSION['PERSONAGEM']['ID'];
} else if(isset($_SESSION['PERSONAGEM_ID']) && !empty($_SESSION['PERSONAGEM_ID'])) {
    $idPersonagemChat = $_SESSION['PERSONAGEM_ID'];
} else if(isset($_SESSION['PERSONAGEMID']) && !empty($_SESSION['PERSONAGEMID'])) {
    $idPersonagemChat = $_SESSION['PERSONAGEMID'];
}

// ✅ SAFETY CHECK 3: User must be logged in
if(!$idPersonagemChat) {
    return;
}

// ✅ SAFETY CHECK 4: $idGet must exist and be valid
if(!isset($idGet) || empty($idGet) || $idGet <= 0) {
    return;
}

// ✅ SAFETY CHECK 5: User must be a member of the team
if(!$equipes->isMembro($idPersonagemChat, $idGet)) {
    return;
}

// Get chat data with safe query
$dadosChat = $core->getDados('equipes_chat', "WHERE idEquipe = ".intval($idGet));
$dados_equipe = $core->getDados('equipes', "WHERE id = ".intval($idGet));

// ✅ SAFETY CHECK 6: Chat and team must exist
if(empty($dadosChat) || empty($dados_equipe)) {
    return;
}
?>

<?php if($dadosChat->status == 1): ?>
<!-- CHAT LATERAL -->
<div id="chat-lateral-equipe" class="chat-sidebar">
    <div class="chat-toggle" onclick="toggleChatEquipe()">
        <i class="fas fa-comments"></i>
        <span>Chat da Equipe</span>
    </div>
    
    <div class="chat-content">
        <div class="chat-header-sidebar">
            <h4><i class="fas fa-trophy"></i>&nbsp;&nbsp;<?php echo $dados_equipe->nome; ?></h4>
            <button class="close-chat" onclick="toggleChatEquipe()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chat-messages-sidebar" id="mensagens-chat-equipe">
            <!-- Messages loaded via AJAX -->
        </div>
        
        <form id="formChatEquipe" class="chat-form-sidebar">
            <input 
                type="text" 
                id="mensagemEquipe" 
                name="mensagem" 
                placeholder="Digite sua mensagem..." 
                autocomplete="off"
                maxlength="255"
                required>
            <button type="submit">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script type="text/javascript">
// Toggle chat sidebar
function toggleChatEquipe() {
    const chat = document.getElementById('chat-lateral-equipe');
    chat.classList.toggle('active');
}

// ✅ STORE idEquipe in a global variable for AJAX use
var idEquipeGlobal = <?php echo isset($idGet) && !empty($idGet) ? intval($idGet) : 0; ?>;

// Debug - check if we have valid ID
console.log('Team ID for chat:', idEquipeGlobal);

if(idEquipeGlobal <= 0) {
    console.error('Invalid team ID - chat will not work');
}


$(document).ready(function(){
    // Verify we have a valid team ID
    if(!idEquipeGlobal || idEquipeGlobal <= 0) {
        console.error('Invalid team ID');
        return;
    }
    
    // ✅ FIX: Track form submissions to prevent reload conflicts
    var isSubmitting = false;
    var chatInterval = null;
    
    // ✅ FIX: Detect when ANY form (except chat) is being submitted
    $('form').on('submit', function(e) {
        if($(this).attr('id') !== 'formChatEquipe') {
            isSubmitting = true;
            if(chatInterval) {
                clearInterval(chatInterval);
            }
        }
    });
    
    // Load chat initially
    carregarChatEquipe();
    
    // Auto-refresh every 5 seconds
    chatInterval = setInterval(function(){
        if(!isSubmitting) {
            carregarChatEquipe();
        }
    }, 5000);
    
    function carregarChatEquipe(){
        if(isSubmitting) return;
        
        $.ajax({
            url: '<?php echo BASE; ?>ajax/ajaxChatEquipe.php',
            type: 'POST',
            data: {
                tipo: 'listar',
                idEquipe: idEquipeGlobal  // ✅ Use the global variable
            },
            success: function(data){
                $('#mensagens-chat-equipe').html(data);
                // Auto scroll to bottom
                var chatBox = document.getElementById('mensagens-chat-equipe');
                if(chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            },
            error: function(xhr, status, error){
                console.error('Erro ao carregar chat:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }
    
    // Send message
    $('#formChatEquipe').on('submit', function(e){
        e.preventDefault();
        
        var mensagem = $('#mensagemEquipe').val().trim();
        
        if(mensagem === ''){
            return false;
        }
        
        $.ajax({
            url: '<?php echo BASE; ?>ajax/ajaxChatEquipe.php',
            type: 'POST',
            data: {
                tipo: 'enviar',
                idEquipe: idEquipeGlobal,  // ✅ Use the global variable
                mensagem: mensagem
            },
            success: function(response){
                $('#mensagemEquipe').val('');
                carregarChatEquipe();
            },
            error: function(xhr, status, error){
                console.error('Erro ao enviar mensagem:', error);
                console.error('Response:', xhr.responseText);
            }
        });
        
        return false;
    });
});
</script>


<style>
/* CHAT SIDEBAR STYLES */
.chat-sidebar {
    position: fixed;
    left: -350px;
    top: 0;
    width: 350px;
    height: 100vh;
    background: linear-gradient(180deg, rgba(20,20,40,0.98) 0%, rgba(10,10,25,0.98) 100%);
    box-shadow: 2px 0 15px rgba(0,0,0,0.5);
    transition: left 0.3s ease;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.chat-sidebar.active {
    left: 0;
}

.chat-toggle {
    position: absolute;
    right: -50px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 10px;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    color: #fff;
    font-weight: bold;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}

.chat-toggle:hover {
    right: -52px;
    box-shadow: 3px 3px 15px rgba(102,126,234,0.5);
}

.chat-toggle i {
    margin-right: 5px;
}

.chat-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    margin: -50px 0px 0px 0px;
    padding: 20px;
}

.chat-header-sidebar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 5px;
    border-bottom: 2px solid rgba(102,126,234,0.3);
    margin-bottom: 15px;
}

.chat-header-sidebar h4 {
    color: #73ff50ff;
    margin: 80px 150px 10px 0px;
    font-size: 18px;
}

.close-chat {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    padding: 5px;
    transition: all 0.3s ease;
}

.close-chat:hover {
    color: #ff6b6b;
    transform: rotate(90deg);
}

.chat-messages-sidebar {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    background: rgba(0,0,0,0.2);
    border-radius: 8px;
    margin-bottom: 15px;
}

.chat-form-sidebar {
    display: flex;
    gap: 10px;
}

.chat-form-sidebar input {
    flex: 1;
    padding: 12px 15px;
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
}

.chat-form-sidebar input:focus {
    outline: none;
    border-color: #667eea;
}

.chat-form-sidebar button {
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.chat-form-sidebar button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102,126,234,0.4);
}

/* Message styling */
.mensagem-chat {
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(102,126,234,0.1);
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.mensagem-chat .autor {
    font-weight: bold;
    color: #8bea66ff;
    margin-bottom: 5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
}

.mensagem-chat .hora {
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    margin-left: 10px;
}

.mensagem-chat .texto {
    color: #fff;
    word-wrap: break-word;
}

/* 👑 Crown icon - force inline and small */
.mensagem-chat .fas.fa-crown {
    font-size: 10px !important;
    display: inline-block !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
    vertical-align: middle !important;
}
</style>
<?php endif; ?>
