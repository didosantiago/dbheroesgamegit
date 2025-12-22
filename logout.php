<?php
    require_once './init.php';
    
    // Store username before clearing session (for welcome back message)
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    
    // Delete monitoring before destroying session
    if(isset($_SERVER['REMOTE_ADDR'])){
        $user->deleteMonitoramento($_SERVER['REMOTE_ADDR']);
    }
    
    // Update user status to offline in database
    if(isset($_SESSION['user_id'])){
        $campos = array('online' => 0);
        $where = 'id = "'.$_SESSION['user_id'].'"';
        $core->update('usuarios', $campos, $where);
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Start new session for logout message
    session_start();
    $_SESSION['logout_success'] = true;
    if($username){
        $_SESSION['logout_username'] = $username;
    }
    
    // Redirect to home page (public landing page)
    header('Location: '.BASE.'home?logout=success');
    exit;
?>
