<?php
    // Set PHP timezone to Brazil (São Paulo)
    date_default_timezone_set('America/Sao_Paulo');
    // Arquivo de Configuração do Banco de Dados e Sistema
    // Updated for XAMPP 8.0.30 - 2025-11-01

    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_DATABASE', 'dbheroes');  // Changed from 'db' to 'dbheroes'
    define('DB_USER', 'root');
    define('DB_PASS', '');

    // Debug Mode
    define('DEBUG', 'false');
    
    // Timezone Configuration
    date_default_timezone_set('America/Sao_Paulo');
    
    // Base URL - IMPORTANT: Update this to match your setup
    define('BASE', 'http://localhost:8080/dbheroes/');  // Changed port and folder
?>