<?php
    session_start();
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    
    $core = new Core();
    
    if(isset($_POST['id']) && $_POST['id'] != ''){
        $idCacada = intval($_POST['id']);
        
        // Get hunt details
        $sql = "SELECT * FROM cacadas WHERE id = $idCacada AND concluida = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $cacada = $stmt->fetch();
            
            // Verify it belongs to current user's character
            if($cacada->idPersonagem == $_SESSION['PERSONAGEMID']){
                
                // IMPORTANT: Mark as BOTH cancelled AND completed (to prevent re-processing)
                $campos = array(
                    'cancelada' => 1,
                    'concluida' => 1  // ✅ Also mark completed to prevent re-running
                );
                $where = 'id = ' . $idCacada;
                
                if($core->update('cacadas', $campos, $where)){
                    // Clear session
                    unset($_SESSION['cacada']);
                    unset($_SESSION['cacada_id']);
                    
                    echo "success";
                } else {
                    echo "error_update_failed";
                }
            } else {
                echo "error_wrong_character";
            }
        } else {
            echo "error_not_found";
        }
    } else {
        echo "error_no_id";
    }
?>