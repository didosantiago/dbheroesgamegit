<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $filePaths = $rootPath . "/core/ifsnop/Mysqldump.php";
        
    if (file_exists($filePaths)){
        require_once $filePaths;
    }
    
    // Incluindo a classe que criamos
    include_once $_SERVER['DOCUMENT_ROOT']."/core/ifsnop/Backup.php";

    // Como a geração do backup pode ser demorada, retiramos
    // o limite de execução do script
    set_time_limit(0);

    // Utilizando a classe para gerar um backup na pasta 'backups'
    // e manter os últimos dez arquivos
    $backup = new \Ifsnop\Mysqldump\Backup('dump', 10);
    $backup->setDatabase('162.241.2.194', 'dbheroes_game', 'dbheroes_dev', 'dbheroes2018');
    $backup->generate();
?>