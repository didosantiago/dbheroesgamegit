<?php
        
class DB{
    
    private static $instance;
    
    public static function getInstance(){

        if(!isset(self::$instance)){

            try {
                self::$instance = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
                self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                
                // ✅ SET MYSQL TIMEZONE TO MATCH PHP (Brazil - São Paulo = UTC-3)
                self::$instance->exec("SET time_zone = '-03:00'");
                
            } catch (PDOException $e) {
                echo $e->getMessage();
            }

        }

        return self::$instance;
    }

    public static function prepare($sql){
        return self::getInstance()->prepare($sql);
    }
            
    public static function tabelaExiste($tabela){
        $sql = "SHOW TABLES LIKE '$tabela'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function bdExiste($banco){
        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$banco'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function itemExiste($table, $id, $loja){
        $sql = "SELECT * FROM $table WHERE id = '$id' AND idLoja = $loja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function itemExistePlaceholder($table, $id, $placeholder){
        $sql = "SELECT * FROM $table WHERE id = '$id' AND idPlaceholder = $placeholder";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function itemExisteByName($table, $campo, $nome, $loja){
        $sql = "SELECT * FROM $table WHERE $campo = '$nome' AND idLoja = $loja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function itemExisteByQuery($table, $query){
        $sql = "SELECT * FROM $table ".$query;
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function itemExisteByTitulo($table, $nome, $loja){
        $sql = "SELECT * FROM $table WHERE titulo = '$nome' AND idLoja = $loja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function slugItemExiste($table, $slug, $loja){
        $sql = "SELECT * FROM $table WHERE slug = '$slug' AND idLoja = $loja ";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public static function campoExiste($nome_banco, $tabela, $campo, $tipo, $aux){
                
        $sql = "SELECT TABLE_SCHEMA AS DB, TABLE_NAME AS Tabela, COLUMN_NAME AS coluna
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                TABLE_SCHEMA = '$nome_banco'
                AND COLUMN_NAME = '$campo'";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() <= 0){
            $insere = "ALTER TABLE ".$nome_banco.".".$tabela." ADD ".$campo." ".$tipo." ".$aux.";";
        
            $stmt = DB::prepare($insere);
            $stmt->execute();
            
            return true;
        }
    }
    
    public static function insert($table = null, $data) {
        if ($table === null or empty($data) or ! is_array($data)) {
            return false;
        }
        
        $q = "INSERT INTO `" . $table . "` ";
        $v = '';
        $k = '';

        foreach ($data as $key => $val) :
            $k .= "`$key`, ";
            if (strtolower($val) == 'null')
                $v .= "NULL, ";
            elseif (strtolower($val) == 'now()')
                $v .= "NOW(), ";
            elseif (strtolower($val) == 'tzdate')
                $v .= "DATE_ADD(NOW(),INTERVAL " . $core->timezone . " HOUR), ";
            else
                $v .= "'" . $val . "', ";
        endforeach;

        $q .= "(" . rtrim($k, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";
        
        $stmt = DB::prepare($q);
        
        if ($stmt->execute()) {
            return true;
        } else
            return false;
    }
}