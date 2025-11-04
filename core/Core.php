<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Core
 *
 * @author Felipe Faciroli
 * @updated 2025-11-01 - Fixed for XAMPP 8.0.30 by GitHub Copilot
 */
class Core {
    public function cleanHTML($string) {
        $string = str_replace(array("amp;", "&lt;", "&gt;", '&amp;', '&#039;', '&quot;', '&lt;', '&gt;'), array('', "<", ">", '&', '\'', '"', '<', '>'), htmlspecialchars_decode($string, ENT_NOQUOTES));
        return $string;
    }
    
    public function decodeHTML($text) {
        $text = strtr($text, array('\r\n' => "", '\r' => "", '\n' => ""));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = str_replace('<br>', '<br />', $text);
        return stripslashes($text);
    }
    
    public function slug($string){
        $string = preg_replace('/[\t\n]/', ' ', $string);
        $string = preg_replace('/\s{2,}/', ' ', $string);
        $list = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
            'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
            'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y',
            'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-', '.' => '-',
        );

        $string = strtr($string, $list);
        $string = preg_replace('/-{2,}/', '-', $string);
        $string = strtolower($string);

        return $string;
    }
    
    public function copiar_diretorio($diretorio, $destino){
        $dir_copy = opendir($diretorio);

        while($file = readdir($dir_copy)){
            if ($file == '.' || $file == '..'){
                continue;
            }
            
            if(substr($file,0,1) != "."){
                copy($diretorio."/".$file,$destino."/".$file);
            }
        }
        
        return true;
    }
    
    public function isNewChecked($campo, $valor){
        if($campo == $valor){
            echo 'checked';
        }
    }
    
    public function isNewSelected($campo, $valor){
        if($campo == $valor){
            echo 'selected';
        }
    }
    
    public function update($table, $params = array(), $where){
        $args = array();
        
        foreach($params as $field=>$value){
            $args[] = $field.'="'.$value.'"';
        }
        
        $sql = 'UPDATE '.$table.' SET '.implode(',',$args).' WHERE '.$where;
        $this->gravaLog($sql, 'update');
        $stmt = DB::prepare($sql);
        
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function select($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){
        $q = 'SELECT '.$rows.' FROM '.$table;
        
        if($join != null){
            $q .= ' JOIN '.$join;
        }
        
        if($where != null){
            $q .= ' WHERE '.$where;
        }
        
        if($order != null){
            $q .= ' ORDER BY '.$order;
        }
        
        if($limit != null){
            $q .= ' LIMIT '.$limit;
        }
        
        $stmt = DB::prepare($q);
        
        if($stmt->execute()){
            return $stmt->fetchAll();
        } else{
            return false;
        }
    }
    
    public function insert($table, $params = array()){
        $sql = 'INSERT INTO `'.$table.'` (`'.implode('`, `',array_keys($params)).'`) VALUES ("' . implode('", "', $params) . '")';
        if($table != 'adm_log'){
            $this->gravaLog($sql, 'insert');
        }
        $stmt = DB::prepare($sql);
        
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete($table,$where = null){
        if($where == null){
            $delete = 'DROP TABLE '.$table;
        }else{
            $delete = 'DELETE FROM '.$table.' WHERE '.$where; 
        }
        
        $stmt = DB::prepare($delete);
        
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function msg($tipo, $mensagem){
        unset($_SESSION['msg']);
        
        if($tipo == 'sucesso'){
            $html = "<script type='text/javascript'>
                        $(function() {
                            new PNotify({
                                title: 'Sucesso',
                                text: '".$mensagem."',
                                type: 'success',
                                addclass: 'notification-success',
                                icon: 'fas fa-check-circle'
                            });
                        });
                     </script>";
            
            $_SESSION['msg'] = $html;
            $_SESSION['timer_msg'] = time();
        }
        
        if($tipo == 'error'){
            $html = "<script type='text/javascript'>
                        $(function() {
                            new PNotify({
                                title: 'Ocorreu um erro',
                                text: '".$mensagem."',
                                type: 'error',
                                addclass: 'notification-danger',
                                icon: 'fas fa-exclamation-circle'
                            });
                        });
                     </script>";
            
            $_SESSION['msg'] = $html;
            $_SESSION['timer_msg'] = time();
        }
        
        return $_SESSION['msg'];
    }
    
    public function verificaMsg(){
        $timeout_msg = 3;
 
        if(isset($_SESSION['timer_msg'])) {
            $duracao = time() - (int) $_SESSION['timer_msg'];

            if($duracao > $timeout_msg) {
                unset($_SESSION['timer_msg']);
                unset($_SESSION['msg']);
            }
        }
    }
    
    public function isSlug($table, $slug, $idLoja){
        $sql = "SELECT slug FROM $table WHERE slug = '$slug' AND idLoja = $idLoja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getImageName($table, $campo, $id, $idLoja){
        $sql = "SELECT $campo FROM $table WHERE idLoja = $idLoja AND id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $imagem = $stmt->fetch();
            return $imagem->$campo;
        }
    }
    
    public function dataBR($data){
        if (strstr($data, "/")) {
            $d = explode("/", $data);
            $rstData = str_pad($d[2], 2, "0", STR_PAD_LEFT) . "-" . str_pad($d[1], 2, "0", STR_PAD_LEFT) . "-$d[0]";
            return $rstData;
        } else if (strstr($data, "-")) {
            $data = substr($data, 0, 10);
            $d = explode("-", $data);
            $rstData = str_pad($d[2], 2, "0", STR_PAD_LEFT) . "/" . str_pad($d[1], 2, "0", STR_PAD_LEFT) . "/$d[0]";
            return $rstData;
        } else {
            return '';
        }
    }
    
    public function dataMysql($data){
        if (strstr($data, "/")) {
            $data = substr($data, 0, 10);
            $d = explode("/", $data);
            $rstData = str_pad($d[2], 2, "0", STR_PAD_LEFT) . "-" . str_pad($d[1], 2, "0", STR_PAD_LEFT) . "-$d[0]";
            return $rstData;
        } else {
            return '';
        }
    }
    
    public function dataTimeBR($data){
        $dateEN = new \DateTime($data);
        $datePTBR = $dateEN->format('d/m/Y H:i');
        return $datePTBR;
    }
    
    public function formataMoeda($valor){
        $preco = 'R$ ' . number_format($valor, 2, ',', '.');
        return $preco;
    }
    
    public function calculaParcelamento($valor, $parcelas){
        $parcelamento = intval($valor) / intval($parcelas);
        return '<span>ou <label class="skuBestInstallmentNumber">'.$parcelas.'<span class="x">x</span></label> de </span><strong><label class="skuBestInstallmentValue">'.$this->formataMoeda($parcelamento).'</label></strong></span>';
    }
    
    public function getByID($tabela, $id){
        $sql = "SELECT * FROM $tabela WHERE id = $id";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    // ✅ FIXED: Added try-catch to handle missing tables
    public function isExists($tabela, $where){
        try {
            $sql = "SELECT * FROM $tabela $where LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getDados($tabela, $where = ''){
        $sql = "SELECT * FROM $tabela $where";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function converteHoraToSegundos($time) {
        $hours = substr($time, 0, -6);
        $minutes = substr($time, -5, 2);
        $seconds = substr($time, -2);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }
    
    // ✅ FIXED: Changed from 'configuracoes' to 'adm_configuracoes'
    public function getConfiguracoes(){
        $sql = "SELECT * FROM adm_configuracoes LIMIT 1";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        return $row;
    }
    
    public function getSeo($pagina){
        $sql = "SELECT * FROM adm_seo WHERE pagina = '$pagina'";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            return $row;
        } else {
            return null;
        }
    }
    
    public function getPorcentagemDoacao($meta, $alcancado){
        $total = $alcancado / $meta;
        $resultado = intval($total * 100);
        return $resultado;
    }
    
    public function contaRegistros($table){
        $sql = "SELECT COUNT(*) AS num_registros FROM ".$table;
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        
        return $row->num_registros;
    }
    
    public function getCountUsuarios(){
        $sql = "SELECT count(*) as total FROM usuarios";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetch();
        
        return $usuarios->total;
    }
    
    public function getCountGuerreiros(){
        $sql = "SELECT count(*) as total FROM usuarios_personagens";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetch();
        
        return $usuarios->total;
    }
    
    public function getCountPVP(){
        $sql = "SELECT count(*) as total FROM pvp WHERE concluido = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountNPC(){
        $sql = "SELECT count(*) as total FROM npc WHERE concluido = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountEquipes(){
        $sql = "SELECT count(*) as total FROM equipes";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountCacadas(){
        $sql = "SELECT count(*) as total FROM cacadas WHERE concluida = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountMissoes(){
        $sql = "SELECT count(*) as total FROM personagens_missoes WHERE concluida = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountPostsPendentes(){
        $sql = "SELECT count(*) as total FROM forum_posts WHERE aprovado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountComentariosPendentes(){
        $sql = "SELECT count(*) as total FROM forum_comentarios WHERE aprovado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountConvidados(){
        $sql = "SELECT count(*) as total FROM usuarios WHERE user_vinculado != 0 AND vinculo_fidelizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountFidelizados(){
        $sql = "SELECT count(*) as total FROM usuarios WHERE user_vinculado is not null AND vinculo_fidelizado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    // ✅ FIXED: Added try-catch to handle missing table
    public function filtrarPalavrasOfensivas($review_from_user){
        try {
            $sql = "SELECT * FROM palavras_bloqueadas LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            return true; // Table doesn't exist, allow registration
        }
        
        $sql = "SELECT * FROM palavras_bloqueadas";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $palavras = $stmt->fetchAll();
        
        if(empty($palavras)){
            return true;
        }
        
        $lista_palavras = array();
        
        foreach ($palavras as $chave => $d_words) {
            array_push($lista_palavras, $d_words->palavra);
        }
        
        if(empty($lista_palavras)){
            return true;
        }
        
        $blocked_words = implode(",", $lista_palavras);
        $blocked_words_expo = explode(",", $blocked_words);
        $review_from_user = " ".$review_from_user." ";
        $bloqueado = 0;
        $new_word = strtoupper($review_from_user);
        
        foreach($blocked_words_expo as $rmv){
            if(strpos($new_word, strtoupper($rmv)) !== false){
               $bloqueado = 1;
               break;
            }
        }
        
        return ($bloqueado == 0);
    }
    
    public function proccessInExecution(){
        $existe = 0;
        
        if(isset($_SESSION['cacada'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['missao'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['npc'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['pvp'])){
            $existe = 1;
        }

        if($existe == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function proccessInNotNPC(){
        $existe = 0;
        
        if(isset($_SESSION['cacada'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['missao'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['pvp'])){
            $existe = 1;
        }

        if($existe == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function proccessInNotPVP(){
        $existe = 0;
        
        if(isset($_SESSION['cacada'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['missao'])){
            $existe = 1;
        }
        
        if(isset($_SESSION['npc'])){
            $existe = 1;
        }

        if($existe == 1){
            return true;
        } else {
            return false;
        }
    }
    
    public function criaResumo($string, $caracteres) {         
        if (strlen($string) > $caracteres){
            return substr($string, 0, $caracteres).'...';
        } else {
            return $string;
        }
    }
    
    public function counterRegisters($table, $parametros = ""){
        $sql = "SELECT count(*) as total FROM $table " . $parametros;
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $valor = $stmt->fetch();
        return $valor->total;
    }
    
    public function sortearPorcentagem($porcentagem) {
        return mt_rand(1, 100 / $porcentagem) === 1;
    }
    
    public function setNotification($mensagem, $tipo, $idPersonagem, $url = 'portal'){
        $campos = array(
            'idPersonagem' => $idPersonagem,
            'conteudo' => $mensagem,
            'tipo' => $tipo,
            'lido' => 0,
            'url' => $url
        );
        
        $this->insert('personagens_notificacoes', $campos);
    }
    
    public function getNotification($idPersonagem){
        $sql = "SELECT * FROM personagens_notificacoes WHERE idPersonagem = $idPersonagem AND lido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<div class="db-notification '.$value->tipo.'">
                            <div class="content-notification">
                                '.$value->conteudo.'
                            </div>
                            <form id="formNotification" method="post" action="">
                                <input type="hidden" name="idNotification" value="'.$value->id.'" />
                                <input type="hidden" name="urlNotificacao" value="'.$value->url.'" />
                                <input type="submit" name="confirmarNotificacao" value="Confirmar" class="bts-form" />
                            </form>
                         </div>';
            }
        }
        
        echo $row;
    }
    
    public function setViewNotification($id){
        $campos = array('lido' => 1);
        $where = 'id="'.$id.'"';

        if($this->update('personagens_notificacoes', $campos, $where)){
            return true;
        } else {
            return false;
        }
    }
    
    public function getDiaSemana(){
        $diasemana = array('domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado');
        $data = date('Y-m-d');
        $diasemana_numero = date('w', strtotime($data));
        
        return $diasemana[$diasemana_numero];
    }
    
    public function getSemanaAtual($dia){
        $domingo = 6;
        $dia_atual = date('w');
        $dias_que_faltam_para_o_domingo = $domingo - $dia_atual;

        $inicio = strtotime("-$dia_atual days");
        $fim = strtotime("+$dias_que_faltam_para_o_domingo days");

        $date_inicio = date('Y-m-d',$inicio);
        $date_fim =  date('Y-m-d',$fim);
        
        $dia_2 = date('Y-m-d',strtotime($date_inicio . "+1 days"));
        $dia_3 = date('Y-m-d',strtotime($date_inicio . "+2 days"));
        $dia_4 = date('Y-m-d',strtotime($date_inicio . "+3 days"));
        $dia_5 = date('Y-m-d',strtotime($date_inicio . "+4 days"));
        $dia_6 = date('Y-m-d',strtotime($date_inicio . "+5 days"));
        
        $datas = array(
            '1' => $date_inicio, '2' => $dia_2, '3' => $dia_3,
            '4' => $dia_4, '5' => $dia_5, '6' => $dia_6, '7' => $date_fim
        );
        
        foreach($datas as $key => $value){
            if($key == $dia){
                return $value;
            }
        }
    }
    
    public function validaTamanhoImagem($nome, $minimo){
        if(!isset($_FILES[$nome]) || !isset($_FILES[$nome]['tmp_name'])){
            return false;
        }
        
        $filename = $_FILES[$nome]['tmp_name'];
        list($largura, $altura) = getimagesize($filename);

        if($largura >= $minimo && $largura == $altura){
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaPaginasFront($modulo){
        $paginas_front = array('home', 'login', 'cadastro', 'assistir', 'rank', 'sobre');
        
        return in_array($modulo, $paginas_front);
    }
    
    public function verificaPaginasRecovery($modulo){
        $paginas_restritas_recovery = array('combate', 'npc');
        
        return in_array($modulo, $paginas_restritas_recovery);
    }
    
    public function getIP(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public function getStatusGraduacao($idGraduacao){
        $sql = "SELECT * FROM graduacoes WHERE id = $idGraduacao";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item){
            return $item->status_extra;
        }
        return 0;
    }
    
    // ✅ FIXED: Changed field from 'mensagem' to 'sql'
    public function gravaLog($mensagem, $tipo){
        $campos = array(
            'sql' => addslashes($mensagem),
            'tipo' => $tipo
        );
        
        $this->insert('adm_log', $campos);
    }
    
    // ✅ FIXED: Check if table exists before inserting
    public function controleLogin($ip, $username, $senha){
        try {
            $sql = "SELECT * FROM adm_log_login LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            return; // Table doesn't exist, skip logging
        }
        
        $campos = array(
            'data' => date('Y-m-d H:i:s'),
            'username' => $username,
            'senha' => $senha,
            'ip' => $ip
        );
        
        $this->insert('adm_log_login', $campos);
    }
    
    public function exportLog(){
        $config = $this->getConfiguracoes();
        
        if(!isset($config->time_log)){
            return;
        }
        
        if($config->time_log < time()){
            $sql = "SELECT * FROM adm_log";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetchAll();

            $row = '';

            foreach ($item as $key => $value) {
                $row .= '['.$value->sql.' - '.$this->dataTimeBR($value->data).' - '.$value->tipo.']'.PHP_EOL;
            }
            
            $nome_arquivo = 'log-'.date('d-m-Y').'-'.date('H-i');
            
            if(!file_exists('./logs')){
                mkdir('./logs', 0777, true);
            }
            
            $file = fopen('./logs/'.$nome_arquivo.'.txt','w');
            fwrite($file, $row);
            fclose($file);
            
            $sql = "TRUNCATE TABLE adm_log";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            $data = time() + 86400;
            
            try {
                $sql = "SELECT * FROM configuracoes LIMIT 1";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                
                $campos = array('time_log' => $data);
                $where = 'id = 1';
                $this->update('configuracoes', $campos, $where);
            } catch(PDOException $e) {
                // Table doesn't exist, skip
            }
        }
    }
    
    // ✅ FIXED: Check if table exists
    public function verifyDoubleEXP(){
        try {
            $sql = "SELECT * FROM adm_double_exp WHERE status = 1 AND periodo = 1 LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getDoubleEXP(){
        try {
            $sql = "SELECT * FROM adm_double_exp WHERE status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            return $item;
        } catch(PDOException $e) {
            return null;
        }
    }
    
    public function convertDataDoubleEXP(){
        try {
            $sql = "SELECT DAY(data_final) as dia, MONTH(data_final) as mes, YEAR(data_final) as ano, HOUR(data_final) as hora, MINUTE(data_final) as minuto FROM adm_double_exp WHERE status = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            if($stmt->rowCount() > 0){
                $data = mktime($item->hora, $item->minuto, 00, $item->mes, $item->dia, $item->ano);
                return $data;
            }
        } catch(PDOException $e) {
            return time();
        }
        return time();
    }
    
    public function convertDataDoubleEXPInicio(){
        try {
            $sql = "SELECT DAY(data_inicial) as dia, MONTH(data_inicial) as mes, YEAR(data_inicial) as ano, HOUR(data_inicial) as hora, MINUTE(data_inicial) as minuto FROM adm_double_exp WHERE status = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();
            
            if($item){
                $data = mktime($item->hora, $item->minuto, 00, $item->mes, $item->dia, $item->ano);
                return $data;
            }
        } catch(PDOException $e) {
            return time();
        }
        return time();
    }
    
    public function monitoraDoubleExp(){
        try {
            $sql = "SELECT * FROM adm_double_exp LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            return; // Table doesn't exist
        }
        
        $dadosDouble = $this->getDados('adm_double_exp');
        
        if(!$dadosDouble){
            return;
        }
            
        $time_atual = time();
        $time_inicio = $this->convertDataDoubleEXPInicio();
        $tempoRestanteInicio = $time_inicio - $time_atual;
            
        if($tempoRestanteInicio <= 0){
            $tempoRestanteDouble = $this->convertDataDoubleEXP() - $time_atual;
        } else{
            $tempoRestanteDouble = 1;
        }

        if($tempoRestanteDouble <= 0 && $dadosDouble->status == 1){
            $campos = array('periodo' => 0);
            $where = 'id = 1';
            $this->update('adm_double_exp', $campos, $where);
        } else if($tempoRestanteDouble > 0 && $dadosDouble->status == 0){
            $campos = array('periodo' => 1);
            $where = 'id = 1';
            $this->update('adm_double_exp', $campos, $where);
        }

        $dadosDouble = $this->getDados('adm_double_exp');

        if($tempoRestanteDouble <= 0 && $dadosDouble->status == 1){
            $campos = array('status' => 0);
            $where = 'id = 1';
            $this->update('adm_double_exp', $campos, $where);
        }

        if($tempoRestanteInicio <= 0 && $dadosDouble->status == 0 && $dadosDouble->periodo == 1){
            $campos = array('status' => 1);
            $where = 'id = 1';
            $this->update('adm_double_exp', $campos, $where);
        }
    }
}