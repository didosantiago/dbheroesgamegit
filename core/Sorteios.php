<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sorteios
 *
 * @author Felipe Faciroli
 */
class Sorteios {
    public function getList($pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("adm_sorteios", "WHERE vencedor != '' ");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT s.*, p.foto "
             . "FROM adm_sorteios as s "
             . "INNER JOIN adm_sorteios_produto as p ON p.id = s.idProduto "
             . "WHERE s.vencedor != '' "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $dadosVencedor = $core->getDados('usuarios', "WHERE id = ".$value->vencedor);
            $bilheteVencedor = $core->getDados('adm_sorteios_participantes', "WHERE idUsuario = ".$value->vencedor);
            
            $row .= '<li>
                        <h3 class="titulo-sorteio">'.$value->titulo.' <span>'.$core->dataTimeBR($value->data_sorteio).'</span></h3>
                        <div class="premio">
                            <h2>Prêmio</h2>
                            <img src="'.BASE.'assets/sorteios/'.$value->foto.'" />
                            <div class="numero-participantes">
                                <h3>Nº de Participantes</h3>
                                <span>'.$this->getTotalParticipantes($value->id).'</span>
                            </div>
                        </div>
                        <div class="vencedor">
                            <img src="'.BASE.$dadosVencedor->foto.'" />
                            <div class="info">
                                <h2>'.$dadosVencedor->nome.'</h2>
                                <span class="data">Data de Cadastro: '.$core->dataBR($dadosVencedor->data_cadastro).'</span>
                                <h2>VENCEDOR</h2>
                            </div>
                            <div class="bilhete-gerado">
                                <h3>Bilhete Premiado</h3>
                                <span>'.$bilheteVencedor->bilhete.'</span>
                            </div>
                        </div>
                     </li>';
        }
        
        // Mostra Navegador da Paginação
        $row .= $pager->paginar($pc, $tp);
        
        echo $row;
    }
    
    public function geraBilhete(){
        $codigo = rand(1000, 9999);
        
        $verificador = substr(str_shuffle("AKSZ"), 0, $codigo);
        
        $bilhete = str_shuffle($verificador) . '-' . $codigo;
        
        $sql = "SELECT * FROM adm_sorteios_participantes WHERE bilhete = '$bilhete'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $codigo = rand(1000, 9999);
            $verificador = substr(str_shuffle("AKSZ"), 0, $codigo);
            $bilhete = str_shuffle($verificador) . '-' . $codigo;
        }
        
        return $bilhete;
    }
    
    public function validaParticipacao($idUsuario, $idSorteio){
        $sql = "SELECT * FROM adm_sorteios_participantes WHERE idUsuario = $idUsuario AND idSorteio = $idSorteio";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function existeSorteioAtivo(){
        $sql = "SELECT * FROM adm_sorteios WHERE status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function existeBilhete($idUsuario, $idSorteio){
        $sql = "SELECT * FROM adm_sorteios_participantes WHERE idUsuario = $idUsuario AND idSorteio = $idSorteio";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetch();
            return $item;
        } else {
            return null;
        }
    }
    
    public function existeVencedor($idSorteio){
        $sql = "SELECT * FROM adm_sorteios WHERE id = $idSorteio AND vencedor != ''";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetch();
            return $item->vencedor;
        } else {
            return null;
        }
    }
    
    public function getTotalParticipantes($idSorteio){
        $sql = "SELECT count(*) as total FROM adm_sorteios_participantes WHERE idSorteio = $idSorteio";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function souVencedor($idSorteio, $idUsuario){
        $sql = "SELECT * FROM adm_sorteios WHERE id = $idSorteio AND vencedor = $idUsuario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function existeSolicitacaoRetirada($idSorteio, $idUsuario){
        $sql = "SELECT * FROM adm_sorteios_retiradas WHERE idSorteio = $idSorteio AND idUsuario = $idUsuario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
}
